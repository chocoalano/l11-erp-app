<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserAttendanceResource\Pages;
use App\Filament\Resources\UserAttendanceResource\RelationManagers;
use App\Models\InAttendance;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
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
            'index' => Pages\ManageUserAttendances::route('/'),
        ];
    }
}
