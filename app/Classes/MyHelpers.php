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
    function syncJamJadwalKerja($departmentId, $jam) {
        // Inisialisasi default untuk $where
        $where = ['type' => 'Office', 'pattern_name' => 'office'];
    
        $dept = \App\Models\Organization::find($departmentId);
    
        // Cek jika $dept tidak null
        if ($dept) {
            $department = $dept->name;
            
            if ($department === 'CLEANING SERVICE') {
                if ($jam === 'Shift Adm') {
                    $where = ['type' => 'Adm', 'pattern_name' => 'office', 'rules' => 0];
                } elseif ($jam === 'Shift 1') {
                    $where = ['type' => 'OB Shift 1', 'pattern_name' => 'office', 'rules' => 1];
                } elseif ($jam === 'Shift 2') {
                    $where = ['type' => 'OB Shift 2', 'pattern_name' => 'office', 'rules' => 2];
                }
            } elseif ($department === 'MAINTENANCE') {
                if ($jam === 'Office Schedule') {
                    $where = ['type' => 'Office', 'pattern_name' => 'office'];
                } elseif ($jam === 'Shift 1') {
                    $where = ['type' => 'MTC Shift 1', 'pattern_name' => 'maintenance', 'rules' => 1];
                } elseif ($jam === 'Shift 2') {
                    $where = ['type' => 'MTC Shift 2', 'pattern_name' => 'maintenance', 'rules' => 2];
                }
            } elseif ($department === 'WAREHOUSE') {
                if ($jam === 'Office Schedule') {
                    $where = ['type' => 'Office', 'pattern_name' => 'office'];
                } elseif ($jam === 'Shift 1') {
                    $where = ['type' => 'WH Shift 1', 'pattern_name' => 'warehouse', 'rules' => 1];
                } elseif ($jam === 'Shift 2 Gudang') {
                    $where = ['type' => 'WH Shift 2', 'pattern_name' => 'warehouse', 'rules' => 2];
                }
            } elseif ($department === 'PRODUKSI' || $department === 'QC') {
                if ($jam === 'Office Schedule') {
                    $where = ['type' => 'Office', 'pattern_name' => 'office'];
                } elseif ($jam === 'Shift 1') {
                    $where = ['type' => 'Shift 1', 'pattern_name' => 'production', 'rules' => 1];
                } elseif ($jam === 'Shift 2') {
                    $where = ['type' => 'Shift 2', 'pattern_name' => 'production', 'rules' => 2];
                }
            }
        }
    
        // Cari dan kembalikan data time attendance sesuai kondisi $where
        return \App\Models\TimeAttendance::where($where)->first();
    }
    function validateUserExist(array $data)
    {
        try {
            $religionMap = [
                'KHATOLIK' => 'catholic',
                'PROTESTAN' => 'protestant',
                'HINDU' => 'hindu',
                'BUDDHA' => 'buddha',
                'ISLAM' => 'islam',
            ];
    
            $agama = $religionMap[$data['religion']] ?? 'khonghucu';
    
            $user = User::updateOrCreate(
                ['nik' => $data['nik']],
                [
                    'name' => $data['nama'],
                    'email' => $data['email'],
                    'email_verified_at' => date('Y-m-d H:i:s'),
                    'phone' => $data['no_hp'],
                    'placebirth' => $data['placebirth'],
                    'datebirth' => $data['datebirth'],
                    'gender' => $data['gender'] === 'LAKI-LAKI' ? 'm' : 'w',
                    'religion' => $agama,
                ]
            );
    
            $org = Organization::updateOrCreate(
                ['name' => $data['dept']],
                ['description' => $data['dept']]
            );
    
            $job = JobPosition::updateOrCreate(
                ['name' => $data['position']],
                ['description' => $data['position']]
            );
    
            $lvl = JobLevel::updateOrCreate(
                ['name' => $data['level']],
                ['description' => $data['level']]
            );
    
            $atasanName = $data['atasan'] === 'Agustinus' ? 'Tubagus Angga Dheviests' : $data['atasan'];
    
            $appline = User::where('name', $atasanName)->firstOrFail();
            $appmngr = $appline;
    
            $dataEmp = [
                'organization_id' => $org->id,
                'job_position_id' => $job->id,
                'job_level_id' => $lvl->id,
                'approval_line' => $appline->id,
                'approval_manager' => $appmngr->id,
                'company_id' => 1,
                'branch_id' => 1,
                'status' => 'contract',
                'join_date' => $data['tgl_bergabung'],
                'sign_date' => $data['tgl_bergabung'],
            ];
    
            $user->employe()->updateOrCreate($dataEmp);
    
            return $user;
        } catch (\Exception $e) {
            throw $e;
        }
    }
    function addUserGroup(array $data)
    {
        $groups = [
            'groupA' => [],
            'groupB' => [],
            'whA' => [],
            'whB' => [],
            'office' => [],
            'adm' => [],
            'mtncA' => [],
            'mtncB' => [],
            'mtncC' => [],
            'mtncD' => [],
            'mtncE' => [],
            'mtncF' => [],
            'mtncG' => []
        ];

        foreach ($data as $key) {
            $u = User::where('nik', $key['nik'])->first();

            switch ($key['department']) {
                case 'PRODUKSI':
                case 'CLEANING SERVICE':
                    if ($key['jam'] === 'Shift 1') {
                        $groups['groupA'][] = $u->id;
                    } elseif ($key['jam'] === 'Shift 2') {
                        $groups['groupB'][] = $u->id;
                    } else {
                        $groups['adm'][] = $u->id;
                    }
                    break;

                case 'QC':
                    if ($key['jobPosition'] === 'QC INLINE') {
                        if ($key['jam'] === 'Shift 1') {
                            $groups['groupA'][] = $u->id;
                        } else {
                            $groups['groupB'][] = $u->id;
                        }
                    } else {
                        $groups['office'][] = $u->id;
                    }
                    break;

                case 'WAREHOUSE':
                    if ($key['jobPosition'] === 'DRIVER WAREHOUSE' || $key['jam'] === 'Office Schedule') {
                        $groups['adm'][] = $u->id;
                    } elseif ($key['jam'] === 'Shift 1') {
                        $groups['whA'][] = $u->id;
                    } else {
                        $groups['whB'][] = $u->id;
                    }
                    break;

                case 'MAINTENANCE':
                    if ($key['jobPosition'] === 'SPV MAINTENANCE') {
                        $groups['office'][] = $u->id;
                    } else {
                        switch ($key['name']) {
                            case 'Yoga Pangestu':
                                $groups['mtncA'][] = $u->id;
                                break;
                            case 'Ari Maulana Rahman':
                                $groups['mtncB'][] = $u->id;
                                break;
                            case 'Muhammad Rivaldiansyah Nugraha':
                                $groups['mtncC'][] = $u->id;
                                break;
                            case 'Rusli':
                                $groups['mtncD'][] = $u->id;
                                break;
                            case 'Yan Dwiyono Putro':
                                $groups['mtncE'][] = $u->id;
                                break;
                            case 'Ali Firdaus':
                                $groups['mtncF'][] = $u->id;
                                break;
                            case 'Mujib RIdwan Fauzi':
                                $groups['mtncG'][] = $u->id;
                                break;
                        }
                    }
                    break;

                default:
                    $groups['office'][] = $u->id;
                    break;
            }
        }

        $groupNames = [
            'GROUP NON SHIFT ADM' => 'adm',
            'GROUP NON SHIFT' => 'office',
            'GROUP-A' => 'groupA',
            'GROUP-B' => 'groupB',
            'GUDANG A' => 'whA',
            'GUDANG B' => 'whB',
            'MAINTENANCE A' => 'mtncA',
            'MAINTENANCE B' => 'mtncB',
            'MAINTENANCE C' => 'mtncC',
            'MAINTENANCE D' => 'mtncD',
            'MAINTENANCE E' => 'mtncE',
            'MAINTENANCE F' => 'mtncF',
            'MAINTENANCE G' => 'mtncG'
        ];

        foreach ($groupNames as $groupName => $groupKey) {
            $group = \App\Models\GroupAttendance::where([
                'name' => $groupName
            ])->first();
    
            if ($group) {
                $group->user()->sync($groups[$groupKey]);
            }
        }

        return $groups;
    }
}
