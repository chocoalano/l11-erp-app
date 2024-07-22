<?php

namespace App\Filament\Marketing\Resources;

use App\Filament\Marketing\Resources\ProductResource\Pages;
use App\Filament\Marketing\Resources\ProductResource\RelationManagers;
use Illuminate\Support\Str;
use App\Filament\Marketing\Resources\ProductResource\RelationManagers\ParentRelationManager;
use App\Models\Marketing\Compro\Product;
use App\Models\Marketing\Compro\ProductItem;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = ProductItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Product';
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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Fieldset::make('Content')
                    ->schema([
                        Forms\Components\Select::make('product_id')
                            ->relationship(name: 'parent', titleAttribute: 'title')
                            ->createOptionForm([
                                Forms\Components\Fieldset::make('Content')
                                    ->schema([
                                        Forms\Components\FileUpload::make('cover_image')
                                            ->image()
                                            ->directory('products/parent')
                                            ->required()
                                            ->columnSpanFull(),
                                        Forms\Components\TextInput::make('title')
                                            ->required(),
                                        Forms\Components\Toggle::make('active')
                                            ->onIcon('heroicon-o-check-badge')
                                            ->offIcon('heroicon-c-x-mark'),
                                        Forms\Components\Textarea::make('description')
                                            ->required()
                                            ->columnSpanFull(),
                                    ]),
                                Forms\Components\Fieldset::make('SEO Meta Data')
                                    ->schema([
                                        Forms\Components\TextInput::make('meta_keywords')
                                            ->required(),
                                        Forms\Components\TextInput::make('meta_title')
                                            ->required(),
                                        Forms\Components\Textarea::make('meta_descriptions')
                                            ->required()
                                            ->columnSpanFull(),
                                    ])
                            ])
                            ->createOptionUsing(function (array $data): int {
                                $data['slug'] = Str::slug($data['meta_descriptions']);
                                return Product::create($data)->getKey();
                            }),
                        Forms\Components\FileUpload::make('cover_image')
                            ->image()
                            ->directory('products/parent')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('title')
                            ->required(),
                        Forms\Components\Toggle::make('active')
                            ->onIcon('heroicon-o-check-badge')
                            ->offIcon('heroicon-c-x-mark'),
                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Fieldset::make('SEO Meta Data')
                    ->schema([
                        Forms\Components\TextInput::make('meta_keywords')
                            ->required(),
                        Forms\Components\TextInput::make('meta_title')
                            ->required(),
                        Forms\Components\Textarea::make('meta_descriptions')
                            ->required()
                            ->columnSpanFull(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image'),
                Tables\Columns\TextColumn::make('title')->limit(35),
                Tables\Columns\TextColumn::make('description')->limit(35),
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
            ->actions([
                Tables\Actions\EditAction::make(),
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
            ParentRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
