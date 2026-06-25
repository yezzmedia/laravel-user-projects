<?php

declare(strict_types=1);

use YezzMedia\UserProjects\Models\Project;
use YezzMedia\UserProjects\Support\ProjectStatsService;

beforeEach(function (): void {
    $this->user = $this->createUser();
});

it('returns zero totals when no projects exist', function (): void {
    $stats = app(ProjectStatsService::class)->dashboard();

    expect($stats['total_projects'])->toBe(0)
        ->and($stats['total_members'])->toBe(0)
        ->and($stats['active_projects'])->toBe(0)
        ->and($stats['archived_projects'])->toBe(0)
        ->and($stats['avg_members_per_project'])->toBe(0.0);
});

it('returns correct total projects count', function (): void {
    $this->createProject($this->user, 'Project 1');
    $this->createProject($this->user, 'Project 2');

    expect(app(ProjectStatsService::class)->totalProjects())->toBe(2);
});

it('returns correct active and archived counts', function (): void {
    $this->createProject($this->user, 'Active');
    Project::query()->create([
        'owner_id' => $this->user->getAuthIdentifier(),
        'name' => 'Archived',
        'status' => 'archived',
    ]);

    expect(app(ProjectStatsService::class)->activeProjects())->toBe(1)
        ->and(app(ProjectStatsService::class)->archivedProjects())->toBe(1);
});

it('calculates average members per project', function (): void {
    $p1 = $this->createProject($this->user, 'P1');
    $p2 = $this->createProject($this->user, 'P2');

    $memberUser1 = $this->createUser();
    $memberUser2 = $this->createUser();

    $this->addMember($p1, $memberUser1, 'member');
    $this->addMember($p2, $memberUser2, 'member');
    $this->addMember($p2, $this->user, 'admin');

    expect(app(ProjectStatsService::class)->averageMembersPerProject())->toBe(1.5);
});

it('returns role distribution', function (): void {
    $project = $this->createProject($this->user, 'Test');
    $this->addMember($project, $this->user, 'owner');

    $admin = $this->createUser();
    $this->addMember($project, $admin, 'admin');

    $member = $this->createUser();
    $this->addMember($project, $member, 'member');

    $dist = app(ProjectStatsService::class)->memberRoleDistribution();

    expect($dist->get('owner'))->toBe(1)
        ->and($dist->get('admin'))->toBe(1)
        ->and($dist->get('member'))->toBe(1);
});

it('returns top projects by member count', function (): void {
    $p1 = $this->createProject($this->user, 'Small');
    $p2 = $this->createProject($this->user, 'Large');

    $this->addMember($p2, $this->createUser(), 'member');
    $this->addMember($p2, $this->createUser(), 'member');
    $this->addMember($p2, $this->createUser(), 'member');

    $top = app(ProjectStatsService::class)->topProjectsByMembers(2);

    expect($top)->toHaveCount(2)
        ->and($top->first()->name)->toBe('Large');
});

it('returns projects by owner', function (): void {
    $this->createProject($this->user, 'P1');
    $this->createProject($this->user, 'P2');

    $otherUser = $this->createUser();
    $this->createProject($otherUser, 'OtherP');

    $owners = app(ProjectStatsService::class)->projectsByOwner();

    expect($owners)->toHaveCount(2)
        ->and($owners->first()['count'])->toBe(2);
});

it('returns newly created counts', function (): void {
    $this->createProject($this->user, 'Today');

    expect(app(ProjectStatsService::class)->newlyCreatedToday())->toBe(1)
        ->and(app(ProjectStatsService::class)->newlyCreatedThisWeek())->toBe(1)
        ->and(app(ProjectStatsService::class)->newlyCreatedThisMonth())->toBe(1);
});
