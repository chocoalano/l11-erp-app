<?php

namespace App\Filament\Imports\SystemSetup;

use App\Models\SystemSetup\Branch;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class BranchImporter extends Importer
{
    protected static ?string $model = Branch::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->rules(['max:100']),
            ImportColumn::make('latitude')
                ->rules(['max:100']),
            ImportColumn::make('longitude')
                ->rules(['max:100']),
            ImportColumn::make('full_address'),
        ];
    }

    public function resolveRecord(): ?Branch
    {
        // return Branch::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new Branch();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your branch import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
