<?php

declare(strict_types=1);

namespace YezzMedia\UserProjects\Actions;

use YezzMedia\UserProjects\Models\Project;
use YezzMedia\UserProjects\Models\ProjectMember;
use YezzMedia\UserProjects\Support\ProjectMemberManager;

final readonly class InviteMemberAction
{
    public function __construct(
        private ProjectMemberManager $memberManager,
    ) {}

    public function execute(Project $project, int $userId, string $role = 'member'): ProjectMember
    {
        return $this->memberManager->addMember($project, $userId, $role);
    }
}
