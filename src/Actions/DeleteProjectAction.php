<?php

declare(strict_types=1);

namespace YezzMedia\UserProjects\Actions;

use YezzMedia\UserProjects\Models\Project;
use YezzMedia\UserProjects\Support\ProjectManager;

final readonly class DeleteProjectAction
{
    public function __construct(
        private ProjectManager $projectManager,
    ) {}

    public function execute(Project $project): bool
    {
        return $this->projectManager->delete($project);
    }
}
