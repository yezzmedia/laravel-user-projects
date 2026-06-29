<?php

declare(strict_types=1);

namespace YezzMedia\UserProjects;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use YezzMedia\Dashboard\Navigation\DashboardNavigationItem;
use YezzMedia\Dashboard\Navigation\DashboardNavigationRegistry;
use YezzMedia\Dashboard\Support\HubExtensionRegistry;
use YezzMedia\Foundation\Support\PlatformPackageRegistrar;
use YezzMedia\UserProjects\Actions\CreateRoleAction;
use YezzMedia\UserProjects\Actions\DeleteRoleAction;
use YezzMedia\UserProjects\Actions\UpdateRoleAction;
use YezzMedia\UserProjects\Filament\Widgets\ProjectsDashboardWidget;
use YezzMedia\UserProjects\Support\InstalledAddonRegistry;
use YezzMedia\UserProjects\Support\ProjectAddonManager;
use YezzMedia\UserProjects\Support\ProjectManager;
use YezzMedia\UserProjects\Support\ProjectMemberManager;
use YezzMedia\UserProjects\Support\ProjectNavigationManager;
use YezzMedia\UserProjects\Support\ProjectRoleManager;
use YezzMedia\UserProjects\Support\ProjectStatsService;
use YezzMedia\UserProjects\Support\ProjectStoreSetup;

class UserProjectsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-user-projects')
            ->hasConfigFile('user-projects')
            ->hasMigration('0001_create_projects_table')
            ->hasMigration('0002_create_project_members_table')
            ->hasMigration('0003_create_project_roles_table')
            ->hasMigration('0004_create_project_addon_activations_table')
            ->hasMigration('0005_create_installed_addons_table')
            ->hasMigration('0006_create_project_invitations_table')
            ->hasMigration('0007_create_project_activities_table')
            ->hasMigration('0008_add_photo_to_projects_table')
            ->hasViews();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(ProjectStoreSetup::class);
        $this->app->singleton(ProjectManager::class);
        $this->app->singleton(ProjectMemberManager::class);
        $this->app->singleton(ProjectNavigationManager::class);
        $this->app->singleton(ProjectRoleManager::class);
        $this->app->singleton(ProjectStatsService::class);
        $this->app->singleton(ProjectAddonManager::class);
        $this->app->singleton(InstalledAddonRegistry::class);

        $this->app->singleton(ProjectsDashboardWidget::class);

        $this->app->singleton(CreateRoleAction::class);
        $this->app->singleton(UpdateRoleAction::class);
        $this->app->singleton(DeleteRoleAction::class);

        if (class_exists(HubExtensionRegistry::class)) {
            $this->app->make(HubExtensionRegistry::class)
                ->register(ProjectsHubPlugin::class);
        }
    }

    public function packageBooted(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                $this->package->basePath('/../resources/lang') => $this->app->langPath('vendor/laravel-user-projects'),
            ], 'laravel-user-projects-translations');
        }

        $this->loadTranslationsFrom($this->package->basePath('/../resources/lang'), 'user-projects');

        $this->mergeConfigFrom($this->package->basePath('/../config/user-projects.php'), 'user-projects');

        config(['dashboard.widgets' => array_merge(
            config('dashboard.widgets', []),
            [ProjectsDashboardWidget::class],
        )]);

        if (class_exists(DashboardNavigationRegistry::class)) {
            $registry = $this->app->make(DashboardNavigationRegistry::class);
            $registry->group('user_center', 'Projects', 1);

            $registry->add('user_center', new DashboardNavigationItem(
                label: 'Overview',
                url: url('/hub/projects'),
                icon: 'heroicon-o-squares-2x2',
                sort: 10,
            ));

            $registry->add('user_center', new DashboardNavigationItem(
                label: 'Stats',
                url: url('/hub/projects/stats'),
                icon: 'heroicon-o-chart-bar',
                sort: 20,
            ));

            $registry->add('user_center', new DashboardNavigationItem(
                label: 'Settings',
                url: url('/hub/projects/settings'),
                icon: 'heroicon-o-cog-6-tooth',
                sort: 30,
            ));

            $registry->add('user_center', new DashboardNavigationItem(
                label: 'Permissions',
                url: url('/hub/projects/permissions'),
                icon: 'heroicon-o-shield-check',
                sort: 40,
            ));
        }

        $this->app->make(PlatformPackageRegistrar::class)
            ->register(new UserProjectsPlatformPackage);
    }
}
