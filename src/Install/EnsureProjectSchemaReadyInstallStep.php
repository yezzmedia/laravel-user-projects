<?php

declare(strict_types=1);

namespace YezzMedia\UserProjects\Install;

use RuntimeException;
use YezzMedia\Foundation\Data\InstallContext;
use YezzMedia\Foundation\Install\InstallStep;
use YezzMedia\UserProjects\Support\ProjectStoreSetup;

final class EnsureProjectSchemaReadyInstallStep implements InstallStep
{
    public function __construct(private readonly ProjectStoreSetup $setup) {}

    public function key(): string
    {
        return 'ensure_user_projects_schema_ready';
    }

    public function package(): string
    {
        return 'yezzmedia/laravel-user-projects';
    }

    public function priority(): int
    {
        return 30;
    }

    public function shouldRun(InstallContext $context): bool
    {
        return ! $this->setup->storeReady();
    }

    public function handle(InstallContext $context): void
    {
        if (! $context->allowMigrations) {
            throw new RuntimeException('The user projects schema is not ready. Run `php artisan migrate` or rerun `php artisan website:install --migrate`.');
        }
    }
}
