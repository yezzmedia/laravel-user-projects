<?php

declare(strict_types=1);

namespace YezzMedia\UserProjects\Install;

use YezzMedia\Foundation\Data\InstallContext;
use YezzMedia\Foundation\Install\InstallStep;
use YezzMedia\UserProjects\Support\ProjectStoreSetup;

final class PublishProjectMigrationsInstallStep implements InstallStep
{
    public function __construct(private readonly ProjectStoreSetup $setup) {}

    public function key(): string
    {
        return 'publish_user_projects_migrations';
    }

    public function package(): string
    {
        return 'yezzmedia/laravel-user-projects';
    }

    public function priority(): int
    {
        return 15;
    }

    public function shouldRun(InstallContext $context): bool
    {
        return $context->refreshPublishedResources || ! $this->setup->migrationsPublished();
    }

    public function handle(InstallContext $context): void
    {
        $this->setup->publishMigrations();
    }
}
