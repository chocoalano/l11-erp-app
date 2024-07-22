<?php

namespace App\Filament\Marketing\Resources\ReasonResource\Pages;

use App\Filament\Marketing\Resources\ReasonResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageReasons extends ManageRecords
{
    protected static string $resource = ReasonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
