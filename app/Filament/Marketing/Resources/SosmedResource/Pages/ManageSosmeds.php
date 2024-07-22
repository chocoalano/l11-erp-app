<?php

namespace App\Filament\Marketing\Resources\SosmedResource\Pages;

use App\Filament\Marketing\Resources\SosmedResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageSosmeds extends ManageRecords
{
    protected static string $resource = SosmedResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
