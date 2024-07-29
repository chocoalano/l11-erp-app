<?php

namespace App\Filament\Marketing\Resources;

use App\Filament\Marketing\Resources\ValueResource\Pages;
use App\Models\Marketing\Digital\Value;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;

class ValueResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Value::class;

    protected static ?string $navigationLabel = 'Value Company';
    protected static ?string $navigationIcon = 'fas-heart';
    protected static ?string $navigationGroup = 'Company Profile';
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
            'description'
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                ->required(),
                Forms\Components\TextInput::make('subtitle')
                ->required(),
                Forms\Components\Textarea::make('description')
                ->required(),
                Forms\Components\Toggle::make('active')->required(),
                Forms\Components\FileUpload::make('image')
                ->image()
                ->directory('value')
                ->required(),
                Forms\Components\TextInput::make('icon')
                ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('title')->limit(35),
                Tables\Columns\TextColumn::make('subtitle')->limit(35),
                Tables\Columns\TextColumn::make('description')->limit(35),
                Tables\Columns\TextColumn::make('icon'),
                Tables\Columns\TextColumn::make('active')
                    ->badge()
                    ->label('Status')
                    ->formatStateUsing(function ($state) {
                        return $state ? 'Active':'Inactive';
                    })
                    ->color(fn (string $state): string => match ($state) {
                        '0' => 'danger',
                        '1' => 'success',
                    }),
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
            'index' => Pages\ManageValues::route('/'),
        ];
    }
}
