<?php

declare(strict_types=1);

namespace YezzMedia\UserProjects\Tests;

use Filament\FilamentServiceProvider;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\Concerns\WithWorkbench;
use YezzMedia\Foundation\FoundationServiceProvider;
use YezzMedia\Foundation\Testing\FoundationTestCase;
use YezzMedia\UserProjects\UserProjectsServiceProvider;

abstract class UserProjectsTestCase extends FoundationTestCase
{
    use WithWorkbench;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        $user = $this->createTestUser();

        $this->actingAs($user, 'web');
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadLaravelMigrations();
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
    }

    protected function getPackageProviders($app): array
    {
        return [
            LivewireServiceProvider::class,
            FilamentServiceProvider::class,
            FoundationServiceProvider::class,
            UserProjectsServiceProvider::class,
        ];
    }

    protected function createTestUser(): Authenticatable
    {
        $userClass = config('auth.providers.users.model');

        return $userClass::factory()->create();
    }

    protected function defineRoutes($router): void
    {
        $router->get('/login', fn () => 'login')->name('login');
    }
}
