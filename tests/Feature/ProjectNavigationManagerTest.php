<?php

declare(strict_types=1);

use YezzMedia\UserProjects\Support\ProjectNavigationManager;

test('index navigation includes all project pages', function (): void {
    $this->createUser();

    $navigation = app(ProjectNavigationManager::class)->indexNavigation();

    expect($navigation)->toHaveKey('Projects')
        ->and($navigation['Projects']['items'])->toHaveCount(4);

    $labels = array_column($navigation['Projects']['items'], 'label');
    expect($labels)->toContain('Overview', 'Stats', 'Settings', 'Permissions');
});
