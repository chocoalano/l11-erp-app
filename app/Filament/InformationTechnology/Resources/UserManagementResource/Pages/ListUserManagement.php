<?php

namespace App\Filament\InformationTechnology\Resources\UserManagementResource\Pages;

use App\Filament\InformationTechnology\Resources\UserManagementResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUserManagement extends ListRecords
{
    protected static string $resource = UserManagementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
