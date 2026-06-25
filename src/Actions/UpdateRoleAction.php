<?php

declare(strict_types=1);

namespace YezzMedia\UserProjects\Actions;

use YezzMedia\UserProjects\Models\ProjectRole;

final readonly class UpdateRoleAction
{
    public function execute(ProjectRole $role, string $label, array $permissions): ProjectRole
    {
        $role->update([
            'label' => $label,
            'permissions' => $permissions,
        ]);

        return $role->refresh();
    }
}
