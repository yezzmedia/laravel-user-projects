<?php

declare(strict_types=1);

namespace YezzMedia\UserProjects\Support;

use Illuminate\Support\Collection;
use YezzMedia\UserProjects\Models\Project;
use YezzMedia\UserProjects\Models\ProjectMember;

final class ProjectMemberManager
{
    public function members(Project $project): Collection
    {
        return $project->members()->with('user')->get();
    }

    public function addMember(Project $project, int $userId, string $role = 'member'): ProjectMember
    {
        return ProjectMember::query()->create([
            'project_id' => $project->id,
            'user_id' => $userId,
            'role' => $role,
        ]);
    }

    public function removeMember(Project $project, int $userId): bool
    {
        return (bool) ProjectMember::query()
            ->where('project_id', $project->id)
            ->where('user_id', $userId)
            ->delete();
    }

    public function updateRole(Project $project, int $userId, string $role): bool
    {
        return (bool) ProjectMember::query()
            ->where('project_id', $project->id)
            ->where('user_id', $userId)
            ->update(['role' => $role]);
    }

    public function isOwner(Project $project, int $userId): bool
    {
        return $project->members()
            ->where('user_id', $userId)
            ->where('role', 'owner')
            ->exists();
    }

    public function isAdminOrOwner(Project $project, int $userId): bool
    {
        return $project->members()
            ->where('user_id', $userId)
            ->whereIn('role', ['owner', 'admin'])
            ->exists();
    }
}
