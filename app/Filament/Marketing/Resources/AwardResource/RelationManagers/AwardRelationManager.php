<?php

namespace App\Filament\Marketing\Resources\AwardResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AwardRelationManager extends RelationManager
{
    protected static string $relationship = 'award';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\FileUpload::make('cover_image')
                    ->image()
                    ->directory('cover-awards')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('title')
                        ->required(),
                Forms\Components\Toggle::make('active')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull()
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image'),
                Tables\Columns\TextColumn::make('title')->limit(35),
                Tables\Columns\TextColumn::make('active')
                    ->badge()
                    ->label('Status')
                    ->formatStateUsing(function ($state) {
                        return $state ? 'Active' : 'Inactive';
                    })
                    ->color(fn (string $state): string => match ($state) {
                        '0' => 'success',
                        '1' => 'danger',
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
}
