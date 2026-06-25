<?php

declare(strict_types=1);

namespace YezzMedia\UserProjects\Actions;

use YezzMedia\UserProjects\Models\Project;
use YezzMedia\UserProjects\Support\ProjectMemberManager;

final readonly class RemoveMemberAction
{
    public function __construct(
        private ProjectMemberManager $memberManager,
    ) {}

    public function execute(Project $project, int $userId): bool
    {
        return $this->memberManager->removeMember($project, $userId);
    }
}
