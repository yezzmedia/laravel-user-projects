<?php

declare(strict_types=1);

namespace Tests;

use Filament\Actions\ActionsServiceProvider;
use Filament\FilamentServiceProvider;
use Filament\Forms\FormsServiceProvider;
use Filament\Notifications\NotificationsServiceProvider;
use Filament\Schemas\SchemasServiceProvider;
use Filament\Support\SupportServiceProvider;
use Filament\Tables\TablesServiceProvider;
use Filament\Widgets\WidgetsServiceProvider;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Livewire\LivewireServiceProvider;
use YezzMedia\Dashboard\DashboardServiceProvider;
use YezzMedia\Foundation\FoundationServiceProvider;
use YezzMedia\Foundation\Testing\FoundationTestCase;
use YezzMedia\UserProjects\Models\Project;
use YezzMedia\UserProjects\Models\ProjectMember;
use YezzMedia\UserProjects\UserProjectsServiceProvider;

abstract class TestCase extends FoundationTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $app['config']->set('app.key', 'base64:'.base64_encode(random_bytes(32)));
        $app['config']->set('app.env', 'local');
        $app['config']->set('auth.providers.users.model', TestUser::class);
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadLaravelMigrations();
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    protected function getPackageProviders($app): array
    {
        return [
            LivewireServiceProvider::class,
            SupportServiceProvider::class,
            ActionsServiceProvider::class,
            FormsServiceProvider::class,
            SchemasServiceProvider::class,
            TablesServiceProvider::class,
            WidgetsServiceProvider::class,
            FilamentServiceProvider::class,
            NotificationsServiceProvider::class,
            FoundationServiceProvider::class,
            DashboardServiceProvider::class,
            UserProjectsServiceProvider::class,
        ];
    }

    protected function createUser(): Authenticatable
    {
        $userClass = config('auth.providers.users.model');

        $user = $userClass::forceCreate([
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => bcrypt('password'),
        ]);

        $this->actingAs($user, 'web');

        return $user;
    }

    protected function createProject(Authenticatable $user, string $name = 'Test Project', ?string $description = null): Project
    {
        return Project::query()->create([
            'owner_id' => $user->getAuthIdentifier(),
            'name' => $name,
            'description' => $description,
        ]);
    }

    protected function addMember(Project $project, Authenticatable $user, string $role = 'member'): ProjectMember
    {
        return ProjectMember::query()->create([
            'project_id' => $project->id,
            'user_id' => $user->getAuthIdentifier(),
            'role' => $role,
        ]);
    }

    protected function defineRoutes($router): void
    {
        $router->get('/login', fn () => 'login')->name('login');
    }
}
