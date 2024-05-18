<?php

namespace App\Filament\Resources\SystemSetup;

use App\Filament\Resources\SystemSetup\GradeEducationResource\Pages;
use App\Filament\Resources\SystemSetup\GradeEducationResource\RelationManagers;
use App\Models\SystemSetup\GradeEducation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GradeEducationResource extends Resource
{
    protected static ?string $model = GradeEducation::class;

    protected static ?string $navigationIcon = 'fas-school';
    protected static ?string $navigationGroup = 'System Configurations';

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
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
                Forms\Components\TextInput::make('name')->maxLength(100)->required()
            ])->columns(['sm' => 1,'xl' => 1,'2xl' => 1])
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->headerActions([
            Tables\Actions\ExportAction::make()
                ->exporter(\App\Filament\Exports\SystemSetup\GradeEducationExporter::class)
                ->icon('fas-file-export'),
            Tables\Actions\ImportAction::make()
                ->importer(\App\Filament\Imports\SystemSetup\GradeEducationImporter::class)
                ->icon('fas-file-import')
        ])
        ->columns([
            Tables\Columns\TextColumn::make('name')->searchable(),
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
                    ->beforeReplicaSaved(function (GradeEducation $replica): void {
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
            'index' => Pages\ManageGradeEducation::route('/'),
        ];
    }
}
