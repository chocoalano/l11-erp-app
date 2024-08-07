<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\Calendar;
use Filament\Pages\Page;

class AdministrationGa extends Page
{

    protected static string $view = 'filament.pages.administration-ga';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $title = 'Administration HRGA';
    protected function getHeaderWidgets(): array
    {
        return [
            Calendar::class
        ];
    }
}
