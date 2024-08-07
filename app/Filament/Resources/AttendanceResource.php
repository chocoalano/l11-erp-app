<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceResource\Pages;
use App\Models\Attendance;
use App\Models\TimeAttendance;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use Filament\Tables\Contracts\HasTable;


class AttendanceResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Attendance::class;

    protected static ?string $navigationIcon = 'fas-fingerprint';
    protected static ?string $label = 'Attendance';
    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Fieldset::make('Users')
                ->schema([
                    Select::make('user_id')
                        ->relationship(name: 'user', titleAttribute: 'name')
                        ->label('Choose User'),
                    Select::make('schedule_group_attendances_id')
                        ->relationship(name: 'schedule', titleAttribute: 'id')
                        ->label('Choose Schedule')
                ]),
                Fieldset::make('Attendance In')
                ->schema([
                    TextInput::make('lat')->numeric(),
                    TextInput::make('lng')->numeric(),
                    TimePicker::make('time'),
                    DatePicker::make('date'),
                ]),
                Fieldset::make('Attendance Out')
                ->schema([
                    TextInput::make('attendance.lat')->numeric(),
                    TextInput::make('attendance.lng')->numeric(),
                    TimePicker::make('attendance.time'),
                    DatePicker::make('attendance.date'),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading()
            ->heading('Attendance Records List')
            ->description('Manage attendance here.')
            ->columns([
                Tables\Columns\TextColumn::make('No')
                ->rowIndex(),
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nik')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('shift')
                    ->getStateUsing(function (Attendance $record) {
                        $time = TimeAttendance::find($record->schedule->time_attendance_id);
                        return $time ? $time->type : null;
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('schedule.date')
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')->sortable(),
                Tables\Columns\TextColumn::make('time_in')->label('Time In'),
                Tables\Columns\TextColumn::make('time_out')->label('Time Out'),
                Tables\Columns\TextColumn::make('status_in')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'late' => 'danger',
                        'unlate' => 'success',
                    })
                    ->label('Status In'),
                Tables\Columns\TextColumn::make('status_out')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'late' => 'danger',
                        'unlate' => 'success',
                    })
                    ->label('Status Out'),
            ])
            ->filters([
                DateRangeFilter::make('date')
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->mutateRecordDataUsing(function (array $data): array {
                            $out = Attendance::where('in_attendance_id', $data['id'])->first();
                            $data['attendance']['lat'] = $out->lat;
                            $data['attendance']['lng'] = $out->lng;
                            $data['attendance']['time'] = $out->time;
                            $data['attendance']['date'] = $out->date;
                            return $data;
                        })
                        ->using(function (Model $record, array $data): Model {
                            $q = Attendance::find($record->id);
                            $q->nik = $record->nik;
                            $q->schedule_group_attendances_id = $data['schedule_group_attendances_id'];
                            $q->lat = $data['lat'];
                            $q->lng = $data['lng'];
                            $q->time = $data['time'];
                            $q->date = $record->date;
                            $q->save();
                            $q->attendance()->updateOrCreate(
                                [
                                    'in_attendance_id'=> $record->id,
                                ],
                                [
                                    'nik'=> $record->nik,
                                    'schedule_group_attendances_id'=> $data['schedule_group_attendances_id'],
                                    'lat'=> $data['attendance']['lat'],
                                    'lng'=> $data['attendance']['lng'],
                                    'time'=> $data['attendance']['time'],
                                    'date'=> $record->date
                                ]
                            );
                            return $record;
                        }),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAttendances::route('/'),
        ];
    }
}
