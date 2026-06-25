<?php

declare(strict_types=1);

namespace YezzMedia\UserProjects;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use YezzMedia\UserProjects\Pages\ProjectsCreatePage;
use YezzMedia\UserProjects\Pages\ProjectsOverviewPage;
use YezzMedia\UserProjects\Pages\ProjectsPermissionsPage;
use YezzMedia\UserProjects\Pages\ProjectsSettingsPage;
use YezzMedia\UserProjects\Pages\ProjectsStatsPage;

final class UserProjectsPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('projects')
            ->path('hub')
            ->login()
            ->authGuard('web')
            ->navigation(false)
            ->viteTheme(config('user-projects.panel.theme'))
            ->pages([
                ProjectsOverviewPage::class,
                ProjectsCreatePage::class,
                ProjectsStatsPage::class,
                ProjectsSettingsPage::class,
                ProjectsPermissionsPage::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ], isPersistent: true);
    }
}
