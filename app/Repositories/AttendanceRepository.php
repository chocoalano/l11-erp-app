<?php

namespace App\Repositories;

use App\Classes\MyHelpers;
use Illuminate\Support\Str;
use App\Interfaces\AttendanceInterface;
use App\Models\GroupAttendance;
use App\Models\GroupUsersAttendance;
use App\Models\InAttendance;
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
            $find = ScheduleGroupAttendance::with('time', 'group_attendance', 'group_users')
            ->whereHas('group_users', function ($query) use ($k) {
                $query->where('nik', $k['emp_code']);
            })
            ->first();
            $date = Carbon::parse($k['punch_time']);
            $time = $date->format('H:i:s');
            $comparisonTimeString = "12:00:00";
            $t = Carbon::createFromTimeString($time);
            $ct = Carbon::createFromTimeString($comparisonTimeString);
            $flag = $t->lt($ct) ? 'A' : 'B';
            if (is_null($find)) {
                // VALIDASI USER::STARTED
                $user = User::where('nik', $k['emp_code'])->first();
                if (is_null($user)) {
                    $user = User::updateOrCreate(
                        ['nik'=>$k['emp_code']],
                        [
                            'name' => $k['first_name'],
                            'nik' => $k['emp_code'],
                            'email' => Str::snake($k['first_name']).'_'.$k['emp_code'].'@sinergiabadisentosa.com',
                            'password' => Hash::make($k['emp_code'])
                        ]
                    );
                    $user->assignRole('panel_user');
                }
                // VALIDASI USER::ENDED
                // VALIDASI GROUP BERDASARKAN DEPARTEMENT::STARTED
                $groupPresence = GroupAttendance::where('name', $k['department'].'-'.$flag)->first();
                if (is_null($groupPresence)) {
                    $groupPresence = GroupAttendance::firstOrCreate(
                        ['name'=>$k['department'].'-'.$flag, 'description'=>'Information group '.$k['department']],
                        ['name'=>$k['department'].'-'.$flag, 'description'=>'Information group '.$k['department']],
                    );
                }
                GroupUsersAttendance::firstOrCreate(
                    [ "group_attendance_id"=>$groupPresence->id, "nik"=>$user->nik ],
                    [ "group_attendance_id"=>$groupPresence->id, "nik"=>$user->nik ],
                );
                // VALIDASI GROUP BERDASARKAN DEPARTEMENT::ENDED
                // VALIDASI WAKTU BERDASARKAN JAM::STARTED
                $columnFindTime = (int)$k['punch_state'] > 0 ? 'out':'in';
                $findTime = TimeAttendance::whereTime($columnFindTime, '>', $time)->orWhereTime($columnFindTime, '<', $time)->first();
                $roundedTime = $date->roundHour();
                $rtime = $findTime;
                if(is_null($findTime)){
                    $currentTime = Carbon::parse($roundedTime);
                    $futureTime = $currentTime->copy()->addHours(7);
                    $oldTime = $currentTime->copy()->subHours(7);
    
                    $rtime = TimeAttendance::updateOrCreate(
                        [$columnFindTime => $roundedTime],
                        [
                            'type'=>'Shift '.Str::lower(Str::random(5)),
                            'in'=>(int)$k['punch_state'] < 1 ? $roundedTime:$oldTime->toTimeString(),
                            'out'=>(int)$k['punch_state'] < 1 ? $futureTime->toTimeString():$roundedTime,
                        ]
                    );
                }
                $groupPresence->schedule_attendance()->updateOrCreate(
                    ['time_attendance_id'=>1],
                    [
                        'time_attendance_id'=>$rtime->id,
                        'date'=>$date->format('Y-m-d'),
                        'status'=>$k['punch_state_display'] === "Check In" ? 'in' : 'out'
                    ],
                );
                // VALIDASI WAKTU BERDASARKAN JAM::ENDED
                $find = ScheduleGroupAttendance::with('time', 'group_attendance', 'group_users')
                ->whereHas('group_users', function ($query) use ($k) {
                    $query->where('nik', $k['emp_code']);
                })
                ->first();
            }
            $validate = $helper->cekStatusTelatAbsen($find->group_attendance_id, $time, (int)$k['punch_state'] < 1 ? 'in' : 'out');
            $status = $validate['status'];
            if ((int)$k['punch_state'] < 1) {
                // cek jam masuk simpan data pertama kali saja
                $cek = $this->model::where([
                    'nik' => $k['emp_code'],
                    'date' => $date->format('Y-m-d'),
                    'schedule_group_attendances_id' => $find->id,
                ])->count();
                if ($cek < 1) {
                    $this->model::create([
                        'nik' => $k['emp_code'],
                        'schedule_group_attendances_id' => $find->id,
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
                    'schedule_group_attendances_id' => $find->id,
                ])->first();
                if ($q) {
                    $q->attendance()->updateOrCreate(
                        [
                            'nik' => $k['emp_code'],
                            'date' => $date->format('Y-m-d'),
                            'schedule_group_attendances_id' => $find->id,
                        ],
                        [
                            'nik' => $k['emp_code'],
                            'schedule_group_attendances_id' => $find->id,
                            'lat' => (double)'-6.1749639',
                            'lng' => (double)'106.598571,15',
                            'date' => $date->format('Y-m-d'),
                            'time' => $time,
                            'status' => $status,
                        ]
                    );
                }
            }
        }
        return $data;
    }
}
