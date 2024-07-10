<?php

namespace App\Repositories;

use App\Classes\MyHelpers;
use Illuminate\Support\Str;
use App\Interfaces\AttendanceInterface;
use App\Models\Branch;
use App\Models\Company;
use App\Models\GroupAttendance;
use App\Models\GroupUsersAttendance;
use App\Models\InAttendance;
use App\Models\JobLevel;
use App\Models\JobPosition;
use App\Models\Organization;
use App\Models\ScheduleGroupAttendance;
use App\Models\TimeAttendance;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AttendanceRepository implements AttendanceInterface
{
    protected $model;
    /**
     * Create a new class instance.
     */
    public function __construct(InAttendance $model)
    {
        $this->model = $model;
    }

    /**
     * Mendapatkan semua data absensi.
     */
    public function index($perPage, $page, $search)
    {
        $query = $this->model::query();
        if ($search) {
            $query->where('nik', 'like', "%{$search}%")
                  ->orWhere('date', 'like', "%{$search}%")
                  ->orWhere('time', 'like', "%{$search}%");
        }
        $data = $query->paginate($perPage, ['*'], 'page', $page);
        return $data;
    }

    /**
     * Mendapatkan data absensi berdasarkan ID.
     */
    public function getById($id)
    {
        return $this->model::with('user', 'schedule', 'attendance')->findOrFail($id);
    }

    /**
     * Menyimpan data absensi baru.
     */
    public function store($data)
    {
        $myHelpers = new MyHelpers();
        $validate = $myHelpers->cekStatusTelatAbsen($data['schedule_group_attendances_id'], $data['time'], $data['flag']);
        $status = $validate['status'];
        if ($data['flag'] === 'in') {
            $q = $this->model::updateOrCreate(
                [
                    'nik' => $data['nik'],
                    'date' => $data['date'],
                    'schedule_group_attendances_id' => $data['schedule_group_attendances_id'],
                ],
                [
                    'nik' => $data['nik'],
                    'schedule_group_attendances_id' => $data['schedule_group_attendances_id'],
                    'lat' => $data['lat'],
                    'lng' => $data['lng'],
                    'date' => $data['date'],
                    'time' => $data['time'],
                    // 'photo' => $data['photo'],
                    'status' => $status,
                ]
            );

            $q->attendance()->updateOrCreate(
                [
                    'nik' => $data['nik'],
                    'date' => $data['date'],
                    'schedule_group_attendances_id' => $data['schedule_group_attendances_id'],
                ],
                [
                    'nik' => $data['nik'],
                    'date' => $data['date'],
                    'schedule_group_attendances_id' => $data['schedule_group_attendances_id'],
                ]
            );
        }else{
            $q=$this->model::where('nik', $data['nik'])
                ->where('date', $data['date'])
                ->where('schedule_group_attendances_id', $data['schedule_group_attendances_id'])
                ->first();
            $q->attendance()->updateOrCreate(
                [
                    'nik' => $data['nik'],
                    'date' => $data['date'],
                    'schedule_group_attendances_id' => $data['schedule_group_attendances_id'],
                ],
                [
                    'nik' => $data['nik'],
                    'schedule_group_attendances_id' => $data['schedule_group_attendances_id'],
                    'lat' => $data['lat'],
                    'lng' => $data['lng'],
                    'date' => $data['date'],
                    'time' => $data['time'],
                    // 'photo' => $data['photo'],
                    'status' => $status,
                ]
            );
        }
        return $q;
    }

    /**
     * Memperbarui data absensi.
     */
    public function update($data, $id)
    {
        $q = InAttendance::find($id);
        $q->lat = $data['in']['lat'];
        $q->lng = $data['in']['lng'];
        $q->time = $data['in']['time'];
        $q->save();
        $q->attendance()->updateOrCreate(
            [
                'in_attendance_id'=> $id,
            ],
            [
                'nik'=> $q->nik,
                'schedule_group_attendances_id'=> $q->schedule_group_attendances_id,
                'lat'=> $data['out']['lat'],
                'lng'=> $data['out']['lng'],
                'time'=> $data['out']['time'],
                'date'=> $q->date
            ]
        );
        return $q;
    }

    /**
     * Menghapus data absensi.
     */
    public function delete($id)
    {
        $this->model::destroy($id);
    }
    /**
     * Menghapus data absensi.
     */
    public function sync($data)
    {
        $helper = new MyHelpers();
        foreach ($data['data'] as $k) {
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
                $cek = $this->model::where([
                    'nik' => $k['emp_code'],
                    'date' => $date->format('Y-m-d'),
                    'schedule_group_attendances_id' => $jadwal[0]->id,
                ])->count();
                if ($cek < 1) {
                    $this->model::create([
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
                $q = $this->model::where([
                    'nik' => $k['emp_code'],
                    'date' => $date->format('Y-m-d'),
                    'schedule_group_attendances_id' => $jadwal[0]->id,
                ])->first();
                if ($q) {
                    $cek = $this->model::whereHas('attendance', function ($query) use ($k, $date) {
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
        return $data;
    }
}
