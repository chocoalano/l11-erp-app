<?php

namespace App\Filament\Marketing\Resources;

use App\Filament\Marketing\Resources\AwardResource\Pages;
use App\Filament\Marketing\Resources\AwardResource\RelationManagers;
use App\Models\Marketing\Digital\AwardItem;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;

class AwardResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = AwardItem::class;

    protected static ?string $navigationLabel = 'Awards';
    protected static ?string $navigationIcon = 'fas-award';
    protected static ?string $navigationGroup = 'Company Profile';
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
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'title',
            'slug',
            'content',
            'tags',
            'active',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('cover_image')
                ->image()
                ->directory('cover-awards')
                ->columnSpanFull(),
                Forms\Components\TextInput::make('title')
                ->required(),
                Forms\Components\Select::make('award_id')
                ->label('Award Parent')
                ->relationship(name: 'award', titleAttribute: 'title')
                ->createOptionForm([
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
                ]),
                Forms\Components\Toggle::make('active')
                ->columnSpanFull(),
                Forms\Components\Textarea::make('description')
                ->columnSpanFull()
                ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            Tables\Columns\ImageColumn::make('cover_image'),
            Tables\Columns\TextColumn::make('award.title')->limit(35),
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

    public static function getRelations(): array
    {
        return [
            RelationManagers\AwardRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAwards::route('/'),
            'create' => Pages\CreateAward::route('/create'),
            'edit' => Pages\EditAward::route('/{record}/edit'),
        ];
    }
}
