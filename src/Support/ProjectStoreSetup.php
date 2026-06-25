<?php

declare(strict_types=1);

namespace YezzMedia\UserProjects\Support;

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
}
