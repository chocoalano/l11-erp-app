<?php

namespace App\Filament\Resources\SystemSetup;

use App\Filament\Resources\SystemSetup\JobPositionResource\Pages;
use App\Filament\Resources\SystemSetup\JobPositionResource\RelationManagers;
use App\Models\SystemSetup\JobPosition;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class JobPositionResource extends Resource
{
    protected static ?string $model = JobPosition::class;

    protected static ?string $navigationIcon = 'fas-up-down-left-right';
    protected static ?string $navigationGroup = 'System Configurations';
    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'description'];
    }

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
                ->exporter(\App\Filament\Exports\SystemSetup\JobPositionExporter::class),
            Tables\Actions\ImportAction::make()
                ->importer(\App\Filament\Imports\SystemSetup\JobPositionImporter::class)
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
                    ->beforeReplicaSaved(function (JobPosition $replica): void {
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
            'index' => Pages\ManageJobPositions::route('/'),
        ];
    }
}
