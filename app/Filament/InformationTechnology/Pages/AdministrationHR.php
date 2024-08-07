<?php

namespace App\Filament\InformationTechnology\Pages;

use App\Filament\InformationTechnology\Widgets\CalendarEventWidgets;
use Filament\Pages\Page;

class AdministrationHR extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $title = 'Administration HRGA';
    protected static string $view = 'filament.information-technology.pages.administration-h-r';
    protected function getHeaderWidgets(): array
    {
        return [
            CalendarEventWidgets::class
        ];
    }
}
