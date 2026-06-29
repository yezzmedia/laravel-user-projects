<?php

declare(strict_types=1);

namespace YezzMedia\UserProjects\Pages;

use YezzMedia\Dashboard\Navigation\DashboardNavigationItem;
use YezzMedia\Dashboard\Pages\DashboardPage;
use YezzMedia\UserProjects\Support\ProjectAddonManager;
use YezzMedia\UserProjects\Support\ProjectManager;

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

    protected function getLayoutData(): array
    {
        $data = parent::getLayoutData();
        $groups = $data['navigationGroups'] ?? [];

        $user = auth(config('user-projects.panel.guard', 'web'))->user();

        if ($user !== null) {
            $projects = app(ProjectManager::class)->listForUser($user);
            $addonManager = app(ProjectAddonManager::class);
            $groupSort = 10;

            foreach ($projects as $project) {
                $groupKey = 'project_'.$project->id;
                $addons = $addonManager->activeForProject($project);

                $items = [];
                $itemSort = 10;

                $items[] = new DashboardNavigationItem(
                    label: __('user-projects::ui.overview'),
                    url: url('/hub/projects?project='.$project->id),
                    icon: 'heroicon-o-squares-2x2',
                    group: $groupKey,
                    sort: $itemSort++,
                );

                foreach ($addons as $addon) {
                    $items[] = new DashboardNavigationItem(
                        label: $addon->label,
                        url: $addon->urlFor($project),
                        icon: 'heroicon-o-'.$addon->icon,
                        group: $groupKey,
                        sort: $itemSort++,
                    );
                }

                $groups[$groupKey] = [
                    'label' => $project->name,
                    'sort' => $groupSort++,
                    'items' => $items,
                ];
            }
        }

        $data['navigationGroups'] = $groups;

        return $data;
    }
}
