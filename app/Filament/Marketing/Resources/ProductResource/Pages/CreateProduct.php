<?php

namespace App\Filament\Marketing\Resources\ProductResource\Pages;

use App\Filament\Marketing\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;
}
