<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TimeAttendanceResource\Pages;
use App\Filament\Resources\TimeAttendanceResource\RelationManagers;
use App\Models\TimeAttendance;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TimeAttendanceResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = TimeAttendance::class;

    protected static ?string $navigationLabel = 'Time Attendance';
    protected static ?string $navigationIcon = 'fas-clock';
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
            'type', 'in', 'out'
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('type')
                    ->unique()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TimePicker::make('in')
                    ->required(),
                Forms\Components\TimePicker::make('out')
                    ->required(),
                Forms\Components\Select::make('pattern_name')
                    ->options([
                        "production"=>"Production",
                        "warehouse"=>"Warehouse",
                        "maintenance"=>"Maintenance",
                        "office"=>"Office",
                        "customs"=>"Customs",
                    ])
                    ->required(),
                Forms\Components\Select::make('rules')
                    ->options([
                        "1"=>"First",
                        "2"=>"Second",
                        "3"=>"Thirth",
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('in')
                    ->searchable(),
                Tables\Columns\TextColumn::make('out')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pattern_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('rules')
                    ->searchable(),
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
            'index' => Pages\ManageTimeAttendances::route('/'),
        ];
    }
}
