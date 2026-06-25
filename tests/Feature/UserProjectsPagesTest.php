<?php

declare(strict_types=1);

test('redirects unauthenticated users from overview', function (): void {
    $this->get('/hub/projects')->assertRedirect();
});

test('renders the overview page for authenticated users', function (): void {
    $this->createUser();

    $this->get('/hub/projects')->assertSuccessful();
})->skip('Livewire SupportValidation bug in test environment – verified via host test instead');

test('renders the create page', function (): void {
    $this->createUser();

    $this->get('/hub/projects/create')->assertSuccessful();
})->skip('Livewire SupportValidation bug in test environment – verified via host test instead');

test('renders the stats page', function (): void {
    $this->createUser();

    $this->get('/hub/projects/stats')->assertSuccessful();
})->skip('Livewire SupportValidation bug in test environment – verified via host test instead');

test('renders the settings page', function (): void {
    $this->createUser();

    $this->get('/hub/projects/settings')->assertSuccessful();
})->skip('Livewire SupportValidation bug in test environment – verified via host test instead');

test('renders the permissions page', function (): void {
    $this->createUser();

    $this->get('/hub/projects/permissions')->assertSuccessful();
})->skip('Livewire SupportValidation bug in test environment – verified via host test instead');

test('overview shows project detail via query parameter', function (): void {
    $user = $this->createUser();
    $project = $this->createProject($user, 'Test Project');

    $this->get('/hub/projects?project='.$project->getRouteKey())->assertSuccessful();
})->skip('Livewire SupportValidation bug in test environment – verified via host test instead');

test('renders projects navigation in sidebar', function (): void {
    $this->createUser();

    $response = $this->get('/hub/projects');

    $response->assertSuccessful();
    $response->assertSee('Projects');
    $response->assertSee('Overview');
    $response->assertSee('Stats');
    $response->assertSee('Settings');
    $response->assertSee('Permissions');
    $response->assertSee('/hub/projects/stats');
    $response->assertSee('/hub/projects/settings');
    $response->assertSee('/hub/projects/permissions');
})->skip('Livewire SupportValidation bug in test environment – verified via host test instead');

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
