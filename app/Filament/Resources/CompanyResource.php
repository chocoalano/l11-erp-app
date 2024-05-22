<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyResource\Pages;
use App\Filament\Resources\CompanyResource\RelationManagers;
use App\Models\Company;
use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static ?string $navigationLabel = 'Company';
    protected static ?string $navigationIcon = 'fas-building';
    protected static ?string $navigationGroup = 'Settings';

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'restore',
            'restore_any',
            'replicate',
            'reorder',
            'delete',
            'delete_any',
            'force_delete',
            'force_delete_any',
            'export_excel',
            'import_excel',
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'name',
            'latitude',
            'longitude',
            'full_address',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required(),
                TextInput::make('latitude')->numeric()->required(),
                TextInput::make('longitude')->numeric()->required(),
                Textarea::make('full_address')->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('latitude')->searchable(),
                TextColumn::make('longitude')->searchable(),
                TextColumn::make('full_address')->limit(50)->searchable()
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\ForceDeleteAction::make(),
                    Tables\Actions\ReplicateAction::make()
                        ->beforeReplicaSaved(function (Company $replica): void {
                            $carbonDate = \Illuminate\Support\Carbon::now();
                            $datetime = $carbonDate->format('YmdHis');
                            $replica->name = "[new]$datetime._$replica->name";
                            $replica->nik = $datetime;
                            $replica->email = "[new]$datetime._$replica->email";
                        })->requiresConfirmation()
                        ->modalHeading('Replicate Data')
                        ->modalDescription('Are you sure you\'d like to replicate this data? This cannot be undone.')
                        ->modalSubmitActionLabel('Yes, replicate it'),
                    Tables\Actions\RestoreAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageCompanies::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
