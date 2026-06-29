<?php

declare(strict_types=1);

namespace YezzMedia\UserProjects\Pages;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Url;
use Livewire\WithFileUploads;
use YezzMedia\UserProjects\Models\Project;
use YezzMedia\UserProjects\Models\ProjectActivity;
use YezzMedia\UserProjects\Models\ProjectInvitation;
use YezzMedia\UserProjects\Models\ProjectMember;
use YezzMedia\UserProjects\Notifications\ProjectInvitationNotification;
use YezzMedia\UserProjects\Notifications\ProjectRoleChangedNotification;
use YezzMedia\UserProjects\Support\ProjectAddonManager;
use YezzMedia\UserProjects\Support\ProjectManager;
use YezzMedia\UserProjects\Support\ProjectMemberManager;

final class ProjectsOverviewPage extends UserProjectsPage
{
    use WithFileUploads;

    protected static ?string $slug = 'projects';

    #[Url]
    public ?string $project = null;

    public string $activeTab = 'overview';

    public string $inviteEmail = '';

    public string $inviteRole = 'member';

    public string $editName = '';

    public ?string $editDescription = null;

    public string $editStatus = 'active';

    public $uploadPhoto = null;

    #[Url]
    public string $search = '';

    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

    #[Url]
    public bool $showArchived = false;

    public function selectProject(string $projectId): void
    {
        $this->project = $projectId;
        $this->activeTab = 'overview';
    }

    public function backToList(): void
    {
        $this->project = null;
        $this->activeTab = 'overview';
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;

        if ($tab === 'settings') {
            $this->mountProjectSettings();
        }
    }

    public function mountProjectSettings(): void
    {
        $project = $this->resolvedProject;

        if ($project === null) {
            return;
        }

        $this->editName = $project->name;
        $this->editDescription = $project->description;
        $this->editStatus = $project->status;
    }

    public function saveProjectSettings(): void
    {
        $project = $this->resolvedProject;
        $user = auth(config('user-projects.panel.guard', 'web'))->user();

        if ($project === null || $user === null) {
            return;
        }

        $memberManager = app(ProjectMemberManager::class);
        $role = $memberManager->memberRole($project, (int) $user->getAuthIdentifier());

        if (! in_array($role, ['owner', 'admin'], true)) {
            session()->flash('error', __('user-projects::ui.cannot_edit_project'));

            return;
        }

        $statuses = implode(',', array_keys(config('user-projects.projects.statuses', ['active' => 'Active'])));

        $this->validate([
            'editName' => 'required|string|max:255',
            'editDescription' => 'nullable|string|max:5000',
            'editStatus' => "required|string|in:{$statuses}",
        ]);

        app(ProjectManager::class)->update($project, [
            'name' => $this->editName,
            'description' => $this->editDescription,
            'status' => $this->editStatus,
        ]);

        ProjectActivity::log(
            $project->id,
            'project_updated',
            __('user-projects::ui.activity_project_updated'),
            (int) $user->getAuthIdentifier(),
        );

        session()->flash('success', __('user-projects::ui.project_updated'));
    }

    public function saveProjectPhoto(): void
    {
        $project = $this->resolvedProject;
        $user = auth(config('user-projects.panel.guard', 'web'))->user();

        if ($project === null || $user === null) {
            return;
        }

        $memberManager = app(ProjectMemberManager::class);
        $role = $memberManager->memberRole($project, (int) $user->getAuthIdentifier());

        if (! in_array($role, ['owner', 'admin'], true)) {
            session()->flash('error', __('user-projects::ui.cannot_edit_project'));

            return;
        }

        $this->validate([
            'uploadPhoto' => 'nullable|image|max:2048',
        ]);

        if ($this->uploadPhoto === null) {
            return;
        }

        if ($project->photo_path !== null) {
            Storage::disk('public')->delete($project->photo_path);
        }

        $path = $this->uploadPhoto->store('project-photos', 'public');

        app(ProjectManager::class)->update($project, ['photo_path' => $path]);

        $this->uploadPhoto = null;

        session()->flash('success', __('user-projects::ui.photo_uploaded'));
    }

