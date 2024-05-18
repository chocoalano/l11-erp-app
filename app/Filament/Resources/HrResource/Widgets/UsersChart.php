<?php

namespace App\Filament\Resources\HrResource\Widgets;

use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class UsersChart extends ChartWidget
{
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Chart Users Sign In';

    protected function getData(): array
    {
        $data = Trend::model(\App\Models\User::class)
        ->between(
            start: now()->startOfYear(),
            end: now()->endOfYear(),
        )
        ->perDay()
        ->count();
        return [
            'datasets' => [
                [
                    'label' => 'Users Sign In',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
