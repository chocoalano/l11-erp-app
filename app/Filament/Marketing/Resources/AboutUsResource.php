<?php

namespace App\Filament\Marketing\Resources;

use App\Filament\Marketing\Resources\AboutUsResource\Pages;
use App\Filament\Marketing\Resources\AboutUsResource\RelationManagers;
use App\Models\Marketing\Digital\AboutUs;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;

class AboutUsResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = AboutUs::class;
    protected static ?string $navigationLabel = 'About Us';
    protected static ?string $navigationIcon = 'fas-building';
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
            'description',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('cover_image')
                ->image()
                ->directory('cover-about-us')
                ->required(),
                Forms\Components\Radio::make('cover_position')
                ->options([
                    'left' => 'Left',
                    'right' => 'Right'
                ])->required(),
                Forms\Components\TextInput::make('title')
                ->required(),
                Forms\Components\Toggle::make('active'),
                Forms\Components\RichEditor::make('description')
                ->columnSpanFull()
                ->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image')
                    ->sortable(),
                Tables\Columns\TextColumn::make('cover_position')
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->limit(70)
                    ->html()
                    ->sortable(),
                Tables\Columns\TextColumn::make('active')
                    ->badge()
                    ->formatStateUsing(function ($state) {
                        return $state ? 'Active' : 'Inactive';
                    })
                    ->color(fn (string $state): string => match ($state) {
                        '0' => 'success',
                        '1' => 'danger',
                    }),
                    // ->color(fn (string $state): string => match ($state) {
                    //     '0' => 'success',
                    //     '1' => 'danger',
                    // }),
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
            'index' => Pages\ManageAboutUs::route('/'),
        ];
    }
}
