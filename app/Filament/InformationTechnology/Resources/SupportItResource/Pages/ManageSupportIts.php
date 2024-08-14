<?php

namespace App\Filament\InformationTechnology\Resources\SupportItResource\Pages;

use App\Filament\InformationTechnology\Resources\SupportItResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageSupportIts extends ManageRecords
{
    protected static string $resource = SupportItResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
