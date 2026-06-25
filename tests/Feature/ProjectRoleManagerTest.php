<?php

declare(strict_types=1);

use YezzMedia\UserProjects\Models\ProjectRole;
use YezzMedia\UserProjects\Support\ProjectRoleManager;

beforeEach(function (): void {
    $this->createUser();
});

it('lists all roles including seeded system roles', function (): void {
    $roles = app(ProjectRoleManager::class)->all();

    expect($roles)->toHaveCount(3)
        ->and($roles->pluck('name')->values())->toContain('owner', 'admin', 'member');
});

it('finds a role by name', function (): void {
    $role = app(ProjectRoleManager::class)->findByName('owner');

    expect($role)->not->toBeNull()
        ->and($role->name)->toBe('owner')
        ->and($role->is_system)->toBeTrue();
});

it('finds a role by id', function (): void {
    $role = app(ProjectRoleManager::class)->findByName('admin');

    $found = app(ProjectRoleManager::class)->findById($role->id);

    expect($found)->not->toBeNull()
        ->and($found->id)->toBe($role->id);
});

it('creates a custom role', function (): void {
    $role = app(ProjectRoleManager::class)->create(
        name: 'editor',
        label: 'Editor',
        permissions: ['edit_project', 'invite_members'],
    );

    expect($role->name)->toBe('editor')
        ->and($role->label)->toBe('Editor')
        ->and($role->is_system)->toBeFalse()
        ->and($role->permissions)->toBe(['edit_project', 'invite_members']);
});

it('updates a role label and permissions', function (): void {
    $role = app(ProjectRoleManager::class)->create('viewer', 'Viewer', []);

    $updated = app(ProjectRoleManager::class)->update(
        role: $role,
        label: 'Read-only Viewer',
        permissions: ['view_stats'],
    );

    expect($updated->label)->toBe('Read-only Viewer')
        ->and($updated->permissions)->toBe(['view_stats']);
});

it('deletes a non-system role', function (): void {
    $role = app(ProjectRoleManager::class)->create('temp', 'Temporary', []);

    $result = app(ProjectRoleManager::class)->delete($role);

    expect($result)->toBeTrue()
        ->and(ProjectRole::find($role->id))->toBeNull();
});

it('does not delete system roles', function (): void {
    $role = app(ProjectRoleManager::class)->findByName('owner');

    $result = app(ProjectRoleManager::class)->delete($role);

    expect($result)->toBeFalse()
        ->and(ProjectRole::find($role->id))->not->toBeNull();
});

it('gets permissions for a role', function (): void {
    $permissions = app(ProjectRoleManager::class)->getPermissions('owner');

    expect($permissions)->toContain('create_project', 'edit_project', 'delete_project');
});

it('gets permissions returns empty for unknown role', function (): void {
    $permissions = app(ProjectRoleManager::class)->getPermissions('nonexistent');

    expect($permissions)->toBe([]);
});

it('checks role permission', function (): void {
    expect(app(ProjectRoleManager::class)->can('owner', 'delete_project'))->toBeTrue();
    expect(app(ProjectRoleManager::class)->can('admin', 'delete_project'))->toBeFalse();
    expect(app(ProjectRoleManager::class)->can('member', 'view_stats'))->toBeFalse();
});

it('returns all registered permissions from config', function (): void {
    $permissions = app(ProjectRoleManager::class)->allPermissions();

    expect($permissions)->toHaveCount(8)
        ->toContain('create_project', 'edit_project', 'delete_project', 'invite_members', 'remove_members', 'manage_roles', 'view_stats', 'manage_settings');
});
