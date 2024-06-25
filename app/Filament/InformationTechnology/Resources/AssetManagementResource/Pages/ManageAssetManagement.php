<?php

namespace App\Filament\InformationTechnology\Resources\AssetManagementResource\Pages;

use App\Filament\InformationTechnology\Resources\AssetManagementResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAssetManagement extends ManageRecords
{
    protected static string $resource = AssetManagementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
