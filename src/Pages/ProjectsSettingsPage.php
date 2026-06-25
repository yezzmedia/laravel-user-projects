<?php

declare(strict_types=1);

namespace YezzMedia\UserProjects\Pages;

final class ProjectsSettingsPage extends UserProjectsPage
{
    protected static ?string $slug = 'projects/settings';

    protected string $view = 'user-projects::pages.settings';

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $navigationLabel = 'Settings';

    protected static string|\BackedEnum|null $navigationIcon = 'cog-6-tooth';

    protected static ?int $navigationSort = 30;

    public int $displayLimit;

    public function mount(): void
    {
        $this->displayLimit = (int) config('user-projects.projects.display_limit', 25);
    }

    public function save(): void
    {
        config()->set('user-projects.projects.display_limit', $this->displayLimit);
    }

    protected function getPageTitle(): string
    {
        return $this->translate('user-projects::ui.settings_title', 'Project Settings');
    }

    protected function getPageDescription(): string
    {
        return $this->translate('user-projects::ui.settings_description', 'Global configuration for all projects.');
    }

    protected function pageData(): array
    {
        $roles = config('user-projects.members.roles', []);
        $defaultRole = array_key_first($roles) ?? 'member';

        return [
            'displayLimit' => $this->displayLimit,
            'availableRoles' => $roles,
            'defaultRole' => $defaultRole,
        ];
    }
}
