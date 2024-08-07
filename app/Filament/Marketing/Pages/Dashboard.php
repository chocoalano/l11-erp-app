<?php

namespace App\Filament\Marketing\Pages;

use App\Filament\Marketing\Widgets\StatsDigitalMarketingOverview;
use Filament\Pages\Dashboard\Concerns\HasFiltersAction;

class Dashboard extends \Filament\Pages\Dashboard
{
    use HasFiltersAction;
 
    public function getColumns(): int|string|array
    {
        return 12;
    }
    public function getWidgets(): array
    {
        return [
            StatsDigitalMarketingOverview::class
        ];
    }
}
