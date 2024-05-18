<?php

namespace App\Filament\Resources\SystemSetup\OrganizationResource\Pages;

use App\Filament\Resources\SystemSetup\OrganizationResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageOrganizations extends ManageRecords
{
    protected static string $resource = OrganizationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
