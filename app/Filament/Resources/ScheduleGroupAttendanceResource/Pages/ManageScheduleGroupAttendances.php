<?php

namespace App\Filament\Resources\ScheduleGroupAttendanceResource\Pages;

use App\Filament\Resources\ScheduleGroupAttendanceResource;
use App\Jobs\ProcessImportScheduleFromBiotime;
use App\Models\ScheduleGroupAttendance;
use DateInterval;
use DatePeriod;
use DateTime;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\GroupAttendance;
use App\Models\TimeAttendance;
use App\Models\User;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ManageScheduleGroupAttendances extends ManageRecords
{
    protected static string $resource = ScheduleGroupAttendanceResource::class;

    protected function formatDate($date)
    {
        $dateObj = DateTime::createFromFormat('d/m/Y', $date);
        if (!$dateObj) {
            throw new \Exception("Format tanggal tidak valid");
        }
        return $dateObj->format('Y-m-d');
    }

    protected function getHeaderActions(): array
    {
        $startDate = Carbon::now()->startOfWeek();
        $endDate = $startDate->copy()->endOfWeek();
        $optionsHariSeninSampaiMinggu = [];
        for ($date = $startDate; $date <= $endDate; $date->addDay()) {
            $optionsHariSeninSampaiMinggu[$date->format('l')] = $date->format('l'); // 'l' akan mengembalikan nama hari penuh, seperti "Monday"
        }
        return [
            ActionGroup::make([
                Actions\Action::make('create_schedule_production')
                    ->color('info')
                    ->outlined()
                    ->form([
                        Section::make('Range Date Setup')
                            ->schema([
                                DatePicker::make('start')->required(),
                                DatePicker::make('end')->required(),
                            ])->columns(2),
                        Repeater::make('group_setup')
                            ->schema([
                                Select::make('group')
                                    ->options(GroupAttendance::where('pattern_name', 'production')->get()->pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('time')
                                    ->label('Select the previous shift')
                                    ->options(TimeAttendance::where('pattern_name', 'production')->get()->pluck('type', 'id'))
                                    ->required(),
                                Select::make('config')
                                    ->options([
                                        'rolling' => 'Rolling',
                                    ])
                                    ->required(),
                            ])
                            ->columns(3)
                    ])
                    ->action(function (array $data) {
                        $startDate = new DateTime($data['start']);
                        $endDate = new DateTime($data['end']);
                        $interval = new DateInterval('P1D');
                        $period = new DatePeriod($startDate, $interval, $endDate->modify('+1 day'));
                        $dates = [];
                        foreach ($period as $date) {
                            $dates[] = $date->format('Y-m-d');
                        }
                        $insert_data = [];
                        foreach ($data['group_setup'] as $k) {
                            $u = DB::table('group_attendances as ga')
                                ->join('group_users as gu', 'ga.id', '=', 'gu.group_attendance_id')
                                ->join('users as u', 'gu.user_id', '=', 'u.id')
                                ->where('ga.id', (int) $k['group'])
                                ->select('u.id as user_id')
                                ->get();
                            foreach ($u as $user) {
                                $hasilPergantian = $this->gantiShiftIdPadaSenin($dates, (int) $k['time'], (int) $k['group'], $k['config'], $user->user_id, null);
                                $insert_data[] = $hasilPergantian;
                            }
                        }
                        $mergedArray = array_merge(...$insert_data);
                        return \App\Jobs\ProcessLargeDataSchedule::dispatch($mergedArray);
                    }),
                Actions\Action::make('create_schedule_maintenance')
                    ->color('danger')
                    ->outlined()
                    ->form([
                        Section::make('Range Date Setup')
                            ->schema([
                                DatePicker::make('start')->required(),
                                DatePicker::make('end')->required(),
                            ])->columns(2),
                        Repeater::make('group_setup')
                            ->schema([
                                Select::make('group')
                                    ->options(GroupAttendance::where('pattern_name', 'maintenance')->get()->pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('time')
                                    ->label('Select the previous shift')
                                    ->options(TimeAttendance::where('pattern_name', 'maintenance')->get()->pluck('type', 'id'))
                                    ->required(),
                                Select::make('config')
                                    ->options([
                                        'rolling' => 'Rolling',
                                    ])
                                    ->required(),
                                Select::make('day_off')
                                    ->options($optionsHariSeninSampaiMinggu)
                                    ->required(),
                            ])
                            ->columns(4)
                    ])
                    ->action(function (array $data) {
                        // Inisialisasi tanggal mulai dan akhir
                        $startDate = Carbon::parse($data['start']);
                        $endDate = Carbon::parse($data['end']);
                        $interval = new DateInterval('P1D');
                        $period = new DatePeriod($startDate, $interval, $endDate->modify('+1 day'));
                        $dates = [];
                        foreach ($period as $date) {
                            $dates[] = $date->format('Y-m-d');
                        }
                        $insert_data = [];
                        foreach ($data['group_setup'] as $k) {
                            $u = DB::table('group_attendances as ga')
                                ->join('group_users as gu', 'ga.id', '=', 'gu.group_attendance_id')
                                ->join('users as u', 'gu.user_id', '=', 'u.id')
                                ->where('ga.id', (int) $k['group'])
                                ->select('u.id as user_id')
                                ->get();
                            foreach ($u as $user) {
                                $hasilPergantian = $this->gantiShiftIdPadaSenin($dates, (int) $k['time'], (int) $k['group'], $k['config'], $user->user_id, $k['day_off']);
                                $insert_data[] = $hasilPergantian;
                            }
                        }
                        $mergedArray = array_merge(...$insert_data);
                        return \App\Jobs\ProcessLargeDataSchedule::dispatch($mergedArray);
                    }),
                Actions\Action::make('create_schedule_warehouse')
                    ->color('warning')
                    ->outlined()
                    ->form([
                        Section::make('Range Date Setup')
                            ->schema([
                                DatePicker::make('start')->required(),
                                DatePicker::make('end')->required(),
                            ])->columns(2),
                        Repeater::make('group_setup')
                            ->schema([
                                Select::make('group')
                                    ->options(GroupAttendance::where('pattern_name', 'warehouse')->get()->pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('time')
                                    ->label('Select the previous shift')
                                    ->options(TimeAttendance::where('pattern_name', 'warehouse')->get()->pluck('type', 'id'))
                                    ->required(),
                                Select::make('config')
                                    ->options([
                                        'rolling' => 'Rolling',
                                    ])
                                    ->required(),
                            ])
                            ->columns(3)
                    ])
                    ->action(function (array $data) {
                        $startDate = new DateTime($data['start']);
                        $endDate = new DateTime($data['end']);
                        $interval = new DateInterval('P1D');
                        $period = new DatePeriod($startDate, $interval, $endDate->modify('+1 day'));
                        $dates = [];
                        foreach ($period as $date) {
                            $dates[] = $date->format('Y-m-d');
                        }
                        $insert_data = [];
                        foreach ($data['group_setup'] as $k) {
                            $u = DB::table('group_attendances as ga')
                                ->join('group_users as gu', 'ga.id', '=', 'gu.group_attendance_id')
                                ->join('users as u', 'gu.user_id', '=', 'u.id')
                                ->where('ga.id', (int) $k['group'])
                                ->select('u.id as user_id')
                                ->get();
                            foreach ($u as $user) {
                                $hasilPergantian = $this->gantiShiftIdPadaSenin($dates, (int) $k['time'], (int) $k['group'], $k['config'], $user->user_id, null);
                                $insert_data[] = $hasilPergantian;
                            }
                        }
                        $mergedArray = array_merge(...$insert_data);
                        return \App\Jobs\ProcessLargeDataSchedule::dispatch($mergedArray);
                    }),
                Actions\Action::make('create_schedule_office')
                    ->color('primary')
                    ->outlined()
                    ->form([
                        Section::make('Range Date Setup')
                            ->schema([
                                DatePicker::make('start')->required(),
                                DatePicker::make('end')->required(),
                            ])->columns(2),
                        Repeater::make('group_setup')
                            ->schema([
                                Select::make('group')
                                    ->options(GroupAttendance::where('pattern_name', 'office')->get()->pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('time')
                                    ->label('Select the previous shift')
                                    ->options(TimeAttendance::where('pattern_name', 'office')->get()->pluck('type', 'id'))
                                    ->required(),
                                Select::make('config')
                                    ->options([
                                        'continue' => 'Continue',
                                    ])
                                    ->required(),
                            ])
                            ->columns(3)
                    ])
                    ->action(function (array $data) {
                        $startDate = new DateTime($data['start']);
                        $endDate = new DateTime($data['end']);
                        $interval = new DateInterval('P1D');
                        $period = new DatePeriod($startDate, $interval, $endDate->modify('+1 day'));
                        $dates = [];
                        foreach ($period as $date) {
                            $dates[] = $date->format('Y-m-d');
                        }
                        $insert_data = [];
                        foreach ($data['group_setup'] as $k) {
                            $u = DB::table('group_attendances as ga')
                                ->join('group_users as gu', 'ga.id', '=', 'gu.group_attendance_id')
                                ->join('users as u', 'gu.user_id', '=', 'u.id')
                                ->where('ga.id', (int) $k['group'])
                                ->select('u.id as user_id')
                                ->get();
                            foreach ($u as $user) {
                                $hasilPergantian = $this->gantiShiftIdPadaSenin($dates, (int) $k['time'], (int) $k['group'], $k['config'], $user->user_id, null);
                                $insert_data[] = $hasilPergantian;
                            }
                        }
                        $mergedArray = array_merge(...$insert_data);
                        return \App\Jobs\ProcessLargeDataSchedule::dispatch($mergedArray);
                    }),
                Actions\Action::make('users_custom_reschedule')
                    ->color('info')
                    ->outlined()
                    ->form([
                        Section::make('Range Date Setup')
                            ->schema([
                                DatePicker::make('start')->required(),
                                DatePicker::make('end')->required(),
                            ])->columns(2),
                        Repeater::make('group_setup')
                            ->schema([
                                Select::make('user_id')
                                    ->label('Select users')
                                    ->options(User::get()->pluck('name', 'id'))
                                    ->multiple()
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('time')
                                    ->label('Select time shift')
                                    ->options(TimeAttendance::get()->pluck('type', 'id'))
                                    ->required(),
                                Select::make('config')
                                    ->options([
                                        'continue' => 'Continue',
                                        'rolling' => 'Rolling',
                                    ])
                                    ->required(),
                            ])
                            ->columns(3)
                    ])
                    ->action(function (array $data) {
                        $startDate = new DateTime($data['start']);
                        $endDate = new DateTime($data['end']);
                        $interval = new DateInterval('P1D');
                        $period = new DatePeriod($startDate, $interval, $endDate->modify('+1 day'));
                        $dates = [];
                        foreach ($period as $date) {
                            $dates[] = $date->format('Y-m-d');
                        }
                        $insert_data = [];
                        foreach ($data['group_setup'] as $k) {
                            foreach ($k['user_id'] as $userId) {
                                $u = DB::table('group_attendances as ga')
                                    ->join('group_users as gu', 'ga.id', '=', 'gu.group_attendance_id')
                                    ->join('users as u', 'gu.user_id', '=', 'u.id')
                                    ->where('u.id', $userId)
                                    ->select('u.id as user_id', 'ga.id as group_id')
                                    ->first();
                                if (is_null($u)) {
                                    continue;
                                }
                                $hasilPergantian = $this->gantiShiftIdPadaSenin($dates, (int) $k['time'], $u->group_id, $k['config'], $userId, null);
                                $insert_data[] = $hasilPergantian;
                            }
                        }
                        $mergedArray = array_merge(...$insert_data);
                        return \App\Jobs\ProcessLargeDataSchedule::dispatch($mergedArray);
                    }),
            ])
                ->label('More create schedule actions')
                ->icon('heroicon-c-plus-circle')
                ->color('primary')
                ->button(),
            Actions\Action::make('import')
                ->label('Import From Biotime Attendance')
                ->icon('fas-file-excel')
                ->outlined()
                ->button()
                ->form([
                    Section::make('Import From .xlsx Biotime')
                        ->description('Make sure you have updated all user data and updated the attendance user group first to help the system validation process when doing this process!')
                        ->schema([
                            FileUpload::make('file_excel')
                                ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                                ->storeFiles(false)
                                ->required(),
                        ])
                ])
                ->action(function (array $data): void {
                    $file = $data['file_excel'];
                    $path = $file->getRealPath();
                    $spreadsheet = IOFactory::load($path);
                    // Get the active sheet
                    $sheet = $spreadsheet->getActiveSheet()->toArray();
                    $originalArray = array_slice($sheet, 2);
                    $array = $originalArray;
                    $cek = ProcessImportScheduleFromBiotime::dispatch($array);
                    if ($cek) {
                        Notification::make()
                            ->title('Saved successfully')
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Error unsuccessfully')
                            ->success()
                            ->send();
                    }
                })
        ];
    }

    function transformData($data)
    {
        $header = $data[0];
        $result = [];

        for ($i = 1; $i < count($data); $i++) {
            $employeeData = $data[$i];
            $employee = [
                'nama' => $employeeData[1], // Assuming 'First Name' is at index 1
                'id' => $employeeData[0], // Assuming 'Employee ID' is at index 0
                'tanggal' => [],
                'jadwal' => [],
            ];

            for ($j = 3; $j < count($employeeData); $j++) { // Assuming data starts from index 3
                $employee['tanggal'][] = $header[$j];
                $employee['jadwal'][] = $employeeData[$j];
            }

            $result[] = $employee;
        }

        return $result;
    }

    public function gantiShiftIdPadaSenin($tanggalArray, int $currentShift, int $groupId, $config, $user_id, $dayoff)
    {
        $hasil = [];
        $currentShiftId = $currentShift;
        if ($config === 'rolling' && !is_null($dayoff)) {
            $currentWeek = null;
            $cekGroup = GroupAttendance::find($groupId);
            switch ($dayoff) {
                case 'Monday':
                    $fixedDayOff = Carbon::MONDAY;
                    break;
                case 'Tuesday':
                    $fixedDayOff = Carbon::TUESDAY;
                    break;
                case 'Wednesday':
                    $fixedDayOff = Carbon::WEDNESDAY;
                    break;
                case 'Thursday':
                    $fixedDayOff = Carbon::THURSDAY;
                    break;
                case 'Friday':
                    $fixedDayOff = Carbon::FRIDAY;
                    break;
                case 'Saturday':
                    $fixedDayOff = Carbon::SATURDAY;
                    break;
                case 'Sunday':
                    $fixedDayOff = Carbon::SUNDAY;
                    break;
                default:
                    $fixedDayOff = Carbon::MONDAY;
                    break;
            }
            $filteredArray = array_filter($tanggalArray, function ($date) use ($fixedDayOff) {
                return Carbon::parse($date)->dayOfWeek !== $fixedDayOff;
            });
            $filteredArray = array_values($filteredArray);
            foreach ($filteredArray as $tanggal) {
                $weekOfYear = date('W', strtotime($tanggal));
                if ($currentWeek !== $weekOfYear) {
                    $currentWeek = $weekOfYear;
                    $findTime = TimeAttendance::where('pattern_name', $cekGroup->pattern_name)
                        ->orderByDesc('rules')
                        ->get();
                    foreach ($findTime as $k) {
                        if ($k['rules'] === 1) {
                            $shift1 = $k['id'];
                        } elseif ($k['rules'] === 2) {
                            $shift2 = $k['id'];
                        }
                    }
                    if ($currentShift === $shift1) {
                        $lawanShift = $shift2;
                    } elseif ($currentShift === $shift2) {
                        $lawanShift = $shift1;
                    }
                    $currentShiftId = ($currentShiftId === $currentShift) ? $lawanShift : $currentShift;
                }
                $hasil[] = [
                    'user_id' => $user_id,
                    'date' => $tanggal,
                    'time_attendance_id' => $currentShiftId,
                    'group_attendance_id' => $groupId
                ];
            }
        } elseif ($config === 'rolling' && is_null($dayoff)) {
            $currentWeek = null;
            $cekGroup = GroupAttendance::find($groupId);
            $filteredDates = array_filter($tanggalArray, function ($date) {
                $dayOfWeek = date('N', strtotime($date));
                return $dayOfWeek < 7; // 6 is Saturday, 7 is Sunday
            });
            foreach ($filteredDates as $tanggal) {
                $weekOfYear = date('W', strtotime($tanggal));
                if ($currentWeek !== $weekOfYear) {
                    $currentWeek = $weekOfYear;
                    $findTime = TimeAttendance::where('pattern_name', $cekGroup->pattern_name)
                        ->orderByDesc('rules')
                        ->get();
                    foreach ($findTime as $k) {
                        if ($k['rules'] === 1) {
                            $shift1 = $k['id'];
                        } elseif ($k['rules'] === 2) {
                            $shift2 = $k['id'];
                        }
                    }
                    if ($currentShift === $shift1) {
                        $lawanShift = $shift2;
                    } elseif ($currentShift === $shift2) {
                        $lawanShift = $shift1;
                    }
                    $currentShiftId = ($currentShiftId === $currentShift) ? $lawanShift : $currentShift;
                }
                $hasil[] = [
                    'user_id' => $user_id,
                    'date' => $tanggal,
                    'time_attendance_id' => $currentShiftId,
                    'group_attendance_id' => $groupId
                ];
            }
        } else {
            $group = GroupAttendance::find($groupId);
            $shift = TimeAttendance::find($currentShift);
            if ($shift->type === 'Office' && $group->name === 'GROUP NON SHIFT') { //office staff
                $filteredDates = array_filter($tanggalArray, function ($date) {
                    $dayOfWeek = date('N', strtotime($date));
                    return $dayOfWeek < 6; // 6 is Saturday, 7 is Sunday
                });
                foreach ($filteredDates as $tanggal) {
                    $hasil[] = [
                        'user_id' => $user_id,
                        'date' => $tanggal,
                        'time_attendance_id' => $currentShiftId,
                        'group_attendance_id' => $groupId
                    ];
                }
            } elseif ($shift->type === 'Adm' && $group->name === 'GROUP NON SHIFT ADM') { //office nonstaff (adm)
                $filteredDates = array_filter($tanggalArray, function ($date) {
                    $dayOfWeek = date('N', strtotime($date));
                    return $dayOfWeek < 7; // 6 is Saturday, 7 is Sunday
                });
                foreach ($filteredDates as $tanggal) {
                    $hasil[] = [
                        'user_id' => $user_id,
                        'date' => $tanggal,
                        'time_attendance_id' => $currentShiftId,
                        'group_attendance_id' => $groupId
                    ];
                }
            } else {
                Notification::make()
                    ->title('Saved unsuccessfully')
                    ->icon('heroicon-s-x-circle')
                    ->iconColor('danger')
                    ->body('The data you input does not match the agreed policy standards!')
                    ->send();
            }
        }
        return $hasil;
    }
}
