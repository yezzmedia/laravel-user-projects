<?php

declare(strict_types=1);

namespace YezzMedia\UserProjects\Support;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Collection;
use YezzMedia\UserProjects\Models\Project;
use YezzMedia\UserProjects\Models\ProjectMember;

final class ProjectManager
{
    public function __construct(
        private readonly ProjectStoreSetup $storeSetup,
    ) {}

    public function listForUser(?Authenticatable $user): Collection
    {
        if ($user === null || ! $this->storeSetup->storeReady()) {
            return collect();
        }

        $userId = (int) $user->getAuthIdentifier();

        $projectIds = ProjectMember::query()
            ->where('user_id', $userId)
            ->pluck('project_id');

        return Project::query()
            ->where('owner_id', $userId)
            ->orWhereIn('id', $projectIds)
            ->withCount('members')
            ->orderByDesc('created_at')
            ->get();
    }

    public function create(Authenticatable $user, string $name, ?string $description = null): Project
    {
        $project = Project::query()->create([
            'owner_id' => $user->getAuthIdentifier(),
            'name' => $name,
            'description' => $description,
            'status' => 'active',
        ]);

        ProjectMember::query()->create([
            'project_id' => $project->id,
            'user_id' => $user->getAuthIdentifier(),
            'role' => 'owner',
        ]);

        return $project;
    }

    public function update(Project $project, array $data): bool
    {
        return $project->update($data);
    }

    public function delete(Project $project): bool
    {
        $project->members()->delete();

        return (bool) $project->delete();
    }

    public function findByIdentifier(string $identifier): ?Project
    {
        return Project::query()->where('id', $identifier)->first();
    }

    public function findByIdentifierOrFail(string $identifier): Project
    {
        return Project::query()->where('id', $identifier)->firstOrFail();
    }
}
