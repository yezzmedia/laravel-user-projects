<?php

declare(strict_types=1);

namespace YezzMedia\UserProjects\Pages;

use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;
use YezzMedia\UserProjects\Support\ProjectManager;

final class ProjectsCreatePage extends UserProjectsPage
{
    protected static ?string $slug = 'projects/create';

    public string $name = '';

    public string $description = '';

    protected string $view = 'user-projects::pages.create';

    protected static bool $shouldRegisterNavigation = false;

    public function create(): Redirector|RedirectResponse|null
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $user = auth(config('user-projects.panel.guard', 'web'))->user();

        if ($user === null) {
            return null;
        }

        $project = app(ProjectManager::class)->create(
            user: $user,
            name: $this->name,
            description: $this->description !== '' ? $this->description : null,
        );

        return redirect(ProjectsOverviewPage::getUrl(['project' => $project->getRouteKey()]));
    }

    protected function getPageTitle(): string
    {
        return $this->translate('user-projects::ui.create_project_title', 'Create New Project');
    }

    protected function getPageDescription(): string
    {
        return $this->translate('user-projects::ui.create_project_description', 'Set up a new project workspace.');
    }

    protected function pageData(): array
    {
        return [];
    }
}
