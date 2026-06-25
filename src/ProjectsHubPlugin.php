<?php

declare(strict_types=1);

namespace YezzMedia\UserProjects;

use Filament\Contracts\Plugin;
use Filament\Panel;
use YezzMedia\UserProjects\Pages\ProjectsCreatePage;
use YezzMedia\UserProjects\Pages\ProjectsOverviewPage;
use YezzMedia\UserProjects\Pages\ProjectsPermissionsPage;
use YezzMedia\UserProjects\Pages\ProjectsSettingsPage;
use YezzMedia\UserProjects\Pages\ProjectsStatsPage;

final class ProjectsHubPlugin implements Plugin
{
    public function getId(): string
    {
        return 'user-projects';
    }

    public function register(Panel $panel): void
    {
        $panel->pages([
            ProjectsOverviewPage::class,
            ProjectsCreatePage::class,
            ProjectsStatsPage::class,
            ProjectsSettingsPage::class,
            ProjectsPermissionsPage::class,
        ]);
    }

    public function boot(Panel $panel): void {}
}
