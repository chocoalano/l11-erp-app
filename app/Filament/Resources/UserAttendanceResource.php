<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserAttendanceResource\Pages;
use App\Filament\Resources\UserAttendanceResource\RelationManagers;
use App\Models\InAttendance;
use App\Models\OutAttendance;
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

class UserAttendanceResource extends Resource
{
    protected static ?string $model = InAttendance::class;

    protected static ?string $navigationIcon = 'fas-fingerprint';
    protected static ?string $label = 'Attendance';

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
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nik')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('schedule.date')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date'),
                Tables\Columns\TextColumn::make('time')->label('In'),
                Tables\Columns\TextColumn::make('attendance.time')->label('Out'),
            ])
            ->filters([
                DateRangeFilter::make('created_at')
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->mutateRecordDataUsing(function (array $data): array {
                    $out = OutAttendance::where('in_attendance_id', $data['id'])->first();
                    $data['attendance']['lat'] = $out->lat;
                    $data['attendance']['lng'] = $out->lng;
                    $data['attendance']['time'] = $out->time;
                    $data['attendance']['date'] = $out->date;
                    return $data;
                })
                ->using(function (Model $record, array $data): Model {
                    $q = InAttendance::find($record->id);
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
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageUserAttendances::route('/'),
        ];
    }
}
