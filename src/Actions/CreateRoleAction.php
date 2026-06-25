<?php

declare(strict_types=1);

namespace YezzMedia\UserProjects\Actions;

use YezzMedia\UserProjects\Models\ProjectRole;

final readonly class CreateRoleAction
{
    public function execute(string $name, string $label, array $permissions = []): ProjectRole
    {
        return ProjectRole::query()->create([
            'name' => $name,
            'label' => $label,
            'permissions' => $permissions,
            'is_system' => false,
        ]);
    }
}
