<?php
 
namespace App\Filament\InformationTechnology\Pages;

use App\Filament\InformationTechnology\Widgets\SupportItOverview;
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
            SupportItOverview::class,
        ];
    }
}