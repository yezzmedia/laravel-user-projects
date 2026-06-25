<?php

declare(strict_types=1);

namespace YezzMedia\UserProjects\Pages;

use YezzMedia\UserProjects\Support\ProjectStatsService;

final class ProjectsStatsPage extends UserProjectsPage
{
    protected static ?string $slug = 'projects/stats';

    protected string $view = 'user-projects::pages.stats';

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $navigationLabel = 'Stats';

    protected static string|\BackedEnum|null $navigationIcon = 'chart-bar';

    protected static ?int $navigationSort = 20;

    protected function getPageTitle(): string
    {
        return $this->translate('user-projects::ui.stats_title', 'Project Statistics');
    }

    protected function getPageDescription(): string
    {
        return $this->translate('user-projects::ui.stats_description', 'Overview and analytics for all projects.');
    }

    protected function pageData(): array
    {
        return app(ProjectStatsService::class)->dashboard();
    }
}
