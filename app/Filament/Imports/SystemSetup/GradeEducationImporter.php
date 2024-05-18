<?php

namespace App\Filament\Imports\SystemSetup;

use App\Models\SystemSetup\GradeEducation;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class GradeEducationImporter extends Importer
{
    protected static ?string $model = GradeEducation::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->rules(['max:100']),
        ];
    }

    public function resolveRecord(): ?GradeEducation
    {
        // return GradeEducation::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new GradeEducation();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your grade education import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