    public function removeProjectPhoto(): void
    {
        $project = $this->resolvedProject;
        $user = auth(config('user-projects.panel.guard', 'web'))->user();

        if ($project === null || $user === null) {
            return;
        }

        $memberManager = app(ProjectMemberManager::class);
        $role = $memberManager->memberRole($project, (int) $user->getAuthIdentifier());

        if (! in_array($role, ['owner', 'admin'], true)) {
            return;
        }

        if ($project->photo_path !== null) {
            Storage::disk('public')->delete($project->photo_path);
        }

        app(ProjectManager::class)->update($project, ['photo_path' => null]);

        session()->flash('success', __('user-projects::ui.photo_removed'));
    }

    public function toggleAddon(string $addonKey): void
    {
        $project = $this->resolvedProject;

        if ($project === null) {
            return;
        }

        app(ProjectAddonManager::class)->toggle($project, $addonKey);
    }

    public function deleteProject(string $projectId): void
    {
        $user = auth(config('user-projects.panel.guard', 'web'))->user();

        $resolved = app(ProjectManager::class)->findByIdentifier($projectId);

        if ($resolved !== null) {
            app(ProjectManager::class)->delete($resolved);
        }

        $this->project = null;
        $this->activeTab = 'overview';
    }

    public function inviteMember(): void
    {
        $project = $this->resolvedProject;
        $user = auth(config('user-projects.panel.guard', 'web'))->user();

        if ($project === null || $user === null) {
            return;
        }

        $this->validate([
            'inviteEmail' => 'required|email',
            'inviteRole' => 'required|string',
        ]);

        $memberManager = app(ProjectMemberManager::class);

        if (! $memberManager->canAssignRole($project, (int) $user->getAuthIdentifier(), $this->inviteRole)) {
            session()->flash('error', __('user-projects::ui.cannot_assign_role'));

            return;
        }

        $invitation = $memberManager->inviteByEmail($project, $this->inviteEmail, $this->inviteRole, (int) $user->getAuthIdentifier());

        if ($invitation === null) {
            session()->flash('error', __('user-projects::ui.user_already_member'));

            return;
        }

        ProjectActivity::log(
            $project->id,
            'member_invited',
            __('user-projects::ui.activity_member_invited', ['email' => $this->inviteEmail]),
            (int) $user->getAuthIdentifier(),
            ['email' => $this->inviteEmail, 'role' => $this->inviteRole],
        );

        $userModel = config('auth.providers.users.model');
        $invitedUser = $userModel::query()->where('email', $this->inviteEmail)->first();

        if ($invitedUser !== null) {
            Notification::send($invitedUser, new ProjectInvitationNotification($project, $invitation));
        }

        $this->inviteEmail = '';
        $this->inviteRole = 'member';
    }

    public function acceptMyInvitation(): void
    {
        $project = $this->resolvedProject;
        $user = auth(config('user-projects.panel.guard', 'web'))->user();

        if ($project === null || $user === null) {
            return;
        }

        $member = app(ProjectMemberManager::class)->acceptInvitationForProject($project, $user);

        if ($member !== null) {
            ProjectActivity::log(
                $project->id,
                'member_accepted',
                __('user-projects::ui.activity_member_accepted'),
                (int) $user->getAuthIdentifier(),
            );
        }
    }

    public function declineMyInvitation(): void
    {
        $project = $this->resolvedProject;
        $user = auth(config('user-projects.panel.guard', 'web'))->user();

        if ($project === null || $user === null) {
            return;
        }

        $declined = app(ProjectMemberManager::class)->declineInvitationForProject($project, $user);

        if ($declined) {
            ProjectActivity::log(
                $project->id,
                'member_declined',
                __('user-projects::ui.activity_member_declined'),
                (int) $user->getAuthIdentifier(),
            );
        }
    }

