<?php

namespace App\Filament\Marketing\Resources;

use App\Filament\Marketing\Resources\PartnerResource\Pages;
use App\Filament\Marketing\Resources\PartnerResource\RelationManagers;
use App\Models\Marketing\Digital\Partner;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;

class PartnerResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Partner::class;

    protected static ?string $navigationLabel = 'Partner';
    protected static ?string $navigationIcon = 'fas-handshake';
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
                Forms\Components\FileUpload::make('image')
                ->image()
                ->directory('partner/image')
                ->columnSpanFull(),
                Forms\Components\TextInput::make('video')
                ->prefixIcon('heroicon-s-play-circle')
                ->columnSpanFull(),
                Forms\Components\Toggle::make('active')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('video')->limit(60),
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
            'index' => Pages\ManagePartners::route('/'),
        ];
    }
}
