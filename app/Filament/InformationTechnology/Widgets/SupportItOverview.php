<?php

namespace App\Filament\InformationTechnology\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SupportItOverview extends BaseWidget
{
    use HasWidgetShield;
    protected static ?string $pollingInterval = '10s';
    protected function getStats(): array
    {
        $count_support_urgent_total = \App\Models\Task::where('urgent', true)->count();
        $count_support_total_isurgent = \App\Models\Task::where('urgent', true)->where('progress', '<', 100)->count();
        $count_support_unurgent_total = \App\Models\Task::where('urgent', false)->count();
        $count_support_total_isunurgent = \App\Models\Task::where('urgent', false)->where('progress', '<', 100)->count();
        $count_support_total_compleated = \App\Models\Task::where('progress', 100)->count();
        $count_support_total_uncompleated = \App\Models\Task::where('progress', '<', 100)->count();
        $count_all_assets = \App\Models\AssetManagement::count();
        return [
            Stat::make('Support Urgent Data', "$count_support_urgent_total All Data")
                ->description("$count_support_total_isurgent Support Uncompleated")
                ->descriptionIcon('heroicon-o-information-circle'),
            Stat::make('Support Unurgent Data', "$count_support_unurgent_total All Data")
                ->description("$count_support_total_isunurgent Support Uncompleated")
                ->descriptionIcon('heroicon-o-information-circle'),
            Stat::make('Support Is Compleated Data', "$count_support_total_compleated All Data")
                ->description("$count_support_total_uncompleated Support Uncompleated")
                ->descriptionIcon('heroicon-o-information-circle'),
            Stat::make('Asset Data', "$count_all_assets All Data")
                ->description("$count_all_assets All Assets")
                ->descriptionIcon('heroicon-o-information-circle'),
        ];
    }
}
