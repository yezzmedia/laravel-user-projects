<?php

declare(strict_types=1);

namespace YezzMedia\UserProjects\Pages;

use YezzMedia\UserProjects\Models\ProjectRole;
use YezzMedia\UserProjects\Support\ProjectRoleManager;

final class ProjectsPermissionsPage extends UserProjectsPage
{
    protected static ?string $slug = 'projects/permissions';

    protected string $view = 'user-projects::pages.permissions';

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $navigationLabel = 'Permissions';

    protected static string|\BackedEnum|null $navigationIcon = 'shield-check';

    protected static ?int $navigationSort = 40;

    public string $newRoleName = '';

    public string $newRoleLabel = '';

    public array $newRolePermissions = [];

    public ?int $editingRoleId = null;

    public string $editRoleLabel = '';

    public array $editRolePermissions = [];

    protected function getPageTitle(): string
    {
        return $this->translate('user-projects::ui.permissions_title', 'Role Permissions');
    }

    protected function getPageDescription(): string
    {
        return $this->translate('user-projects::ui.permissions_description', 'Manage roles and their assigned permissions.');
    }

    protected function pageData(): array
    {
        return [
            'roles' => app(ProjectRoleManager::class)->all(),
            'allPermissions' => app(ProjectRoleManager::class)->allPermissions(),
            'editingRoleId' => $this->editingRoleId,
            'editingRole' => $this->editingRoleId !== null
                ? app(ProjectRoleManager::class)->findById($this->editingRoleId)
                : null,
        ];
    }

    public function createRole(): void
    {
        $this->validate([
            'newRoleName' => ['required', 'string', 'max:255', 'unique:project_roles,name'],
            'newRoleLabel' => ['required', 'string', 'max:255'],
        ]);

        app(ProjectRoleManager::class)->create(
            name: $this->newRoleName,
            label: $this->newRoleLabel,
            permissions: $this->newRolePermissions,
        );

        $this->reset(['newRoleName', 'newRoleLabel', 'newRolePermissions']);
    }

    public function startEditing(ProjectRole $role): void
    {
        $this->editingRoleId = $role->id;
        $this->editRoleLabel = $role->label;
        $this->editRolePermissions = $role->permissions ?? [];
    }

    public function cancelEditing(): void
    {
        $this->editingRoleId = null;
        $this->editRoleLabel = '';
        $this->editRolePermissions = [];
    }

    public function updateRole(): void
    {
        $this->validate([
            'editRoleLabel' => ['required', 'string', 'max:255'],
        ]);

        $role = app(ProjectRoleManager::class)->findById($this->editingRoleId);

        if ($role !== null) {
            app(ProjectRoleManager::class)->update(
                role: $role,
                label: $this->editRoleLabel,
                permissions: $this->editRolePermissions,
            );
        }

        $this->cancelEditing();
    }

    public function deleteRole(int $roleId): void
    {
        $role = app(ProjectRoleManager::class)->findById($roleId);

        if ($role !== null) {
            app(ProjectRoleManager::class)->delete($role);
        }

        if ($this->editingRoleId === $roleId) {
            $this->cancelEditing();
        }
    }
}
