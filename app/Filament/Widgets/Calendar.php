<?php

namespace App\Filament\Widgets;

use App\Models\HRGA\AdjustAttendance;
use App\Models\HRGA\Cuti;
use App\Models\HRGA\Dispen;
use App\Models\HRGA\Event;
use App\Models\HRGA\IzinInOut;
use App\Models\HRGA\IzinOrSick;
use App\Models\HRGA\WorkOvertime;
use App\Models\JobPosition;
use App\Models\Organization;
use App\Models\ScheduleGroupAttendance;
use App\Models\User;
use Filament\Actions\CreateAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Guava\Calendar\Widgets\CalendarWidget;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Calendar extends CalendarWidget
{
    protected bool $eventClickEnabled = true;
    public function getDateClickContextMenuActions(): array
    {
        $authUser = User::with('employe')->where('id', auth()->user()->id)->first();
        return [
            CreateAction::make('cuti')
                ->label('Create Form Cuti')
                ->form([
                    Section::make('Form Cuti')
                        ->columns([
                            'sm' => 1,
                            'xl' => 2,
                            '2xl' => 2,
                        ])
                        ->schema([
                            Select::make('user_id')
                                ->label('Choose Users')
                                ->options(function (Builder $query) use ($authUser) {
                                    $u = User::find(auth()->user()->id);
                                    if ($u->hasRole(['super_admin'])) {
                                        return User::get()
                                            ->pluck('name', 'id');
                                    }else{
                                        return User::with('employe')
                                            ->whereHas('employe', function ($q) use ($authUser) {
                                                $q->where('organization_id', $authUser->employe->organization_id);
                                            })
                                            ->get()
                                            ->pluck('name', 'id');
                                    }
                                })
                                ->searchable()
                                ->required(),
                            Select::make('category')
                                ->options([
                                    "cuti tahunan"=>"Cuti Tahunan",
                                    "cuti 1/2 hari"=>"Cuti 1/2 Hari",
                                ])
                                ->required(),
                            DatePicker::make('start_date')->required(),
                            DatePicker::make('end_date')->required(),
                            TimePicker::make('start_time')->required(),
                            TimePicker::make('end_time')->required(),
                            Textarea::make('description')->columnSpanFull()->required()
                        ])
                ])
                ->mountUsing(fn ($arguments, $form) => $form->fill([
                    'start_date' => data_get($arguments, 'dateStr'),
                    'end_date' => data_get($arguments, 'dateStr'),
                ]))
                ->mutateFormDataUsing(function (array $data): array {
                    $data['user_approved'] = 'y';
                    return $data;
                })
                ->using(function (array $data): Model {
                    return DB::transaction(function () use ($data) {
                        // Membuat instance Cuti terlebih dahulu
                        $i = Cuti::create($data);
                        // Membuat instance Event setelahnya dan mengasosiasikan dengan Cuti
                        $e = Event::create([
                            'model_name' => 'App\Models\HRGA\Cuti',
                            'id_form' => $i->id, // Menggunakan id dari Cuti yang baru saja dibuat
                        ]);
                        // Mengupdate Cuti dengan event_number dari Event
                        $i->update([
                            'event_number' => $e->event_number
                        ]);
                    });
                }),
            CreateAction::make('izinSick')
                ->label('Create Form Permission Only/Sick, ')
                ->form([
                    Section::make('Permission Form')
                        ->description('Please create your normal or sick leave form on this form.')
                        ->schema([
                            Select::make('user_id')
                                ->label('Choose Users')
                                ->options(function (Builder $query) use ($authUser) {
                                    $u = User::find(auth()->user()->id);
                                    if ($u->hasRole(['super_admin'])) {
                                        return User::get()
                                            ->pluck('name', 'id');
                                    }else{
                                        return User::with('employe')
                                            ->whereHas('employe', function ($q) use ($authUser) {
                                                $q->where('organization_id', $authUser->employe->organization_id);
                                            })
                                            ->get()
                                            ->pluck('name', 'id');
                                    }
                                })
                                ->searchable()
                                ->preload(),
                            Select::make('category')
                                ->options([
                                    'Only Permission' => [
                                        'izin 1/2 hari' => 'Izin 1/2 hari',
                                        'izin sehari' => 'Izin sehari',
                                    ],
                                    'Sick' => [
                                        'sakit dengan surat dokter dan resep' => 'Sakit dengan surat dokter dan resep',
                                        'sakit tanpa surat dokter' => 'Sakit tanpa surat dokter',
                                        'sakit kecelakaan kerja' => 'Sakit kecelakaan kerja',
                                        'sakit rawat inap' => 'Sakit rawat inap',
                                    ],
                                ])
                                ->required(),
                            Toggle::make('type')
                                ->inline(false),
                            DatePicker::make('start_date'),
                            DatePicker::make('end_date'),
                            TimePicker::make('start_time'),
                            TimePicker::make('end_time'),
                            Textarea::make('description')
                                ->columnSpanFull(),
                            FileUpload::make('file_image')
                                ->directory('permission-sick')
                                ->columnSpanFull(),
                        ])->columns([
                            'sm' => 1,
                            'xl' => 2,
                            '2xl' => 2,
                        ])
                ])
                ->mountUsing(fn ($arguments, $form) => $form->fill([
                    'start_date' => data_get($arguments, 'dateStr'),
                    'end_date' => data_get($arguments, 'dateStr'),
                ]))
                ->mutateFormDataUsing(function (array $data): array {
                    $data['user_approved'] = 'y';
                    return $data;
                })
                ->action(function (array $data) {
                    // Parsing tanggal dan waktu menggunakan Carbon
                    $startDate = Carbon::parse($data['start_date'].' '.$data['start_time']);
                    $endDate = Carbon::parse($data['end_date'].' '.$data['end_time']);
                    $data['total_day'] = $startDate->diffInDays($endDate);
                    // Membuat instance IzinOrSick terlebih dahulu
                    $i = IzinOrSick::create($data);
                    // Membuat instance Event setelahnya dan mengasosiasikan dengan IzinOrSick
                    $e = Event::create([
                        'model_name' => 'App\Models\HRGA\IzinOrSick',
                        'id_form' => $i->id, // Menggunakan id dari IzinOrSick yang baru saja dibuat
                    ]);
                    // Mengupdate IzinOrSick dengan event_number dari Event
                    $i->update([
                        'event_number' => $e->event_number
                    ]);
                }),
            CreateAction::make('izinInOut')
                ->label('Create Form Permission In/Out')
                ->form([
                    Section::make('Permission Form In Or Out')
                        ->description('Please create your in or out leave office on this form.')
                        ->schema([
                            Select::make('user_id')
                                ->label('Choose Users')
                                ->options(function (Builder $query) use ($authUser) {
                                    $u = User::find(auth()->user()->id);
                                    if ($u->hasRole(['super_admin'])) {
                                        return User::get()
                                            ->pluck('name', 'id');
                                    }else{
                                        return User::with('employe')
                                            ->whereHas('employe', function ($q) use ($authUser) {
                                                $q->where('organization_id', $authUser->employe->organization_id);
                                            })
                                            ->get()
                                            ->pluck('name', 'id');
                                    }
                                })
                                ->searchable()
                                ->preload(),
                            DatePicker::make('date'),
                            TimePicker::make('out_time'),
                            TimePicker::make('in_time'),
                            Textarea::make('description')
                            ->columnSpanFull()
                        ])->columns([
                            'sm' => 1,
                            'xl' => 2,
                            '2xl' => 2,
                        ])
                ])
                ->mountUsing(fn ($arguments, $form) => $form->fill([
                    'date' => data_get($arguments, 'dateStr')
                ]))
                ->mutateFormDataUsing(function (array $data): array {
                    $data['user_approved'] = 'y';
                    return $data;
                })
                ->action(function (array $data) {
                    // Membuat instance izinInOut terlebih dahulu
                    $i = IzinInOut::create($data);
                    // Membuat instance Event setelahnya dan mengasosiasikan dengan izinInOut
                    $e = Event::create([
                        'model_name' => 'App\Models\HRGA\IzinInOut',
                        'id_form' => $i->id, // Menggunakan id dari izinInOut yang baru saja dibuat
                    ]);
                    // Mengupdate izinInOut dengan event_number dari Event
                    $i->update(['event_number' => $e->event_number]);
                }),
            CreateAction::make('Dispensation')
                ->label('Create Form Dispensation')
                ->form([
                    Section::make('Dispensation Form In Or Out')
                        ->description('Please create your dispensation on this form.')
                        ->schema([
                            Select::make('user_id')
                                ->label('Choose Users')
                                ->options(function (Builder $query) use ($authUser) {
                                    $u = User::find(auth()->user()->id);
                                    if ($u->hasRole(['super_admin'])) {
                                        return User::get()
                                            ->pluck('name', 'id');
                                    }else{
                                        return User::with('employe')
                                            ->whereHas('employe', function ($q) use ($authUser) {
                                                $q->where('organization_id', $authUser->employe->organization_id);
                                            })
                                            ->get()
                                            ->pluck('name', 'id');
                                    }
                                })
                                ->searchable()
                                ->preload(),
                            TextInput::make('category'),
                            DatePicker::make('start_date'),
                            DatePicker::make('end_date'),
                            Textarea::make('description')
                            ->columnSpanFull()
                        ])->columns([
                            'sm' => 1,
                            'xl' => 2,
                            '2xl' => 2,
                        ])
                ])
                ->mutateFormDataUsing(function (array $data): array {
                    $data['user_approved'] = 'y';
                    return $data;
                })
                ->action(function (array $data) {
                    $startDate = Carbon::createFromFormat('Y-m-d', $data['start_date']);
                    $endDate = Carbon::createFromFormat('Y-m-d', $data['end_date']);
                    $data['total_day'] = $startDate->diffInDays($endDate);
                    // Membuat instance Dispensation terlebih dahulu
                    $i = Dispen::create($data);
                    // Membuat instance Event setelahnya dan mengasosiasikan dengan Dispensation
                    $e = Event::create([
                        'model_name' => 'App\Models\HRGA\Dispen',
                        'id_form' => $i->id, // Menggunakan id dari Dispensation yang baru saja dibuat
                    ]);
                    // Mengupdate Dispensation dengan event_number dari Event
                    $i->update(['event_number' => $e->event_number]);
                })
                ->mountUsing(fn ($arguments, $form) => $form->fill([
                    'start_date' => data_get($arguments, 'dateStr'),
                    'end_date' => data_get($arguments, 'dateStr')
                ])),
            CreateAction::make('AdjustAttendance')
                ->label('Create Form Adjust Attendance')
                ->form([
                    Section::make('Adjust Attendance Form In Or Out')
                        ->description('Please create your adjust attendance on this form.')
                        ->schema([
                            Select::make('user_id')
                                ->label('Choose Users')
                                ->options(function (Builder $query) use ($authUser) {
                                    $u = User::find(auth()->user()->id);
                                    if ($u->hasRole(['super_admin'])) {
                                        return User::get()
                                            ->pluck('name', 'id');
                                    }else{
                                        return User::with('employe')
                                            ->whereHas('employe', function ($q) use ($authUser) {
                                                $q->where('organization_id', $authUser->employe->organization_id);
                                            })
                                            ->get()
                                            ->pluck('name', 'id');
                                    }
                                })
                                ->searchable()
                                ->preload(),
                            TextInput::make('problem'),
                            DatePicker::make('date'),
                            Textarea::make('description')
                            ->columnSpanFull()
                        ])->columns([
                            'sm' => 1,
                            'xl' => 2,
                            '2xl' => 2,
                        ])
                ])
                ->mutateFormDataUsing(function (array $data): array {
                    $data['user_approved'] = 'y';
                    return $data;
                })
                ->action(function (array $data) {
                    $i = AdjustAttendance::create($data);
                    // Membuat instance Event setelahnya dan mengasosiasikan dengan AdjustAttendance
                    $e = Event::create([
                        'model_name' => 'App\Models\HRGA\AdjustAttendance',
                        'id_form' => $i->id,
                    ]);
                    // Mengupdate AdjustAttendance dengan event_number dari Event
                    $i->update([
                        'event_number' => $e->event_number
                    ]);
                })
                ->mountUsing(fn ($arguments, $form) => $form->fill([
                    'start_date' => data_get($arguments, 'dateStr'),
                    'end_date' => data_get($arguments, 'dateStr')
                ])),
            CreateAction::make('WorkOvertime')
                ->label('Create Form Work Overtime')
                ->form([
                    Section::make('Adjust Attendance Form In Or Out')
                        ->description('Please create your adjust attendance on this form.')
                        ->schema([
                            Select::make('organization_id')
                                ->label('Choose Organization')
                                ->options(Organization::all()->pluck('name', 'id'))
                                ->searchable()
                                ->preload(),
                            Select::make('job_position_id')
                                ->label('Choose Job Position')
                                ->options(JobPosition::all()->pluck('name', 'id'))
                                ->searchable()
                                ->preload(),
                            DatePicker::make('date_spl'),
                            Toggle::make('overtime_day_status')
                                ->inline(false),
                            DatePicker::make('date_overtime_at'),
                        ])->columns([
                            'sm' => 1,
                            'xl' => 2,
                            '2xl' => 2,
                        ]),
                        Repeater::make('members')
                            ->schema([
                                Select::make('user_id')
                                    ->label('Choose User')
                                    ->options(function (Builder $query) use ($authUser) {
                                        $u = User::find(auth()->user()->id);
                                        if ($u->hasRole(['super_admin'])) {
                                            return User::get()
                                                ->pluck('name', 'id');
                                        }else{
                                            return User::with('employe')
                                                ->whereHas('employe', function ($q) use ($authUser) {
                                                    $q->where('organization_id', $authUser->employe->organization_id);
                                                })
                                                ->get()
                                                ->pluck('name', 'id');
                                        }
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                            ])
                ])
                ->action(function (array $data) {
                    $data['userid_created'] = auth()->user()->id;
                    $i = WorkOvertime::create($data);
                    $i->userMember()->createMany($data['members']);
                    // Membuat instance Event setelahnya dan mengasosiasikan dengan IzinOrSick
                    $e = Event::create([
                        'model_name' => 'App\Models\HRGA\WorkOvertime',
                        'id_form' => $i->id,
                    ]);
                    // Mengupdate IzinOrSick dengan event_number dari Event
                    $i->update([
                        'event_number' => $e->event_number
                    ]);
                })
                ->mountUsing(fn ($arguments, $form) => $form->fill([
                    'date_spl' => data_get($arguments, 'dateStr')
                ])),
        ];
    }
    public function getEvents(array $fetchInfo = []): Collection|array
    {
        $event = [];
        $absen = ScheduleGroupAttendance::query()
            ->with('time')
            ->whereHas('group_users', function($query){
                $query->where('user_id', auth()->user()->id);
            })
            ->where('date', '>=', $fetchInfo['start'])
            ->where('date', '<=', $fetchInfo['end'])
            ->get();
            foreach ($absen as $k) {
                array_push($event,[
                    "id"=>$k->id,
                    "title" => $k->time->type." Time Absence",
                    "start" => $k->date,
                    "end" => $k->date,
                    "backgroundColor"=>"#22c55e",
                    "textColor"=>"#ffffff"
                ]);
            }
            $cuti = Cuti::query()
                ->with('event', 'user')
                ->where('start_date', '>=', $fetchInfo['start'])
                ->where('end_date', '<=', $fetchInfo['end'])
                ->get();
                foreach ($cuti as $k) {
                    $nameUser = $k->user->name;
                    array_push($event,[
                        "id"=>$k->event->id,
                        "title" => "Cuti $k->category $nameUser",
                        "start" => $k->start_date,
                        "end" => $k->end_date,
                        "backgroundColor"=>"#f59e0b",
                        "textColor"=>"#ffffff"
                    ]);
                }
            $dispen = Dispen::query()
                ->with('event', 'user')
                ->where('start_date', '>=', $fetchInfo['start'])
                ->where('end_date', '<=', $fetchInfo['end'])
                ->get();
                foreach ($dispen as $k) {
                    $nameUser = $k->user->name;
                    array_push($event,[
                        "id"=>$k->event->id,
                        "title" => "Dispen $k->category $nameUser",
                        "start" => $k->start_date,
                        "end" => $k->end_date,
                        "backgroundColor"=>"#ea580c",
                        "textColor"=>"#ffffff"
                    ]);
                }
            $sick = IzinOrSick::query()
                ->with('event', 'user')
                ->where('start_date', '>=', $fetchInfo['start'])
                ->where('end_date', '<=', $fetchInfo['end'])
                ->get();
                foreach ($sick as $k) {
                    $nameUser = $k->user->name;
                    array_push($event,[
                        "id"=>$k->event->id,
                        "title" => "Permission Or Sick $k->category $nameUser",
                        "start" => $k->start_date,
                        "end" => $k->end_date,
                        "backgroundColor"=>"#dc2626",
                        "textColor"=>"#ffffff"
                    ]);
                }
            $IzinInOut = IzinInOut::query()
                ->with('event', 'user')
                ->where('date', '>=', $fetchInfo['start'])
                ->where('date', '<=', $fetchInfo['end'])
                ->get();
                foreach ($IzinInOut as $k) {
                    $nameUser = $k->user->name;
                    array_push($event,[
                        "id"=>$k->event->id,
                        "title" => "Permission In/Out $nameUser",
                        "start" => $k->date,
                        "end" => $k->date,
                        "backgroundColor"=>"#57534e",
                        "textColor"=>"#ffffff"
                    ]);
                }
            $AdjustAttendance = AdjustAttendance::query()
                ->with('event', 'user')
                ->where('date', '>=', $fetchInfo['start'])
                ->where('date', '<=', $fetchInfo['end'])
                ->get();
                foreach ($AdjustAttendance as $k) {
                    $nameUser = $k->user->name;
                    array_push($event,[
                        "id"=>$k->event->id,
                        "title" => "Adjust Absence $nameUser",
                        "start" => $k->date,
                        "end" => $k->date,
                        "backgroundColor"=>"#2563eb",
                        "textColor"=>"#ffffff"
                    ]);
                }
        return $event;
    }
}
