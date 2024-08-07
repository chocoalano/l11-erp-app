<?php

namespace App\Filament\Marketing\Pages;

use App\Filament\Marketing\Widgets\Calendar;
use Filament\Pages\Page;

class AdministrationHrga extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.marketing.pages.administration-hrga';
    protected static ?string $title = 'Administration HRGA';
    protected function getHeaderWidgets(): array
    {
        return [
            Calendar::class
        ];
    }
}
