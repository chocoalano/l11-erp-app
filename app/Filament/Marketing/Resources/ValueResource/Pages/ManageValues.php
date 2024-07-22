<?php

namespace App\Filament\Marketing\Resources\ValueResource\Pages;

use App\Filament\Marketing\Resources\ValueResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageValues extends ManageRecords
{
    protected static string $resource = ValueResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
