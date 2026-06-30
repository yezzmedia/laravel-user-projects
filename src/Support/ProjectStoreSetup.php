<?php

declare(strict_types=1);

namespace YezzMedia\UserProjects\Support;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

final class ProjectStoreSetup
{
    public function storeReady(): bool
    {
        return Schema::hasTable('projects') && Schema::hasTable('project_members');
    }

    public function configPublished(): bool
    {
        return file_exists(config_path('user-projects.php'));
    }

    public function publishConfig(bool $force = false): void
    {
        $source = __DIR__.'/../../config/user-projects.php';
        $target = config_path('user-projects.php');

        if (! is_dir(dirname($target))) {
            mkdir(dirname($target), 0755, true);
        }

        if ($force || ! file_exists($target)) {
            copy($source, $target);
        }
    }

    public function migrationsPublished(): bool
    {
        $prefixes = [
            '0001_create_projects_table',
            '0002_create_project_members_table',
            '0003_create_project_roles_table',
            '0004_create_project_addon_activations_table',
            '0005_create_installed_addons_table',
            '0006_create_project_invitations_table',
            '0007_create_project_activities_table',
            '0008_add_photo_to_projects_table',
        ];

        foreach ($prefixes as $prefix) {
            $found = false;
            foreach (File::glob(database_path('migrations'.DIRECTORY_SEPARATOR.'*_'.$prefix.'.php')) as $path) {
                if (str_contains(basename($path), $prefix)) {
                    $found = true;

                    break;
                }
            }

            if (! $found) {
                return false;
            }
        }

        return true;
    }

    public function publishMigrations(): void
    {
        Artisan::call('vendor:publish', [
            '--tag' => 'user-projects-migrations',
            '--force' => true,
        ]);
    }

    public function migrateStore(): void
    {
        Artisan::call('migrate', [
            '--force' => true,
        ]);
    }
}
