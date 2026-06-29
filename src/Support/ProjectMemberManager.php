<?php

declare(strict_types=1);

namespace YezzMedia\UserProjects\Support;

use Illuminate\Support\Collection;
use YezzMedia\UserProjects\Models\Project;
use YezzMedia\UserProjects\Models\ProjectInvitation;
use YezzMedia\UserProjects\Models\ProjectMember;

final class ProjectMemberManager
{
    public function members(Project $project): Collection
    {
        return $project->members()->with('user')->get();
    }

    public function memberRole(Project $project, int $userId): ?string
    {
        $member = ProjectMember::query()
            ->where('project_id', $project->id)
            ->where('user_id', $userId)
            ->first();

        return $member?->role;
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

    public function hierarchyWeight(string $role): int
    {
        return (int) config('user-projects.members.role_weights.'.$role, 0);
    }

    public function wouldLeaveProjectWithoutAdmin(Project $project, int $userId): bool
    {
        $member = ProjectMember::query()
            ->where('project_id', $project->id)
            ->where('user_id', $userId)
            ->first();

        if ($member === null) {
            return false;
        }

        if ($this->hierarchyWeight($member->role) < 50) {
            return false;
        }

        $remaining = ProjectMember::query()
            ->where('project_id', $project->id)
            ->where('user_id', '!=', $userId)
            ->get()
            ->filter(fn (ProjectMember $m) => $this->hierarchyWeight($m->role) >= 50)
            ->count();

        return $remaining === 0;
    }

    public function canAssignRole(Project $project, int $userId, string $targetRole): bool
    {
        $userRole = $this->memberRole($project, $userId);

        if ($userRole === null) {
            return false;
        }

        return $this->hierarchyWeight($userRole) >= $this->hierarchyWeight($targetRole);
    }

    public function canManageMember(Project $project, int $currentUserId, int $targetUserId): bool
    {
        if ($currentUserId === $targetUserId) {
            return false;
        }

        $currentUserRole = $this->memberRole($project, $currentUserId);
        $targetUserRole = $this->memberRole($project, $targetUserId);

        if ($currentUserRole === null || $targetUserRole === null) {
            return false;
        }

        return $this->hierarchyWeight($currentUserRole) >= $this->hierarchyWeight($targetUserRole);
    }

    public function assignableRoles(Project $project, int $userId): array
    {
        $userRole = $this->memberRole($project, $userId);

        if ($userRole === null) {
            return [];
        }

        $userWeight = $this->hierarchyWeight($userRole);
        $roles = [];

        foreach (config('user-projects.members.roles', []) as $roleKey => $roleLabel) {
            if ($userWeight >= $this->hierarchyWeight($roleKey)) {
                $roles[$roleKey] = $roleLabel;
            }
        }

        return $roles;
    }

    public function invitations(Project $project): Collection
    {
        return $project->invitations()->with('invitedBy')->latest()->get();
    }

    public function pendingInvitationsForProject(Project $project): Collection
    {
        return $project->invitations()
            ->whereNull('accepted_at')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->with('invitedBy')
            ->latest()
            ->get();
    }

    public function pendingInvitationsForUser(mixed $user): Collection
    {
        if ($user === null) {
            return collect();
        }

        return ProjectInvitation::query()
            ->where('email', $user->getEmailForVerification() ?? $user->email)
            ->whereNull('accepted_at')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->with('project')
            ->latest()
            ->get();
    }

    public function inviteByEmail(Project $project, string $email, string $role, int $invitedByUserId): ?ProjectInvitation
    {
        $userModel = config('auth.providers.users.model');

        if ($userModel !== null) {
            $invitedUser = $userModel::query()->where('email', $email)->first();

            if ($invitedUser !== null) {
                $existing = ProjectMember::query()
                    ->where('project_id', $project->id)
                    ->where('user_id', $invitedUser->getAuthIdentifier())
                    ->first();

                if ($existing !== null) {
                    return null;
                }
            }
        }

        return ProjectInvitation::query()->create([
            'project_id' => $project->id,
            'email' => $email,
            'role' => $role,
            'token' => ProjectInvitation::generateToken(),
            'invited_by_user_id' => $invitedByUserId,
        ]);
    }

    public function pendingInvitationForProjectAndUser(Project $project, mixed $user): ?ProjectInvitation
    {
        if ($user === null) {
            return null;
        }

        $email = $user->getEmailForVerification() ?? $user->email;

        if ($email === null) {
            return null;
        }

        return ProjectInvitation::query()
            ->where('project_id', $project->id)
            ->where('email', $email)
            ->whereNull('accepted_at')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->first();
    }

    public function acceptInvitationForProject(Project $project, mixed $user): ?ProjectMember
    {
        $invitation = $this->pendingInvitationForProjectAndUser($project, $user);

        if ($invitation === null) {
            return null;
        }

        $email = $user->getEmailForVerification() ?? $user->email;

        $existing = $this->member($project, (int) $user->getAuthIdentifier());

        if ($existing !== null) {
            $invitation->delete();

            return $existing;
        }

        $member = $this->addMember($project, (int) $user->getAuthIdentifier(), $invitation->role);
        $invitation->update(['accepted_at' => now()]);

        return $member;
    }

    public function member(Project $project, int $userId): ?ProjectMember
    {
        return ProjectMember::query()
            ->where('project_id', $project->id)
            ->where('user_id', $userId)
            ->first();
    }

    public function declineInvitationForProject(Project $project, mixed $user): bool
    {
        $invitation = $this->pendingInvitationForProjectAndUser($project, $user);

        if ($invitation === null) {
            return false;
        }

        return (bool) $invitation->delete();
    }

    public function acceptInvitation(string $token): ?ProjectMember
    {
        $invitation = ProjectInvitation::query()
            ->where('token', $token)
            ->whereNull('accepted_at')
            ->first();

        if ($invitation === null || $invitation->isExpired()) {
            return null;
        }

        $user = $this->resolveUserByEmail($invitation->email);

        if ($user === null) {
            return null;
        }

        $member = $this->addMember(
            $invitation->project,
            (int) $user->getAuthIdentifier(),
            $invitation->role,
        );

        $invitation->update(['accepted_at' => now()]);

        return $member;
    }

    public function cancelInvitation(int $invitationId): bool
    {
        return (bool) ProjectInvitation::query()
            ->where('id', $invitationId)
            ->whereNull('accepted_at')
            ->delete();
    }

    public function leaveProject(Project $project, int $userId): bool
    {
        return (bool) ProjectMember::query()
            ->where('project_id', $project->id)
            ->where('user_id', $userId)
            ->delete();
    }

    private function resolveUserByEmail(string $email): mixed
    {
        $model = config('auth.providers.users.model');

        return $model::query()->where('email', $email)->first();
    }
}
