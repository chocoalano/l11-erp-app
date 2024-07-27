<?php

namespace App\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class OrganizationUnlateChart extends ChartWidget
{
    use HasWidgetShield;
    protected static string $color = 'primary';
    protected static ?string $heading = 'Departments with the most unlate absences';
    protected static ?int $sort = 1;

    protected function getData(): array
    {
        $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
        $endOfMonth = Carbon::now()->startOfMonth()->addMonth()->toDateString();

        $data = DB::table('organizations as o')
            ->join('u_employes as ue', 'o.id', '=', 'ue.organization_id')
            ->join('users as u', 'ue.user_id', '=', 'u.id')
            ->join('attendances as ia', 'u.nik', '=', 'ia.nik')
            ->select(
                'o.id as organization_id',
                'o.name as organization_name',
                DB::raw('COUNT(ia.id) as total_late')
            )
            ->where('ia.status_in', 'unlate')
            ->whereBetween('ia.date', [$startOfMonth, $endOfMonth])
            ->groupBy('o.id', 'o.name')
            ->get();
        $value = [];
        $label = [];
        foreach ($data as $k) {
            array_push($value, $k->total_late);
            array_push($label, $k->organization_name);
        }
    
        return [
            'datasets' => [
                [
                    'label' => 'Attendance Unlate',
                    'data' => $value,
                ],
            ],
            'labels' => $label,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
