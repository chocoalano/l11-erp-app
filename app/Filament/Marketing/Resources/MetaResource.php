<?php

namespace App\Filament\Marketing\Resources;

use App\Filament\Marketing\Resources\MetaResource\Pages;
use App\Models\Marketing\Digital\Meta;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;

class MetaResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Meta::class;

    protected static ?string $navigationLabel = 'Meta SEO';
    protected static ?string $navigationIcon = 'fas-globe';
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
            'page',
            'meta_descriptions',
            'meta_keywords',
            'meta_title',
            'meta_type',
            'meta_site_name'
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('page')
                ->options([
                    'index' => 'Homepage',
                    'about' => 'Tentang Kami',
                    'product' => 'Produk',
                    'awards' => 'Penghargaan',
                    'certificate' => 'Sertifikat',
                    'partner' => 'Kerjasama',
                ])
                ->required(),
                TagsInput::make('meta_keywords')
                ->required(),
                TextInput::make('meta_title')
                ->required(),
                TextInput::make('meta_type')
                ->required(),
                TextInput::make('meta_site_name')
                ->required(),
                Toggle::make('active'),
                Textarea::make('meta_descriptions')
                ->rows(10)
                ->cols(20)
                ->columnSpanFull()
                ->required(),
                FileUpload::make('meta_image')
                ->image()
                ->directory('meta')
                ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('meta_image'),
                Tables\Columns\TextColumn::make('page')->limit(35),
                Tables\Columns\TextColumn::make('meta_keywords')->limit(35),
                Tables\Columns\TextColumn::make('meta_title')->limit(35),
                Tables\Columns\TextColumn::make('meta_type')->limit(35),
                Tables\Columns\TextColumn::make('meta_site_name')->limit(35),
                Tables\Columns\TextColumn::make('meta_descriptions')->limit(35),
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
            'index' => Pages\ManageMetas::route('/'),
        ];
    }
}
