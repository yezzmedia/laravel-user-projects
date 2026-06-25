<?php

declare(strict_types=1);

namespace YezzMedia\UserProjects\Actions;

use Illuminate\Contracts\Auth\Authenticatable;
use YezzMedia\UserProjects\Models\Project;
use YezzMedia\UserProjects\Support\ProjectManager;

final readonly class CreateProjectAction
{
    public function __construct(
        private ProjectManager $projectManager,
    ) {}

    public function execute(Authenticatable $user, string $name, ?string $description = null): Project
    {
        return $this->projectManager->create($user, $name, $description);
    }
}
