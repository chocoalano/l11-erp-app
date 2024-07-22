<?php

namespace App\Filament\Marketing\Resources;

use App\Filament\Marketing\Resources\CertificateResource\Pages;
use App\Filament\Marketing\Resources\CertificateResource\RelationManagers;
use App\Models\Marketing\Compro\CertificateItem;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;

class CertificateResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = CertificateItem::class;

    protected static ?string $navigationLabel = 'Certificate';
    protected static ?string $navigationIcon = 'fas-certificate';
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
                ->directory('certificates')
                ->columnSpanFull(),
                Forms\Components\TextInput::make('title')
                ->required(),
                Forms\Components\Select::make('certificate_id')
                ->label('Certificate Parent')
                ->relationship(name: 'certificate', titleAttribute: 'title')
                ->createOptionForm([
                    Forms\Components\FileUpload::make('cover_image')
                    ->image()
                    ->directory('certificates')
                    ->columnSpanFull(),
                    Forms\Components\TextInput::make('title')
                        ->required(),
                    Forms\Components\Toggle::make('active')
                    ->columnSpanFull(),
                    Forms\Components\TextArea::make('description')
                    ->columnSpanFull()
                    ->required(),
                ]),
                Forms\Components\Toggle::make('active')
                ->columnSpanFull(),
                Forms\Components\TextArea::make('description')
                ->columnSpanFull()
                ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            Tables\Columns\ImageColumn::make('cover_image'),
            Tables\Columns\TextColumn::make('certificate.title')->label('Category')->limit(35),
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
            RelationManagers\CertificateRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCertificates::route('/'),
            'create' => Pages\CreateCertificate::route('/create'),
            'edit' => Pages\EditCertificate::route('/{record}/edit'),
        ];
    }
}
