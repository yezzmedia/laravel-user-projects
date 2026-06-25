<?php

declare(strict_types=1);

use YezzMedia\UserProjects\Models\Project;
use YezzMedia\UserProjects\Support\ProjectMemberManager;

beforeEach(function (): void {
    $this->owner = $this->createUser();
    $this->member = $this->createUser();
    $this->project = Project::query()->create([
        'owner_id' => $this->owner->getAuthIdentifier(),
        'name' => 'Test Project',
    ]);
});

it('lists project members', function (): void {
    $this->addMember($this->project, $this->owner, 'owner');
    $this->addMember($this->project, $this->member, 'member');

    $members = app(ProjectMemberManager::class)->members($this->project);

    expect($members)->toHaveCount(2);
});

it('adds a member', function (): void {
    $member = app(ProjectMemberManager::class)->addMember(
        $this->project,
        (int) $this->member->getAuthIdentifier(),
        'admin',
    );

    expect($member->role)->toBe('admin')
        ->and($member->user_id)->toBe((int) $this->member->getAuthIdentifier());
});

it('removes a member', function (): void {
    $this->addMember($this->project, $this->member, 'member');

    $removed = app(ProjectMemberManager::class)->removeMember(
        $this->project,
        (int) $this->member->getAuthIdentifier(),
    );

    expect($removed)->toBeTrue();
});

it('updates member role', function (): void {
    $this->addMember($this->project, $this->member, 'member');

    $updated = app(ProjectMemberManager::class)->updateRole(
        $this->project,
        (int) $this->member->getAuthIdentifier(),
        'admin',
    );

    expect($updated)->toBeTrue();

    $member = $this->project->members()->where('user_id', $this->member->getAuthIdentifier())->first();
    expect($member->role)->toBe('admin');
});

it('checks if user is owner', function (): void {
    $this->addMember($this->project, $this->owner, 'owner');

    expect(app(ProjectMemberManager::class)->isOwner(
        $this->project,
        (int) $this->owner->getAuthIdentifier(),
    ))->toBeTrue();

    expect(app(ProjectMemberManager::class)->isOwner(
        $this->project,
        (int) $this->member->getAuthIdentifier(),
    ))->toBeFalse();
});

it('checks if user is admin or owner', function (): void {
    $this->addMember($this->project, $this->owner, 'owner');
    $this->addMember($this->project, $this->member, 'admin');

    expect(app(ProjectMemberManager::class)->isAdminOrOwner(
        $this->project,
        (int) $this->owner->getAuthIdentifier(),
    ))->toBeTrue();

    expect(app(ProjectMemberManager::class)->isAdminOrOwner(
        $this->project,
        (int) $this->member->getAuthIdentifier(),
    ))->toBeTrue();
});
