<?php

namespace App\Filament\Marketing\Resources;

use App\Filament\Marketing\Resources\ReasonResource\Pages;
use App\Filament\Marketing\Resources\ReasonResource\RelationManagers;
use App\Models\Marketing\Digital\Reason;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;

class ReasonResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Reason::class;

    protected static ?string $navigationLabel = 'Reasons Choose Me';
    protected static ?string $navigationIcon = 'heroicon-c-information-circle';
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
                Forms\Components\FileUpload::make('cover_image')
                    ->image()
                    ->directory('reasons/cover')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('certification_image')
                    ->image()
                    ->directory('reasons/certificate')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('title')
                    ->required(),
                Forms\Components\Toggle::make('active'),
                Forms\Components\RichEditor::make('description')
                    ->columnSpanFull()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image'),
                Tables\Columns\ImageColumn::make('certification_image'),
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
            'index' => Pages\ManageReasons::route('/'),
        ];
    }
}
