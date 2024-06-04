<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GroupAttendanceResource\Pages;
use App\Filament\Resources\GroupAttendanceResource\RelationManagers;
use App\Models\GroupAttendance;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GroupAttendanceResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = GroupAttendance::class;

    protected static ?string $navigationLabel = 'Group Attendance';
    protected static ?string $navigationIcon = 'fas-people-group';
    protected static ?string $navigationGroup = 'Attendance Config';

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'replicate',
            'delete',
            'delete_any',
            'export_excel',
            'import_excel',
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'name', 'description'
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('nik')
                    ->label('User members')
                    ->options(\App\Models\User::all()->pluck('name', 'nik'))
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->using(function (Model $record, array $data): Model {
                    $q = GroupAttendance::find($record->id);
                    $q->name = $data['name'];
                    $q->description = $data['description'];
                    $q->save();
                    $q->user()->sync($data['nik']);
                    return $q;
                }),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ManageGroupAttendances::route('/'),
        ];
    }
}
