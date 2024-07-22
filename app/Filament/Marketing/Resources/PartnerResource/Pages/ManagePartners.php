<?php

namespace App\Filament\Marketing\Resources\PartnerResource\Pages;

use App\Filament\Marketing\Resources\PartnerResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManagePartners extends ManageRecords
{
    protected static string $resource = PartnerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
