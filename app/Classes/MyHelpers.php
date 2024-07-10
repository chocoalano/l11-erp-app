<?php
namespace App\Classes;

use App\Models\GroupAttendance;
use App\Models\GroupUsersAttendance;
use App\Models\ScheduleGroupAttendance;
use App\Models\TimeAttendance;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MyHelpers
{
    public function cekStatusTelatAbsen($schedule_group_attendances_id, $time, $flag){
        $find = ScheduleGroupAttendance::with('time')
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
}
