<?php

namespace App\Filament\InformationTechnology\Resources;

use App\Filament\InformationTechnology\Resources\SupportItResource\Pages;
use App\Filament\InformationTechnology\Resources\SupportItResource\RelationManagers;
use App\Models\Task;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SupportItResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-s-user-group';
    protected static ?string $navigationLabel = 'Support Orders';
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
            'title',
            'description',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title'),
                Forms\Components\Textarea::make('description'),
                Forms\Components\Toggle::make('urgent'),
                Forms\Components\TextInput::make('progress')->numeric(),
                Forms\Components\Select::make('status')
                ->options([
                    'Low' => 'Low',
                    'High' => 'High',
                    'Medium' => 'Medium',
                ]),
                Forms\Components\Select::make('user_id')
                    ->label('Users Participan')
                    ->options(\App\Models\User::all()->pluck('name', 'id'))
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('No.')->rowIndex(),
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('description'),
                Tables\Columns\TextColumn::make('progress'),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('user_id'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Periority')
                    ->options([
                        'Low' => 'Low',
                        'Medium' => 'Medium',
                        'Hight' => 'Hight',
                    ])
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
            'index' => Pages\ManageSupportIts::route('/'),
        ];
    }
}
