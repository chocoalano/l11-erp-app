<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScheduleGroupAttendanceResource\Pages;
use App\Filament\Resources\ScheduleGroupAttendanceResource\RelationManagers;
use App\Models\ScheduleGroupAttendance;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker;

class ScheduleGroupAttendanceResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = ScheduleGroupAttendance::class;

    protected static ?string $navigationLabel = 'Schedule Group Attendance';
    protected static ?string $navigationIcon = 'fas-calendar';
    protected static ?string $navigationGroup = 'Attendance Config';

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'restore',
            'restore_any',
            'replicate',
            'reorder',
            'delete',
            'delete_any',
            'force_delete',
            'force_delete_any',
            'export_excel',
            'import_excel',
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'group_attendance_id', 
            'time_attendance_id', 
            'date'
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('group_attendance_id')
                    ->label('Choose group attendance')
                    ->relationship(name: 'group_attendance', titleAttribute: 'name')
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('description')
                            ->columnSpanFull(),
                    ])
                    ->required(),
                Forms\Components\Select::make('time_attendance_id')
                    ->label('Choose time attendance')
                    ->relationship(name: 'time', titleAttribute: 'type')
                    ->createOptionForm([
                        Forms\Components\TextInput::make('type')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TimePicker::make('in')
                            ->required(),
                        Forms\Components\TimePicker::make('out')
                            ->required(),
                    ])
                    ->required(),
                DateRangePicker::make('date')
                ->minDate(Carbon::now()->subMonth())
                ->format('Y-m-d')
                ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('group_attendance.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('time.type')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ManageScheduleGroupAttendances::route('/'),
        ];
    }
}
