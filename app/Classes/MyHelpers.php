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
}
