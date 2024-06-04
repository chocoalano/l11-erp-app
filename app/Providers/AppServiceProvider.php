<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use BezhanSalleh\PanelSwitch\PanelSwitch;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        FilamentColor::register([
            'danger' => Color::Red,
            'gray' => Color::Zinc,
            'info' => Color::Blue,
            'primary' => Color::hex('#079246'),
            'success' => Color::Green,
            'warning' => Color::Amber,
        ]);
        PanelSwitch::configureUsing(function (PanelSwitch $panelSwitch) {
            $panelSwitch
            ->modalWidth('sm')
            ->slideOver()
            ->modalHeading('Available Panels System Integrated')
            ->icons([
                'hr' => asset('images/svg/hrd.svg'),
                'it' => asset('images/svg/it.svg'),
            ], $asImage = true)
            ->iconSize(16)
            ->labels([
                'hr' => 'Human Resources Information System (HRIS)',
                'it' => 'Information Technology System (HTS)',
            ]);
        });
    }
}
