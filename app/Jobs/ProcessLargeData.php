<?php

namespace App\Jobs;

use App\Classes\MyHelpers;
use App\Models\Branch;
use App\Models\Company;
use App\Models\GroupAttendance;
use App\Models\InAttendance;
use App\Models\JobLevel;
use App\Models\JobPosition;
use App\Models\Organization;
use App\Models\OutAttendance;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProcessLargeData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $data;
    public $tries = 5; // Set jumlah maksimal percobaan
    public $timeout = 300; // Set waktu timeout dalam detik
    /**
     * Create a new job instance.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $helper = new MyHelpers();
        // Proses data besar dalam chunk
        collect($this->data)->chunk(100)->each(function ($chunk) use ($helper) {
            $validator = Validator::make($chunk->toArray(), [
                'data.*.id' => 'required|integer',
                'data.*.emp_code' => 'required|string|max:255',
                'data.*.first_name' => 'required|string|max:255',
                'data.*.last_name' => 'nullable|string|max:255',
                'data.*.department' => 'required|string|max:255',
                'data.*.position' => 'required|string|max:255',
                'data.*.punch_time' => 'required|date_format:Y-m-d H:i:s',
                'data.*.punch_state' => 'required|string|max:1'
            ]);

            if ($validator->fails()) {
                Log::error('Validation failed for chunk', ['errors' => $validator->errors()]);
                return $validator->errors();
            }
            foreach ($chunk->toArray() as $k) {
                $date = Carbon::parse($k['punch_time']);
                $time = $date->format('H:i:s');
                // VALIDASI USER::STARTED
                $user = User::where('nik', $k['emp_code'])->first();
                $dept = Organization::firstOrCreate(
                    [
                        "name"=>$k['department'],
                        "description"=>$k['department'],
                    ],
                    [
                        "name"=>$k['department'],
                        "description"=>$k['department'],
                    ],
                );
                $position = JobPosition::firstOrCreate(
                    [
                        "name"=>$k['position'],
                        "description"=>$k['position'],
                    ],
                    [
                        "name"=>$k['position'],
                        "description"=>$k['position'],
                    ],
                );
                if (is_null($user)) {
                    // kalo kosong, maka siapin usernya
                    $user = User::create([
                        'name' => $k['first_name'],
                        'nik' => $k['emp_code'],
                        'email' => Str::snake($k['first_name']).'_'.$k['emp_code'].'@sinergiabadisentosa.com',
                        'password' => Hash::make($k['emp_code'])
                    ]);
                    $user->assignRole('panel_user');
                    $company=Company::firstOrCreate(
                        ['name'=>'PT. SINERGI ABADI SENTOSA'],
                        [
                            'name'=>'PT. SINERGI ABADI SENTOSA',
                            'latitude'=>'-6.1749639',
                            'longitude'=>'106.59857115',
                            'full_address'=>'Jl. Prabu Kian Santang No.169A, RT.001/RW.004, Sangiang Jaya, Kec. Periuk, Kota Tangerang, Banten 15132',
                        ],
                    );
                    $branch=Branch::firstOrCreate(
                        ['name'=>'Head Office'],
                        [
                            'name'=>'Head Office',
                            'latitude'=>'-6.1749639',
                            'longitude'=>'106.59857115',
                            'full_address'=>'Jl. Prabu Kian Santang No.169A, RT.001/RW.004, Sangiang Jaya, Kec. Periuk, Kota Tangerang, Banten 15132',
                        ],
                    );
                    $lvl = JobLevel::find(7)->first();
                    $approval = User::find(1)->first();
                    $user->employe()->create([
                        'organization_id'=>$dept->id,
                        'job_position_id'=>$position->id,
                        'job_level_id'=>$lvl->id,
                        'company_id'=>$company->id,
                        'branch_id'=>$branch->id,
                        'approval_line'=>$approval->id,
                        'approval_manager'=>$approval->id,
                        'status'=>'contract',
                        'join_date'=>date('Y-m-d'),
                        'sign_date'=>date('Y-m-d'),
                    ]);
                }
                // VALIDASI USER::ENDED
                // VALIDASI USER-GROUP-ABSEN::STARTED
                if (count($user->group_attendance) < 1) {
                    $group = $helper->validateAndFindGroupAttendance($k['department'], $k['position'], $time, (int)$k['punch_state']);
                    $groupPresence = GroupAttendance::where('name', $group)->first();
                    $groupPresence->user()->attach([$user->id]);
                }
                // VALIDASI USER-GROUP-ABSEN::ENDED
                // VALIDASI USER-GROUP-SCHEDULE-ABSEN::STARTED
                $findGroup = GroupAttendance::whereHas('user', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })->first();
                $groupPresence = GroupAttendance::find($findGroup->id)->first();
                $jadwal = $groupPresence->schedule_attendance;
                if (count($groupPresence->schedule_attendance) < 1) {
                    $shift = DB::table('time_attendances')
                    ->select('*', DB::raw('ABS(TIME_TO_SEC(TIMEDIFF("'.$time.'", `in`))) AS time_difference'))
                    ->orderBy('time_difference', 'asc')
                    ->first();
                    $groupPresence->schedule_attendance()->create([
                        "time_attendance_id"=>$shift->id,
                        "date"=>$date->format('Y-m-d'),
                        "status"=>'unpresent'
                    ]);
                    $groupPresence = GroupAttendance::find($findGroup->id)->first();
                    $jadwal = $groupPresence->schedule_attendance;
                }
                // VALIDASI USER-GROUP-SCHEDULE-ABSEN::ENDED
                $validate = $helper->cekStatusTelatAbsen($jadwal[0]->group_attendance_id, $time, (int)$k['punch_state'] < 1 ? 'in' : 'out');
                $status = $validate['status'];
                if ((int)$k['punch_state'] < 1) {
                    // cek jam masuk simpan data pertama kali saja
                    $cek = InAttendance::where([
                        'nik' => $k['emp_code'],
                        'date' => $date->format('Y-m-d'),
                        'schedule_group_attendances_id' => $jadwal[0]->id,
                    ])->count();
                    if ($cek < 1) {
                        InAttendance::create([
                            'nik' => $k['emp_code'],
                            'schedule_group_attendances_id' => $jadwal[0]->id,
                            'lat' => (double)'-6.1749639',
                            'lng' => (double)'106.598571,15',
                            'date' => $date->format('Y-m-d'),
                            'time' => $time,
                            'status' => $status,
                        ]);
                    }
                }else{
                    $q = OutAttendance::where([
                        'nik' => $k['emp_code'],
                        'date' => $date->format('Y-m-d'),
                        'schedule_group_attendances_id' => $jadwal[0]->id,
                    ])->first();
                    if ($q) {
                        $cek = OutAttendance::whereHas('attendance', function ($query) use ($k, $date) {
                            $query
                            ->where('nik', $k['emp_code'])
                            ->where('date', $date->format('Y-m-d'));
                        })->count();
                        if ($cek < 1) {
                            $q->attendance()->create([
                                'nik' => $k['emp_code'],
                                'schedule_group_attendances_id' => $jadwal[0]->id,
                                'lat' => (double)'-6.1749639',
                                'lng' => (double)'106.598571,15',
                                'date' => $date->format('Y-m-d'),
                                'time' => $time,
                                'status' => $status,
                            ]);
                        }
                    }
                }
            }
        });
    }
    public function failed(\Exception $exception)
    {
        // Tangani kegagalan job di sini
        Log::error('Job failed', ['exception' => $exception]);
    }
}
