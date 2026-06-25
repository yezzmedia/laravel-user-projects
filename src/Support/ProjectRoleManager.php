<?php

declare(strict_types=1);

namespace YezzMedia\UserProjects\Support;

use Illuminate\Support\Collection;
use YezzMedia\UserProjects\Actions\CreateRoleAction;
use YezzMedia\UserProjects\Actions\DeleteRoleAction;
use YezzMedia\UserProjects\Actions\UpdateRoleAction;
use YezzMedia\UserProjects\Models\ProjectRole;

final class ProjectRoleManager
{
    public function __construct(
        private readonly CreateRoleAction $createRoleAction,
        private readonly UpdateRoleAction $updateRoleAction,
        private readonly DeleteRoleAction $deleteRoleAction,
    ) {}

    public function all(): Collection
    {
        return ProjectRole::query()->orderBy('is_system', 'desc')->orderBy('name')->get();
    }

    public function allPermissions(): array
    {
        return config('user-projects.permissions', [
            'create_project',
            'edit_project',
            'delete_project',
            'invite_members',
            'remove_members',
            'manage_roles',
            'view_stats',
            'manage_settings',
        ]);
    }

    public function findById(int $id): ?ProjectRole
    {
        return ProjectRole::query()->find($id);
    }

    public function findByName(string $name): ?ProjectRole
    {
        return ProjectRole::query()->where('name', $name)->first();
    }

    public function create(string $name, string $label, array $permissions = []): ProjectRole
    {
        return $this->createRoleAction->execute($name, $label, $permissions);
    }

    public function update(ProjectRole $role, string $label, array $permissions): ProjectRole
    {
        return $this->updateRoleAction->execute($role, $label, $permissions);
    }

    public function delete(ProjectRole $role): bool
    {
        return $this->deleteRoleAction->execute($role);
    }

    public function getPermissions(ProjectRole|string $role): array
    {
        if (is_string($role)) {
            $role = $this->findByName($role);
        }

        if ($role === null) {
            return [];
        }

        return $role->permissions ?? [];
    }

    public function can(ProjectRole|string|null $role, string $permission): bool
    {
        if ($role === null) {
            return false;
        }

        $permissions = $this->getPermissions($role);

        return in_array($permission, $permissions, true);
    }
}
