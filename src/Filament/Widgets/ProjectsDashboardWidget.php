<?php

declare(strict_types=1);

namespace YezzMedia\UserProjects\Filament\Widgets;

use Filament\Widgets\Widget;
use YezzMedia\UserProjects\Support\ProjectManager;

final class ProjectsDashboardWidget extends Widget
{
    protected static string $view = 'user-projects::widgets.projects-dashboard';

    public function getProjects(): array
    {
        $user = auth(config('user-projects.panel.guard', 'web'))->user();

        if ($user === null) {
            return [];
        }

        return app(ProjectManager::class)
            ->listForUser($user)
            ->take(6)
            ->values()
            ->toArray();
    }

    protected function getViewData(): array
    {
        return [
            'projects' => $this->getProjects(),
        ];
    }
}
