<?php

namespace App\Filament\Pages;

use App\Filament\Resources\HrResource\Widgets\StatsUserOverview;
use App\Filament\Widgets\AttendanceLateChart;
use App\Filament\Widgets\AttendanceUnlateChart;
use App\Filament\Widgets\OrganizationLateChart;
use App\Filament\Widgets\OrganizationUnlateChart;
use Filament\Pages\Dashboard\Concerns\HasFiltersAction;

class Dashboard extends \Filament\Pages\Dashboard
{
    use HasFiltersAction;
    public function getWidgets(): array
    {
        return [
            StatsUserOverview::class,
            AttendanceLateChart::class,
            AttendanceUnlateChart::class,
            OrganizationLateChart::class,
            OrganizationUnlateChart::class,
        ];
    }
}
