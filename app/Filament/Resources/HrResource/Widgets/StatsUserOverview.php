<?php

namespace App\Filament\Resources\HrResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class StatsUserOverview extends BaseWidget
{
    use HasWidgetShield;
    protected static ?string $pollingInterval = '10s';
    protected function getStats(): array
    {
        $count_users_total = \App\Models\User::count();
        $count_level_total = \App\Models\JobLevel::count();
        $count_position_total = \App\Models\JobPosition::count();
        $count_org_total = \App\Models\Organization::count();
        return [
            Stat::make('Users Total', $count_users_total)
                ->description("$count_users_total increase")
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),
            Stat::make('Jobs Level Total', $count_level_total)
                ->description("$count_level_total increase")
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('info'),
            Stat::make('Job Position Total', $count_position_total)
                ->description("$count_position_total increase")
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('warning'),
            Stat::make('Organization Total', $count_org_total)
                ->description("$count_org_total increase")
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('danger'),
        ];
    }
}
