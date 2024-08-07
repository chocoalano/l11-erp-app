<?php

namespace App\Providers;

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Illuminate\Support\ServiceProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use BezhanSalleh\PanelSwitch\PanelSwitch;
use Filament\Support\Facades\FilamentIcon;

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
        FilamentIcon::register([
            'panels::sidebar.collapse-button' => 'heroicon-s-bars-4',
            'panels::sidebar.expand-button' => 'heroicon-s-bars-4',
        ]);
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
                'marketing' => asset('images/svg/marketing.svg'),
            ], $asImage = true)
            ->iconSize(16)
            ->labels([
                'hr' => 'HRIS',
                'it' => 'ITS',
                'marketing' => 'MIS',
            ]);
        });
        FilamentShield::configurePermissionIdentifierUsing(
            fn($resource) => str($resource::getModel())
                ->afterLast('\\')
                ->lower()
                ->toString()
        );
    }
}
