<?php
namespace App\Classes;

use App\Models\Attendance;
use App\Models\Branch;
use App\Models\Company;
use App\Models\GroupAttendance;
use App\Models\JobLevel;
use App\Models\JobPosition;
use App\Models\Organization;
use App\Models\ScheduleGroupAttendance;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class MyHelpers
{
    public function cekStatusTelatAbsen($date, $schedule_group_attendances_id, $time, $flag){
        $find = ScheduleGroupAttendance::with('time')
        ->where('date', $date)
        ->where('group_attendance_id', $schedule_group_attendances_id)
        ->first();
        $timeSchedule = $find->time;
        if ($flag === 'in') {
            $scheduledTime = $timeSchedule->in;
            $arrivalTime = $time;
        }else{
            $scheduledTime = $timeSchedule->out;
            $arrivalTime = $time;
        }
        $compare = $this->compareTimes($scheduledTime, $arrivalTime, $flag);
        return $compare;
    }
    function compareTimes($scheduledTime, $arrivalTime, $flag){
            // Membuat objek Carbon dari kedua waktu tersebut
            $scheduledDateTime = Carbon::createFromFormat('H:i:s', $scheduledTime);
            $arrivalDateTime = Carbon::createFromFormat('H:i:s', $arrivalTime);
            // Menghitung selisih waktu dalam menit
            $minutesLate = $scheduledDateTime->diffInMinutes($arrivalDateTime);
            
            // Menampilkan hasil
            if ($flag === 'out') {
                return [
                    'scheduled_time' => $scheduledTime,
                    'arrival_time' => $arrivalTime,
                    'minutes' => $minutesLate < 0 ? abs($minutesLate) : $minutesLate,
                    'status' => $minutesLate < 0 ? 'unlate' : 'late',
                ];
            }else{
                return [
                    'scheduled_time' => $scheduledTime,
                    'arrival_time' => $arrivalTime,
                    'minutes' => $minutesLate < 0 ? abs($minutesLate) : $minutesLate,
                    'status' => $minutesLate < 0 ? 'unlate' : 'late',
                ];
            }
    }
    function validateAndFindGroupAttendance($deptname, $positionname, $time, $status){
        $shift = DB::table('time_attendances')
        ->select('*', DB::raw('ABS(TIME_TO_SEC(TIMEDIFF("'.$time.'", `in`))) AS time_difference'))
        ->orderBy('time_difference', 'asc')
        ->first();
        switch ($deptname) {
            case 'PRODUKSI':
                if ($positionname === 'OPERATOR PRODUKSI') {
                    if($shift->id === 2){
                        return 'GROUP-A';
                    }else{
                        return 'GROUP-B';
                    }
                } elseif($positionname === 'OPERATOR SUPPORT') {
                    if($shift->id === 2){
                        return 'GROUP-A';
                    }else{
                        return 'GROUP-B';
                    }
                }else{
                    return 'GROUP NON SHIFT';
                }
            case 'MAINTENANCE':
                if ($positionname === 'STAFF MAINTENANCE' || $positionname === 'OPERTAOR MAINTENANCE') {
                    if($shift->id === 2){
                        return 'MAINTENANCE A';
                    }else{
                        return 'MAINTENANCE B';
                    }
                }else{
                    return 'GROUP NON SHIFT';
                }
            case 'QC':
                if ($positionname === 'QC INLINE') {
                    if($shift->id === 2){
                        return 'GROUP-A';
                    }else{
                        return 'GROUP-B';
                    }
                }else{
                    return 'GROUP NON SHIFT';
                }
            case 'WAREHOUSE':
                if($shift->id === 2){
                    return 'GUDANG A';
                }else{
                    return 'GUDANG B';
                }
            case 'CLEANING SERVICE':
                if($shift->id === 2){
                    return 'GROUP-A';
                }else{
                    return 'GROUP-B';
                }
            case 'RND':
                return 'GROUP NON SHIFT';
            case 'REGULATORY':
                return 'GROUP NON SHIFT';
            case 'HRGA':
                return 'GROUP NON SHIFT';
            case 'MARKETING':
                return 'GROUP NON SHIFT';
            case 'FAT':
                return 'GROUP NON SHIFT';
            case 'QA':
                return 'GROUP NON SHIFT';
            case 'DESIGN':
                return 'GROUP NON SHIFT';
            case 'PPIC':
                return 'GROUP NON SHIFT';
            case 'ICT':
                return 'GROUP NON SHIFT';
            case 'SECURITY':
                if($shift->id === 2){
                    return 'GROUP-A';
                }else{
                    return 'GROUP-B';
                }
            
            default:
            return 'GROUP NON SHIFT';
        }
    }
    function validateUserGroupSchedule($groupAttendanceId, $date, $time){
        $groupPresence = GroupAttendance::where('id', $groupAttendanceId)
        ->whereHas('schedule_attendance', function ($query) use ($date) {
            $query->where('date', $date);
        })->first();
        $jadwal = !is_null($groupPresence) ? $groupPresence->schedule_attendance : null;
        if (is_null($jadwal)) {
            $shift = DB::table('time_attendances')
            ->select('*', DB::raw('ABS(TIME_TO_SEC(TIMEDIFF("'.$time.'", `in`))) AS time_difference'))
            ->orderBy('time_difference', 'asc')
            ->first();
            $x = GroupAttendance::find($groupAttendanceId)->first();
            $x->schedule_attendance()->updateOrCreate(
                [
                    "date"=>$date
                ],
                [
                    "time_attendance_id"=>$shift->id,
                    "date"=>$date,
                    "status"=>'unpresent'
                ]
            );
            $jadwal = $x->schedule_attendance;
        }
        $arr = [];
        foreach ($jadwal as $k) {
            array_push($arr, [
            "id" => $k['id'],
            "group_attendance_id" => $k['group_attendance_id'],
            "time_attendance_id" => $k['time_attendance_id'],
            "date" => $k['date'],
            "status" => $k['status'],
          ]);
        }
        $filteredData = array_filter($arr, function($item) use ($date) {
            return $item['date'] === $date;
        });
        $firstElement = reset($filteredData);
        return $firstElement;
    }
    function validateUserExistAttendanceSync($nik, $department, $position, $first_name){
        // Cari user berdasarkan NIK
        $user = User::with('group_attendance')->where('nik', $nik)->first();
        if (is_null($user)) {
            // Buat atau ambil departemen
            $dept = Organization::firstOrCreate(
                [
                    "name" => $department,
                    "description" => $department,
                ]
            );
            // Buat atau ambil posisi
            $position = JobPosition::firstOrCreate(
                [
                    "name" => $position,
                    "description" => $position,
                ]
            );
            // Siapkan data user baru
            $user = User::create([
                'name' => $first_name,
                'nik' => $nik,
                'email' => Str::snake($first_name) . '_' . $nik . '@sinergiabadisentosa.com',
                'password' => Hash::make($nik)
            ]);

            // Assign role ke user
            $role = Role::findByName('panel_user');
            $user->assignRole($role);
            // Buat atau ambil perusahaan
            $company = Company::firstOrCreate(
                ['name' => 'PT. SINERGI ABADI SENTOSA'],
                [
                    'name' => 'PT. SINERGI ABADI SENTOSA',
                    'latitude' => '-6.1749639',
                    'longitude' => '106.59857115',
                    'full_address' => 'Jl. Prabu Kian Santang No.169A, RT.001/RW.004, Sangiang Jaya, Kec. Periuk, Kota Tangerang, Banten 15132',
                ]
            );
            // Buat atau ambil cabang
            $branch = Branch::firstOrCreate(
                ['name' => 'Head Office'],
                [
                    'name' => 'Head Office',
                    'latitude' => '-6.1749639',
                    'longitude' => '106.59857115',
                    'full_address' => 'Jl. Prabu Kian Santang No.169A, RT.001/RW.004, Sangiang Jaya, Kec. Periuk, Kota Tangerang, Banten 15132',
                ]
            );
            // Ambil level pekerjaan dan approval
            $lvl = JobLevel::find(7);
            $approval = User::find(1);
            // Buat data karyawan baru
            $user->employe()->create([
                'organization_id' => $dept->id,
                'job_position_id' => $position->id,
                'job_level_id' => $lvl ? $lvl->id : null,
                'company_id' => $company->id,
                'branch_id' => $branch->id,
                'approval_line' => $approval ? $approval->id : null,
                'approval_manager' => $approval ? $approval->id : null,
                'status' => 'contract',
                'join_date' => date('Y-m-d'),
                'sign_date' => date('Y-m-d'),
            ]);
        }
        return $user;
    }
}
