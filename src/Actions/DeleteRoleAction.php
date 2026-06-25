<?php

declare(strict_types=1);

namespace YezzMedia\UserProjects\Actions;

use YezzMedia\UserProjects\Models\ProjectRole;

final readonly class DeleteRoleAction
{
    public function execute(ProjectRole $role): bool
    {
        if ($role->is_system) {
            return false;
        }

        return (bool) $role->delete();
    }
}
