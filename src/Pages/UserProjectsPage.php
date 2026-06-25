<?php

declare(strict_types=1);

namespace YezzMedia\UserProjects\Pages;

use YezzMedia\Dashboard\Pages\DashboardPage;

abstract class UserProjectsPage extends DashboardPage
{
    protected static bool $shouldRegisterNavigation = true;

    abstract protected function getPageTitle(): string;

    abstract protected function getPageDescription(): string;

    abstract protected function pageData(): array;

    public static function canAccess(): bool
    {
        return auth(config('user-projects.panel.guard', 'web'))->check();
    }

    protected function getViewData(): array
    {
        return [
            'pageTitle' => $this->getPageTitle(),
            'pageData' => $this->pageData(),
            'pageDescription' => $this->getPageDescription(),
        ];
    }

    public function getTitle(): string
    {
        return $this->getPageTitle();
    }
}
