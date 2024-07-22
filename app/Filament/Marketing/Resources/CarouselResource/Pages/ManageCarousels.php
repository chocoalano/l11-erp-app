<?php

namespace App\Filament\Marketing\Resources\CarouselResource\Pages;

use App\Filament\Marketing\Resources\CarouselResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageCarousels extends ManageRecords
{
    protected static string $resource = CarouselResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
