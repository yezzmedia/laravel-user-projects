<?php

declare(strict_types=1);

use YezzMedia\Foundation\Registry\PackageRegistry;
use YezzMedia\UserProjects\UserProjectsPlatformPackage;

beforeEach(function (): void {
    $this->user = $this->createUser();
});

it('creates a test user through TestCase helper', function (): void {
    expect($this->user)->not->toBeNull();
});

it('registers the platform package', function (): void {
    expect(app(PackageRegistry::class)->has('yezzmedia/laravel-user-projects'))->toBeTrue();

    $metadata = app(PackageRegistry::class)->find('yezzmedia/laravel-user-projects');

    expect($metadata)->not->toBeNull()
        ->and($metadata->name)->toBe('yezzmedia/laravel-user-projects')
        ->and($metadata->vendor)->toBe('yezzmedia');
});

it('defines permissions', function (): void {
    $package = app(UserProjectsPlatformPackage::class);
    $permissions = $package->permissionDefinitions();

    expect($permissions)->toHaveCount(2);
    expect($permissions[0]->name)->toBe('user-projects.manage');
});

it('defines features', function (): void {
    $package = app(UserProjectsPlatformPackage::class);
    $features = $package->featureDefinitions();

    expect($features)->toHaveCount(6);
});

it('defines audit events', function (): void {
    $package = app(UserProjectsPlatformPackage::class);
    $events = $package->auditEventDefinitions();

    expect($events)->toHaveCount(9);
});

it('defines rate limiters', function (): void {
    $package = app(UserProjectsPlatformPackage::class);
    $limits = $package->rateLimitDefinitions();

    expect($limits)->toHaveCount(3);
});

it('defines install steps', function (): void {
    $package = app(UserProjectsPlatformPackage::class);
    $steps = $package->installSteps();

    expect($steps)->toHaveCount(3);
});

it('defines doctor checks', function (): void {
    $package = app(UserProjectsPlatformPackage::class);
    $checks = $package->doctorChecks();

    expect($checks)->toHaveCount(2);
});
