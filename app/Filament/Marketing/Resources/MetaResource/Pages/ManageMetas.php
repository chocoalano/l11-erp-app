<?php

namespace App\Filament\Marketing\Resources\MetaResource\Pages;

use App\Filament\Marketing\Resources\MetaResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageMetas extends ManageRecords
{
    protected static string $resource = MetaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
