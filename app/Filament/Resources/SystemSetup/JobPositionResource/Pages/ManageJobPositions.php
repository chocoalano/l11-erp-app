<?php

namespace App\Filament\Resources\SystemSetup\JobPositionResource\Pages;

use App\Filament\Resources\SystemSetup\JobPositionResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageJobPositions extends ManageRecords
{
    protected static string $resource = JobPositionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
