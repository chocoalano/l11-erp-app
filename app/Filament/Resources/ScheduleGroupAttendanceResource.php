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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;

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
            'delete',
            'delete_any',
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
            ->groups([
                Group::make('user.nik')
                    ->label('NIK Users')
                    ->collapsible(),
                Group::make('user.name')
                    ->label('Name Users')
                    ->collapsible(),
                Group::make('time.type')
                    ->label('Time Type')
                    ->collapsible(),
            ])
            ->paginated([10, 25, 50, 100, 'all'])
            ->columns([
                Tables\Columns\TextColumn::make('user.nik')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('time.type')
                    ->searchable()
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
                DateRangeFilter::make('date'),
                SelectFilter::make('filterByGroupAttendance')
                ->relationship('group_attendance', 'name')
                ->searchable()
                ->preload()

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
