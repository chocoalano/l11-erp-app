<?php

namespace App\Jobs;

use App\Classes\MyHelpers;
use App\Models\Attendance;
use App\Models\ScheduleGroupAttendance;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProcessLargeData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    protected $data;
    public $tries = 3; // Set jumlah maksimal percobaan
    public $timeout = 0; // Set waktu timeout dalam detik
    
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
                return;
            }

            foreach ($chunk->toArray() as $k) {
                $date = Carbon::parse($k['punch_time']);
                $time = $date->format('H:i:s');
                // $user = User::with('group_attendance')->where('nik', $k['emp_code'])->first();
                $result = DB::table('users as u')
                    ->join('group_users as gu', 'u.id', '=', 'gu.user_id')
                    ->join('group_attendances as ga', 'gu.group_attendance_id', '=', 'ga.id')
                    ->where('u.nik', $k['emp_code'])
                    ->select('u.id as user_id', 'ga.id as group_id')
                    ->get();

                // Validate user existence and group_attendance relationship
                if ($result->user_id && $result->group_id) {
                    $cekJadwal = ScheduleGroupAttendance::where([
                        'group_attendance_id' => $result->group_id,
                        'user_id' => $result->user_id,
                        'date' => $date->format('Y-m-d')
                    ])->first();

                    if ($cekJadwal) {
                        $status = $helper->cekStatusTelatAbsen($date->format('Y-m-d'), $cekJadwal['group_attendance_id'], $time, (int)$k['punch_state'] < 1 ? 'in' : 'out')['status'];
                        
                        $data = [
                            'nik' => $k['emp_code'],
                            'schedule_group_attendances_id' => (int)$cekJadwal['id'],
                            'date' => $date->format('Y-m-d'),
                        ];

                        if ((int)$k['punch_state'] < 1) {
                            $data = array_merge($data, [
                                'lat_in' => (double)'-6.1749639',
                                'lng_in' => (double)'106.598571,15',
                                'time_in' => $time,
                                'status_in' => $status
                            ]);
                        } else {
                            $data = array_merge($data, [
                                'lat_out' => (double)'-6.1749639',
                                'lng_out' => (double)'106.598571,15',
                                'time_out' => $time,
                                'status_out' => $status
                            ]);
                        }

                        Attendance::updateOrCreate(
                            [
                                'nik' => $k['emp_code'],
                                'date' => $date->format('Y-m-d'),
                                'schedule_group_attendances_id' => (int)$cekJadwal['id']
                            ],
                            $data
                        );
                    }
                    continue;
                }
                continue;
            }
        });
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Exception $exception)
    {
        // Tangani kegagalan job di sini
        Log::error('Job failed', ['exception' => $exception->getMessage()]);
    }
}
