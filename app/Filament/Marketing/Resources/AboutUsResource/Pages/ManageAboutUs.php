<?php

namespace App\Filament\Marketing\Resources\AboutUsResource\Pages;

use App\Filament\Marketing\Resources\AboutUsResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAboutUs extends ManageRecords
{
    protected static string $resource = AboutUsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