    public function acceptInvitationFor(int $projectId): void
    {
        $user = auth(config('user-projects.panel.guard', 'web'))->user();

        if ($user === null) {
            return;
        }

        $project = app(ProjectManager::class)->findByIdentifier((string) $projectId);

        if ($project === null) {
            return;
        }

        $member = app(ProjectMemberManager::class)->acceptInvitationForProject($project, $user);

        if ($member !== null) {
            ProjectActivity::log(
                $project->id,
                'member_accepted',
                __('user-projects::ui.activity_member_accepted'),
                (int) $user->getAuthIdentifier(),
            );
        }
    }

    public function declineInvitationFor(int $projectId): void
    {
        $user = auth(config('user-projects.panel.guard', 'web'))->user();

        if ($user === null) {
            return;
        }

        $project = app(ProjectManager::class)->findByIdentifier((string) $projectId);

        if ($project === null) {
            return;
        }

        $declined = app(ProjectMemberManager::class)->declineInvitationForProject($project, $user);

        if ($declined) {
            ProjectActivity::log(
                $project->id,
                'member_declined',
                __('user-projects::ui.activity_member_declined'),
                (int) $user->getAuthIdentifier(),
            );
        }
    }

    public function cancelInvitation(int $invitationId): void
    {
        $project = $this->resolvedProject;
        $user = auth(config('user-projects.panel.guard', 'web'))->user();

        if ($project === null || $user === null) {
            return;
        }

        $invitation = ProjectInvitation::query()->find($invitationId);

        if ($invitation === null || $invitation->project_id !== $project->id) {
            return;
        }

        $invitedEmail = $invitation->email;
        $invitedRole = $invitation->role;

        app(ProjectMemberManager::class)->cancelInvitation($invitationId);

        ProjectActivity::log(
            $project->id,
            'member_invitation_cancelled',
            __('user-projects::ui.activity_invitation_cancelled'),
            (int) $user->getAuthIdentifier(),
            ['email' => $invitedEmail, 'role' => $invitedRole],
        );
    }

    public function changeMemberRole(int $memberId, string $newRole): void
    {
        $project = $this->resolvedProject;
        $user = auth(config('user-projects.panel.guard', 'web'))->user();

        if ($project === null || $user === null) {
            return;
        }

        $member = ProjectMember::query()->find($memberId);

        if ($member === null || $member->project_id !== $project->id) {
            return;
        }

        $memberManager = app(ProjectMemberManager::class);

        if (! $memberManager->canManageMember($project, (int) $user->getAuthIdentifier(), $member->user_id)) {
            session()->flash('error', __('user-projects::ui.cannot_manage_member'));

            return;
        }

        if (! $memberManager->canAssignRole($project, (int) $user->getAuthIdentifier(), $newRole)) {
            session()->flash('error', __('user-projects::ui.cannot_assign_role'));

            return;
        }

        if ($memberManager->hierarchyWeight($newRole) < 50 && $memberManager->wouldLeaveProjectWithoutAdmin($project, $member->user_id)) {
            session()->flash('error', __('user-projects::ui.project_needs_admin'));

            return;
        }

        $oldRole = $member->role;
        $memberManager->updateRole($project, $member->user_id, $newRole);

        ProjectActivity::log(
            $project->id,
            'role_changed',
            __('user-projects::ui.activity_role_changed'),
            (int) $user->getAuthIdentifier(),
            ['user_id' => $member->user_id, 'old_role' => $oldRole, 'new_role' => $newRole],
        );

        $targetUser = $member->user()->first();

        if ($targetUser !== null) {
            Notification::send($targetUser, new ProjectRoleChangedNotification($project, $newRole, $oldRole));
        }
    }

