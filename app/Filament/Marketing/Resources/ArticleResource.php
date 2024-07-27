<?php

namespace App\Filament\Marketing\Resources;

use App\Filament\Marketing\Resources\ArticleResource\Pages;
use App\Filament\Marketing\Resources\ArticleResource\RelationManagers;
use App\Models\Marketing\Digital\Article;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;

class ArticleResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Article::class;

    protected static ?string $navigationLabel = 'Articles';
    protected static ?string $navigationIcon = 'fas-newspaper';
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
                ->directory('cover-article')
                ->columnSpanFull()
                ->required(),
                Forms\Components\TextInput::make('title')
                ->required(),
                Forms\Components\TagsInput::make('tags')
                ->required(),
                Forms\Components\Select::make('article_category_id')
                ->label('Category')
                ->relationship(name: 'category', titleAttribute: 'name')
                ->createOptionForm([
                    Forms\Components\TextInput::make('name')
                        ->required(),
                ]),
                Forms\Components\Toggle::make('active'),
                Forms\Components\TagsInput::make('meta_keywords')
                ->required(),
                Forms\Components\TextInput::make('meta_title'),
                Forms\Components\Textarea::make('meta_descriptions')
                ->rows(10)
                ->cols(20)
                ->columnSpanFull(),
                Forms\Components\RichEditor::make('content')
                ->columnSpanFull()
                ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image'),
                Tables\Columns\TextColumn::make('user.name')->searchable(),
                Tables\Columns\TextColumn::make('category.name')->searchable(),
                Tables\Columns\TextColumn::make('title')->limit(30)->searchable(),
                Tables\Columns\TagsColumn::make('tags')->searchable(),
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
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
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
            RelationManagers\CategoryRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'edit' => Pages\EditArticle::route('/{record}/edit'),
        ];
    }
}
