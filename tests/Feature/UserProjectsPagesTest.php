<?php

declare(strict_types=1);

use YezzMedia\Dashboard\Navigation\DashboardNavigationRegistry;

test('redirects unauthenticated users from overview', function (): void {
    $this->get('/hub/projects')->assertRedirect();
});

test('dashboard navigation registry contains project items', function (): void {
    $this->createUser();

    $groups = app(DashboardNavigationRegistry::class)->groups();

    $labels = collect($groups)->flatMap(fn (array $group) => collect($group['items'])->pluck('label'))->all();

    expect($labels)->toContain('Overview');
    expect($labels)->toContain('Stats');
    expect($labels)->toContain('Settings');
    expect($labels)->toContain('Permissions');
});

test('redirects unauthenticated users from create', function (): void {
    $this->get('/hub/projects/create')->assertRedirect();
});

test('redirects unauthenticated users from stats', function (): void {
    $this->get('/hub/projects/stats')->assertRedirect();
});

test('redirects unauthenticated users from settings', function (): void {
    $this->get('/hub/projects/settings')->assertRedirect();
});

test('redirects unauthenticated users from permissions', function (): void {
    $this->get('/hub/projects/permissions')->assertRedirect();
});
