<?php

declare(strict_types=1);

namespace YezzMedia\UserProjects\Pages;

use Livewire\Attributes\Url;
use YezzMedia\UserProjects\Models\Project;
use YezzMedia\UserProjects\Support\ProjectManager;
use YezzMedia\UserProjects\Support\ProjectMemberManager;

final class ProjectsOverviewPage extends UserProjectsPage
{
    protected static ?string $slug = 'projects';

    #[Url]
    public ?string $project = null;

    public string $activeTab = 'overview';

    public function selectProject(string $projectId): void
    {
        $this->project = $projectId;
        $this->activeTab = 'overview';
    }

    public function backToList(): void
    {
        $this->project = null;
        $this->activeTab = 'overview';
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function deleteProject(string $projectId): void
    {
        $resolved = app(ProjectManager::class)->findByIdentifier($projectId);

        if ($resolved !== null) {
            app(ProjectManager::class)->delete($resolved);
        }

        $this->project = null;
        $this->activeTab = 'overview';
    }

    protected string $view = 'user-projects::pages.overview';

    protected static string|\UnitEnum|null $navigationGroup = 'Projects';

    protected static ?string $navigationLabel = 'Overview';

    protected static string|\BackedEnum|null $navigationIcon = 'squares-2x2';

    protected static ?int $navigationSort = 10;

    public static function getDefaultSlug(): string
    {
        return 'projects';
    }

    protected function getPageTitle(): string
    {
        if ($this->project !== null) {
            $resolved = $this->resolvedProject;

            if ($resolved !== null) {
                return $resolved->name;
            }
        }

        return $this->translate('user-projects::ui.projects_title', 'Projects');
    }

    protected function getPageDescription(): string
    {
        if ($this->project !== null) {
            return $this->translate('user-projects::ui.project_overview_description', 'View project details and manage settings.');
        }

        return $this->translate('user-projects::ui.projects_description', 'Manage your projects.');
    }

    protected function pageData(): array
    {
        $user = auth(config('user-projects.panel.guard', 'web'))->user();

        return [
            'projects' => $user !== null ? app(ProjectManager::class)->listForUser($user) : collect(),
            'projectCount' => Project::query()->count(),
            'selectedProject' => $this->resolvedProject,
            'members' => $this->resolvedProject !== null
                ? app(ProjectMemberManager::class)->members($this->resolvedProject)
                : collect(),
            'activeTab' => $this->activeTab,
        ];
    }

    public function getResolvedProjectProperty(): ?Project
    {
        if ($this->project === null) {
            return null;
        }

        return app(ProjectManager::class)->findByIdentifier($this->project);
    }
}
