<?php

namespace App\Providers\Filament;

use App\Filament\Auth\Login;
use App\Filament\Pages\AdministrationGa;
use App\Filament\Pages\Dashboard;
use App\Filament\Resources\HrResource\Widgets\StatsUserOverview;
use App\Filament\Resources\HrResource\Widgets\UsersChart;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Navigation\MenuItem;
use App\Filament\Pages\Profile;
use App\Filament\Widgets\AttendanceLateChart;
use App\Filament\Widgets\AttendanceUnlateChart;
use App\Filament\Widgets\OrganizationLateChart;
use App\Filament\Widgets\OrganizationUnlateChart;

class HumanResourcesPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('hr')
            ->path('hr')
            ->brandName('E-SAS')
            ->brandLogo(asset('images/logo.svg'))
            ->login(Login::class)
            ->passwordReset()
            ->emailVerification()
            ->spa()
            ->sidebarCollapsibleOnDesktop()
            ->viteTheme('resources/css/filament/humanResources/theme.css')
            // ->topNavigation()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->userMenuItems([
                MenuItem::make()
                    ->label('Profile')
                    ->url(fn (): string => Profile::getUrl())
                    ->icon('fas-user')
            ])
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->pages([
                Dashboard::class,
                AdministrationGa::class,
            ])
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
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
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
