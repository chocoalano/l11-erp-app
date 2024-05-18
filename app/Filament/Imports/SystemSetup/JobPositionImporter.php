<?php

namespace App\Filament\Imports\SystemSetup;

use App\Models\SystemSetup\JobPosition;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class JobPositionImporter extends Importer
{
    protected static ?string $model = JobPosition::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->rules(['max:100']),
            ImportColumn::make('description'),
        ];
    }

    public function resolveRecord(): ?JobPosition
    {
        // return JobPosition::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new JobPosition();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your job position import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