    public function removeMember(int $memberId): void
    {
        $project = $this->resolvedProject;
        $user = auth(config('user-projects.panel.guard', 'web'))->user();

        if ($project === null || $user === null) {
            return;
        }

        $member = ProjectMember::query()->find($memberId);

        if ($member === null || $member->project_id !== $project->id) {
            return;
        }

        $memberManager = app(ProjectMemberManager::class);

        if (! $memberManager->canManageMember($project, (int) $user->getAuthIdentifier(), $member->user_id)) {
            session()->flash('error', __('user-projects::ui.cannot_manage_member'));

            return;
        }

        if ($memberManager->wouldLeaveProjectWithoutAdmin($project, $member->user_id)) {
            session()->flash('error', __('user-projects::ui.project_needs_admin'));

            return;
        }

        $removedUserId = $member->user_id;
        $memberManager->removeMember($project, $member->user_id);

        ProjectActivity::log(
            $project->id,
            'member_removed',
            __('user-projects::ui.activity_member_removed'),
            (int) $user->getAuthIdentifier(),
            ['user_id' => $removedUserId],
        );
    }

    public function leaveProject(): void
    {
        $project = $this->resolvedProject;
        $user = auth(config('user-projects.panel.guard', 'web'))->user();

        if ($project === null || $user === null) {
            return;
        }

        $memberManager = app(ProjectMemberManager::class);
        $userId = (int) $user->getAuthIdentifier();

        if ($memberManager->isOwner($project, $userId)) {
            session()->flash('error', __('user-projects::ui.owner_cannot_leave'));

            return;
        }

        if ($memberManager->wouldLeaveProjectWithoutAdmin($project, $userId)) {
            session()->flash('error', __('user-projects::ui.project_needs_admin'));

            return;
        }

        $memberManager->leaveProject($project, $userId);

        ProjectActivity::log(
            $project->id,
            'member_left',
            __('user-projects::ui.activity_member_left'),
            $userId,
        );

        $this->project = null;
        $this->activeTab = 'overview';
    }

