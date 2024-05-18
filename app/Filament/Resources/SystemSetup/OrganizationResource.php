<?php

namespace App\Filament\Resources\SystemSetup;

use App\Filament\Resources\SystemSetup\OrganizationResource\Pages;
use App\Filament\Resources\SystemSetup\OrganizationResource\RelationManagers;
use App\Models\SystemSetup\Organization;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrganizationResource extends Resource
{
    protected static ?string $model = Organization::class;

    protected static ?string $navigationIcon = 'fas-people-roof';

    protected static ?string $navigationGroup = 'System Configurations';

    public static function getGloballySearchableAttributes(): array
    {
        return ['name','description'];
    }

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
                Forms\Components\Section::make()->schema([
                    Forms\Components\TextInput::make('name')->maxLength(100)->required(),
                    Forms\Components\Textarea::make('description')->required(),
                ])->columns(['sm' => 1,'xl' => 1,'2xl' => 1])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                Tables\Actions\ExportAction::make()
                    ->exporter(\App\Filament\Exports\SystemSetup\OrganizationExporter::class),
                Tables\Actions\ImportAction::make()
                    ->importer(\App\Filament\Imports\SystemSetup\OrganizationImporter::class)
            ])
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('description')->searchable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable(),
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
                        ->beforeReplicaSaved(function (Organization $replica): void {
                            $carbonDate = \Illuminate\Support\Carbon::now();
                            $datetime = $carbonDate->format('YmdHis');
                            $replica->name = "[new]$datetime._$replica->name";
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
            'index' => Pages\ManageOrganizations::route('/'),
        ];
    }
}
