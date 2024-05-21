<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Navigation\NavigationItem;
use Filament\Navigation\MenuItem;
use App\Filament\InformationTechnology\Pages\Profile;

class InformationTechnologyPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('it')
            ->path('it')
            ->brandName('E-SAS')
            ->brandLogo(asset('images/logo.svg'))
            ->login()
            ->passwordReset()
            ->emailVerification()
            ->spa()
            ->sidebarCollapsibleOnDesktop()
            ->viteTheme('resources/css/filament/humanResources/theme.css')
            ->databaseNotifications()
            ->userMenuItems([
                MenuItem::make()
                    ->label('Profile')
                    ->url(fn (): string => Profile::getUrl())
                    ->icon('fas-user')
            ])
            ->navigationItems([
                NavigationItem::make('Tasks')
                    ->url('hr/tasks-kanban-board')
                    ->icon('heroicon-o-presentation-chart-line')
            ])
            ->databaseNotifications()
            ->plugins([
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make()
                ->gridColumns([
                    'default' => 2,
                    'sm' => 1
                ])
                ->sectionColumnSpan(1)
                ->checkboxListColumns([
                    'default' => 1,
                    'sm' => 2,
                    'lg' => 3,
                ])
                ->resourceCheckboxListColumns([
                    'default' => 1,
                    'sm' => 2,
                ]),
            ])
            ->discoverResources(in: app_path('Filament/InformationTechnology/Resources'), for: 'App\\Filament\\InformationTechnology\\Resources')
            ->discoverPages(in: app_path('Filament/InformationTechnology/Pages'), for: 'App\\Filament\\InformationTechnology\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/InformationTechnology/Widgets'), for: 'App\\Filament\\InformationTechnology\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
