<?php

namespace App\Filament\Widgets;

use App\Models\InAttendance;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceLateChart extends ChartWidget
{
    use HasWidgetShield;
    protected static ?string $heading = 'Attendance Is Late';
    protected static ?int $sort = 1;

    protected function getData(): array
    {
        $startDayOfMonth = Carbon::now()->firstOfMonth()->format('Y-m-d');
        $endDayOfMonth = Carbon::now()->endOfMonth()->format('Y-m-d');
        $data = InAttendance::select(
            DB::raw("DATE_FORMAT(date, '%Y-%m-%d') as date"),
            DB::raw('count(*) as late_count')
        )
        ->whereBetween('date', [$startDayOfMonth, $endDayOfMonth])
        ->where('status', 'late')
        ->groupBy('date')
        ->orderBy('date', 'asc')
        ->get();
        $value = [];
        $label = [];
        foreach ($data as $k) {
            array_push($value, $k->late_count);
            array_push($label, $k->date);
        }
    
        return [
            'datasets' => [
                [
                    'label' => 'Attendance Late',
                    'data' => $value,
                ],
            ],
            'labels' => $label,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
