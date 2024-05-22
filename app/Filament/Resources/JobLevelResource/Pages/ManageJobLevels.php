<?php

namespace App\Filament\Resources\JobLevelResource\Pages;

use App\Filament\Resources\JobLevelResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageJobLevels extends ManageRecords
{
    protected static string $resource = JobLevelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