    public function toggleSort(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function toggleArchived(): void
    {
        $this->showArchived = ! $this->showArchived;
    }

    public function duplicateProject(): void
    {
        $project = $this->resolvedProject;
        $user = auth(config('user-projects.panel.guard', 'web'))->user();

        if ($project === null || $user === null) {
            return;
        }

        $copy = app(ProjectManager::class)->duplicate($project, $user, '(Copy)');

        session()->flash('success', __('user-projects::ui.project_duplicated'));
    }

    public function transferOwnership(int $memberId): void
    {
        $project = $this->resolvedProject;
        $user = auth(config('user-projects.panel.guard', 'web'))->user();

        if ($project === null || $user === null) {
            return;
        }

        $member = ProjectMember::query()->find($memberId);

        if ($member === null || $member->project_id !== $project->id) {
            return;
        }

        $memberManager = app(ProjectMemberManager::class);

        if (! $memberManager->isOwner($project, (int) $user->getAuthIdentifier())) {
            session()->flash('error', __('user-projects::ui.only_owner_can_transfer'));

            return;
        }

        if ((int) $member->user_id === (int) $user->getAuthIdentifier()) {
            return;
        }

        $newOwnerId = (int) $member->user_id;
        $oldOwnerId = (int) $user->getAuthIdentifier();

        app(ProjectManager::class)->transferOwnership($project, $newOwnerId, $oldOwnerId);

        ProjectActivity::log(
            $project->id,
            'role_changed',
            __('user-projects::ui.activity_ownership_transferred'),
            $oldOwnerId,
            ['from_user_id' => $oldOwnerId, 'to_user_id' => $newOwnerId],
        );

        session()->flash('success', __('user-projects::ui.ownership_transferred'));
    }

    protected string $view = 'user-projects::pages.overview';

    protected static string|\UnitEnum|null $navigationGroup = 'Projects';

    protected static ?string $navigationLabel = 'Overview';

    protected static string|\BackedEnum|null $navigationIcon = 'squares-2x2';

    protected static ?int $navigationSort = 10;

    public static function getDefaultSlug(): string
    {
        return 'projects';
    }

    protected function getPageTitle(): string
    {
        if ($this->project !== null) {
            $resolved = $this->resolvedProject;

            if ($resolved !== null) {
                return $resolved->name;
            }
        }

        return $this->translate('user-projects::ui.projects_title', 'Projects');
    }

    protected function getPageDescription(): string
    {
        if ($this->project !== null) {
            return $this->translate('user-projects::ui.project_overview_description', 'View project details and manage settings.');
        }

        return $this->translate('user-projects::ui.projects_description', 'Manage your projects.');
    }

    protected function pageData(): array
    {
        $user = auth(config('user-projects.panel.guard', 'web'))->user();
        $addonManager = app(ProjectAddonManager::class);
        $memberManager = app(ProjectMemberManager::class);

        $currentUserRole = null;
        $assignableRoles = [];
        $pendingInvitations = collect();
        $currentUserInvitation = null;
        $userPendingInvitations = collect();
        $activities = collect();

        if ($user !== null) {
            $userPendingInvitations = $memberManager->pendingInvitationsForUser($user);
        }

        if ($this->resolvedProject !== null && $user !== null) {
            $currentUserRole = $memberManager->memberRole($this->resolvedProject, (int) $user->getAuthIdentifier());
            $assignableRoles = $memberManager->assignableRoles($this->resolvedProject, (int) $user->getAuthIdentifier());
            $pendingInvitations = $memberManager->pendingInvitationsForProject($this->resolvedProject);
            $currentUserInvitation = $memberManager->pendingInvitationForProjectAndUser($this->resolvedProject, $user);
            $activities = ProjectActivity::query()
                ->where('project_id', $this->resolvedProject->id)
                ->with('user')
                ->latest('created_at')
                ->take(50)
                ->get();
        }

        $projects = $user !== null
            ? $this->filterAndSortProjects($user)
            : collect();

        return [
            'projects' => $projects,
            'projectCount' => Project::query()->count(),
            'selectedProject' => $this->resolvedProject,
            'members' => $this->resolvedProject !== null
                ? $memberManager->members($this->resolvedProject)
                : collect(),
            'activeTab' => $this->activeTab,
            'availableAddons' => $addonManager->all(),
            'addonActivations' => $this->resolvedProject !== null
                ? $addonManager->activationStatus($this->resolvedProject)
                : [],
            'currentUserRole' => $currentUserRole,
            'assignableRoles' => $assignableRoles,
            'pendingInvitations' => $pendingInvitations,
            'currentUserInvitation' => $currentUserInvitation,
            'userPendingInvitations' => $userPendingInvitations,
            'activities' => $activities,
            'search' => $this->search,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
            'showArchived' => $this->showArchived,
        ];
    }

    private function filterAndSortProjects(mixed $user): Collection
    {
        $manager = app(ProjectManager::class);
        $projects = $manager->listForUser($user);

        if (! $this->showArchived) {
            $projects = $projects->filter(fn (Project $p) => $p->status !== 'archived');
        }

        if ($this->search !== '') {
            $search = $this->search;
            $projects = $projects->filter(fn (Project $p) => str_contains(strtolower($p->name), strtolower($search))
                || ($p->description !== null && str_contains(strtolower($p->description), strtolower($search))));
        }

        $sortField = $this->sortField;
        $sortDirection = $this->sortDirection;

        $projects = $projects->sortBy(fn (Project $p) => match ($sortField) {
            'name' => strtolower($p->name),
            'members_count' => $p->members_count ?? 0,
            default => $p->created_at->timestamp,
        }, SORT_REGULAR, $sortDirection === 'desc');

        return $projects->values();
    }

    public function getResolvedProjectProperty(): ?Project
    {
        if ($this->project === null) {
            return null;
        }

        return app(ProjectManager::class)->findByIdentifier($this->project);
    }
}
