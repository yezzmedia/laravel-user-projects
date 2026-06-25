<?php

declare(strict_types=1);

use YezzMedia\UserProjects\Models\ProjectMember;
use YezzMedia\UserProjects\Support\ProjectManager;

beforeEach(function (): void {
    $this->user = $this->createUser();
});

it('creates a project', function (): void {
    $project = app(ProjectManager::class)->create(
        user: $this->user,
        name: 'My Project',
        description: 'A test project',
    );

    expect($project->name)->toBe('My Project')
        ->and($project->description)->toBe('A test project')
        ->and((int) $project->owner_id)->toBe((int) $this->user->getAuthIdentifier())
        ->and($project->status)->toBe('active');
});

it('creates an owner member when creating a project', function (): void {
    $project = app(ProjectManager::class)->create(
        user: $this->user,
        name: 'My Project',
    );

    expect($project->members()->where('role', 'owner')->exists())->toBeTrue();
});

it('lists projects for a user', function (): void {
    app(ProjectManager::class)->create($this->user, 'Project A');
    app(ProjectManager::class)->create($this->user, 'Project B');

    $projects = app(ProjectManager::class)->listForUser($this->user);

    expect($projects)->toHaveCount(2);
});

it('returns empty list for guest', function (): void {
    $projects = app(ProjectManager::class)->listForUser(null);

    expect($projects)->toHaveCount(0);
});

it('finds project by identifier', function (): void {
    $project = app(ProjectManager::class)->create($this->user, 'Test');

    $found = app(ProjectManager::class)->findByIdentifier((string) $project->id);

    expect($found)->not->toBeNull()
        ->and((string) $found->id)->toBe((string) $project->id);
});

it('updates a project', function (): void {
    $project = app(ProjectManager::class)->create($this->user, 'Old Name');

    $updated = app(ProjectManager::class)->update($project, ['name' => 'New Name']);

    expect($updated)->toBeTrue()
        ->and($project->fresh()->name)->toBe('New Name');
});

it('deletes a project and its members', function (): void {
    $project = app(ProjectManager::class)->create($this->user, 'To Delete');

    app(ProjectManager::class)->delete($project);

    expect($project->fresh())->toBeNull()
        ->and(ProjectMember::where('project_id', $project->id)->exists())->toBeFalse();
});
