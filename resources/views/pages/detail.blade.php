@php
    /** @var \YezzMedia\UserProjects\Models\Project|null $project */
    /** @var \Illuminate\Support\Collection $members */
    /** @var string $activeTab */
    /** @var array $availableAddons */
    /** @var array $addonActivations */
    /** @var string|null $currentUserRole */
    /** @var array $assignableRoles */
    /** @var \Illuminate\Support\Collection $pendingInvitations */
    /** @var \YezzMedia\UserProjects\Models\ProjectInvitation|null $currentUserInvitation */
    /** @var \Illuminate\Support\Collection $activities */
@endphp

<div class="space-y-6">
    @if ($project === null)
        <x-user-projects::page-header :title="__('user-projects::ui.project_not_found')" color="rose">
            <x-slot:icon><x-user-projects::icon name="exclamation-triangle" class="h-5 w-5" /></x-slot:icon>
        </x-user-projects::page-header>
    @else
        <x-user-projects::page-header
            :title="$project->name"
            :subtitle="$project->description ?: __('user-projects::ui.project_overview_description')"
            color="indigo"
        >
            <x-slot:icon>
                @if ($project->photo_url)
                    <img src="{{ $project->photo_url }}" alt="{{ $project->name }}" class="h-10 w-10 rounded-full object-cover">
                @else
                    <span class="text-lg font-bold">{{ strtoupper(substr($project->name, 0, 2)) }}</span>
                @endif
            </x-slot:icon>
            <x-slot:badges>
                <x-user-projects::badge :class="$project->status === 'active' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'">
                    {{ ucfirst($project->status) }}
                </x-user-projects::badge>
                <x-user-projects::badge class="bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400">
                    <x-user-projects::icon name="users" class="mr-1 h-3 w-3" />
                    {{ trans_choice('user-projects::ui.member_count', $members->count(), ['count' => $members->count()]) }}
                </x-user-projects::badge>
            </x-slot:badges>
            <x-slot:actions>
                <button type="button" wire:click="backToList" class="inline-flex items-center gap-1.5 border border-gray-300 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                    <x-user-projects::icon name="arrow-left" class="h-3.5 w-3.5" />
                    {{ __('user-projects::ui.projects') }}
                </button>
            </x-slot:actions>
        </x-user-projects::page-header>

        <div class="flex gap-1 border border-gray-200 bg-white p-1 shadow-sm dark:border-gray-700 dark:bg-gray-900">
            @foreach (['overview' => __('user-projects::ui.overview'), 'members' => __('user-projects::ui.members'), 'addons' => __('user-projects::ui.addons'), 'activity' => __('user-projects::ui.activity'), 'settings' => __('user-projects::ui.settings')] as $tab => $label)
                <button type="button" wire:click="setTab('{{ $tab }}')"
                    @class([
                        'flex-1 px-4 py-2 text-sm font-medium transition-colors',
                        'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/50 dark:text-indigo-300' => $activeTab === $tab,
                        'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white' => $activeTab !== $tab,
                    ])
                >
                    {{ $label }}
                </button>
            @endforeach
        </div>

        @if ($currentUserInvitation !== null && $currentUserRole === null)
            <div class="border border-amber-200 bg-amber-50/50 p-4 dark:border-amber-800 dark:bg-amber-950/20">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <x-user-projects::icon name="users" class="h-5 w-5 shrink-0 text-amber-500" />
                        <div>
                            <p class="text-sm font-medium text-amber-800 dark:text-amber-200">
                                {{ __('user-projects::ui.invitation_pending_banner', ['role' => __("user-projects::ui.role_{$currentUserInvitation->role}")]) }}
                            </p>
                            <p class="text-xs text-amber-600 dark:text-amber-400">
                                {{ __('user-projects::ui.invitation_pending_description', ['name' => $currentUserInvitation->invitedBy?->name ?? $currentUserInvitation->invitedBy?->email ?? 'Unknown']) }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <button type="button" wire:click="acceptMyInvitation" wire:loading.attr="disabled"
                            class="inline-flex items-center gap-1.5 border border-amber-300 bg-white px-3 py-1.5 text-xs font-medium text-amber-700 hover:bg-amber-50 disabled:opacity-50 dark:border-amber-600 dark:bg-gray-800 dark:text-amber-300 dark:hover:bg-amber-900/20">
                            <span wire:loading.remove wire:target="acceptMyInvitation"><x-user-projects::icon name="check" class="h-3.5 w-3.5" /></span>
                            <span wire:loading wire:target="acceptMyInvitation"><x-user-projects::icon name="loader" class="h-3.5 w-3.5 animate-spin" /></span>
                            {{ __('user-projects::ui.accept') }}
                        </button>
                        <button type="button" wire:click="declineMyInvitation" wire:confirm="{{ __('user-projects::ui.decline_invitation_confirm') }}" wire:loading.attr="disabled"
                            class="inline-flex items-center gap-1.5 border border-gray-300 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                            <span wire:loading.remove wire:target="declineMyInvitation"><x-user-projects::icon name="x" class="h-3.5 w-3.5" /></span>
                            <span wire:loading wire:target="declineMyInvitation"><x-user-projects::icon name="loader" class="h-3.5 w-3.5 animate-spin" /></span>
                            {{ __('user-projects::ui.decline') }}
                        </button>
                    </div>
                </div>
            </div>
        @endif

        @if ($activeTab === 'overview')
            <div class="grid gap-6 lg:grid-cols-2">
                <div class="border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
                    <div class="px-4 pt-4 sm:px-6 sm:pt-6">
                        <x-user-projects::section-header :title="__('user-projects::ui.details')" color="indigo" />
                    </div>
                    <div class="p-4 sm:p-6 pt-0">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <x-user-projects::stat-card :label="__('user-projects::ui.members')" :value="$members->count()" />
                            <div class="border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
                                <div class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('user-projects::ui.created_this_month') }}</div>
                                <div class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">{{ $project->created_at->format('F j, Y') }}</div>
                                <div class="mt-1 text-xs text-gray-400 dark:text-gray-500">{{ $project->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
                    <div class="px-4 pt-4 sm:px-6 sm:pt-6">
                        <x-user-projects::section-header :title="__('user-projects::ui.info')" color="indigo" />
                    </div>
                    <div class="p-4 sm:p-6 pt-0 space-y-4">
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('user-projects::ui.projects') }}</p>
                            <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $project->name }}</p>
                        </div>
                        @if ($project->description)
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Description</p>
                                <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">{{ $project->description }}</p>
                            </div>
                        @endif
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</p>
                            <x-user-projects::badge :class="$project->status === 'active' ? 'mt-1 bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'mt-1 bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'">
                                {{ ucfirst($project->status) }}
                            </x-user-projects::badge>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if ($activeTab === 'members')
            @if ($assignableRoles !== [])
                <div class="border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
                    <div class="px-4 pt-4 sm:px-6 sm:pt-6">
                        <x-user-projects::section-header :title="__('user-projects::ui.invite_member')" color="indigo" />
                    </div>
                    <div class="p-4 sm:p-6 pt-0">
                        @if (session('error'))
                            <div class="mb-4 rounded-lg border border-rose-200 bg-rose-50 p-3 text-sm text-rose-700 dark:border-rose-800 dark:bg-rose-950/30 dark:text-rose-300">
                                {{ session('error') }}
                            </div>
                        @endif
                        <div class="flex items-end gap-3">
                            <div class="flex-1">
                                <label for="inviteEmail" class="block text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">{{ __('user-projects::ui.email') }}</label>
                                <input type="email" id="inviteEmail" wire:model="inviteEmail" placeholder="email@example.com"
                                    class="block w-full border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-indigo-500 focus:outline-none dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500 dark:focus:border-indigo-400">
                            </div>
                            <div class="w-40">
                                <label for="inviteRole" class="block text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">{{ __('user-projects::ui.role') }}</label>
                                <select id="inviteRole" wire:model="inviteRole"
                                    class="block w-full border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-indigo-500 focus:outline-none dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:focus:border-indigo-400">
                                    @foreach ($assignableRoles as $roleKey => $roleLabel)
                                        <option value="{{ $roleKey }}">{{ $roleLabel }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="button" wire:click="inviteMember" wire:loading.attr="disabled"
                                class="inline-flex items-center gap-1.5 border border-indigo-300 bg-white px-4 py-2 text-xs font-medium text-indigo-700 hover:bg-indigo-50 disabled:opacity-50 dark:border-indigo-700 dark:bg-gray-800 dark:text-indigo-300 dark:hover:bg-indigo-900/20">
                                <x-user-projects::icon name="plus" class="h-3.5 w-3.5" />
                                <span wire:loading.remove wire:target="inviteMember">{{ __('user-projects::ui.invite') }}</span>
                                <span wire:loading wire:target="inviteMember" class="inline-flex items-center gap-1"><x-user-projects::icon name="loader" class="h-3 w-3 animate-spin" /> {{ __('user-projects::ui.inviting') }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            @if ($pendingInvitations->isNotEmpty())
                <div class="border border-amber-200 bg-white shadow-sm dark:border-amber-800 dark:bg-gray-900">
                    <div class="px-4 pt-4 sm:px-6 sm:pt-6">
                        <x-user-projects::section-header :title="__('user-projects::ui.pending_invitations')" color="amber" />
                    </div>
                    <div class="p-4 sm:p-6 pt-0">
                        <div class="-mx-4 -mb-4 overflow-hidden sm:-mx-6 sm:-mb-6">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-800">
                                    <tr>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 sm:px-6">{{ __('user-projects::ui.email') }}</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 sm:px-6">{{ __('user-projects::ui.role') }}</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 sm:px-6">{{ __('user-projects::ui.invited_by') }}</th>
                                        <th scope="col" class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 sm:px-6">{{ __('user-projects::ui.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-900">
                                    @foreach ($pendingInvitations as $invitation)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                            <td class="whitespace-nowrap px-4 py-4 text-sm font-medium text-gray-900 dark:text-white sm:px-6">{{ $invitation->email }}</td>
                                            <td class="whitespace-nowrap px-4 py-4 text-sm sm:px-6">
                                                <x-user-projects::badge :class="match ($invitation->role) {
                                                    'owner' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
                                                    'admin' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
                                                    default => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
                                                }">
                                                    {{ ucfirst($invitation->role) }}
                                                </x-user-projects::badge>
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-500 dark:text-gray-400 sm:px-6">
                                                {{ $invitation->invitedBy?->name ?? $invitation->invitedBy?->email ?? 'Unknown' }}
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-4 text-right text-sm sm:px-6">
                                                <button type="button" wire:click="cancelInvitation({{ $invitation->id }})" wire:confirm="{{ __('user-projects::ui.cancel_invitation_confirm') }}"
                                                    class="text-xs font-medium text-rose-600 hover:text-rose-800 dark:text-rose-400 dark:hover:text-rose-300">
                                                    {{ __('user-projects::ui.cancel') }}
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            <div class="border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <div class="px-4 pt-4 sm:px-6 sm:pt-6">
                    <x-user-projects::section-header :title="__('user-projects::ui.team_members')" color="indigo" />
                </div>
                <div class="p-4 sm:p-6 pt-0">
                    @if ($members->isNotEmpty())
                        <div class="-mx-4 -mb-4 overflow-hidden sm:-mx-6 sm:-mb-6">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-800">
                                    <tr>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 sm:px-6">{{ __('user-projects::ui.user') }}</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 sm:px-6">{{ __('user-projects::ui.role') }}</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 sm:px-6">{{ __('user-projects::ui.joined') }}</th>
                                        @if ($currentUserRole !== null)
                                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 sm:px-6">{{ __('user-projects::ui.actions') }}</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-900">
                                    @foreach ($members as $member)
                                        @php
                                            $canManage = $currentUserRole !== null
                                                && (int) $member->user_id !== (int) auth(config('user-projects.panel.guard', 'web'))->id()
                                                && app(\YezzMedia\UserProjects\Support\ProjectMemberManager::class)->canManageMember($project, (int) auth(config('user-projects.panel.guard', 'web'))->id(), $member->user_id);
                                        @endphp
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                            <td class="whitespace-nowrap px-4 py-4 text-sm font-medium text-gray-900 dark:text-white sm:px-6">
                                                <div class="flex items-center gap-2">
                                                    <span>{{ $member->user?->name ?? $member->user?->email ?? 'Unknown' }}</span>
                                                    @if ((int) $member->user_id === (int) $project->owner_id)
                                                        <x-user-projects::badge class="bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300">{{ __('user-projects::ui.owner') }}</x-user-projects::badge>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-4 text-sm sm:px-6">
                                                @if ($canManage && $member->role !== 'owner')
                                                    <select wire:change="changeMemberRole({{ $member->id }}, $event.target.value)"
                                                        class="block w-full border border-gray-300 bg-white px-2 py-1 text-xs text-gray-900 focus:border-indigo-500 focus:outline-none dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:focus:border-indigo-400">
                                                        @foreach ($assignableRoles as $roleKey => $roleLabel)
                                                            <option value="{{ $roleKey }}" @if ($member->role === $roleKey) selected @endif>{{ $roleLabel }}</option>
                                                        @endforeach
                                                    </select>
                                                @else
                                                    <x-user-projects::badge :class="match ($member->role) {
                                                        'owner' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
                                                        'admin' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
                                                        default => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
                                                    }">
                                                        {{ ucfirst($member->role) }}
                                                    </x-user-projects::badge>
                                                @endif
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-500 dark:text-gray-400 sm:px-6">
                                                {{ $member->created_at->diffForHumans() }}
                                            </td>
                                            @if ($currentUserRole !== null)
                                                <td class="whitespace-nowrap px-4 py-4 text-right text-sm sm:px-6">
                                                    @if ($canManage)
                                                        @if ($currentUserRole === 'owner' && $member->role !== 'owner')
                                                            <button type="button" wire:click="transferOwnership({{ $member->id }})" wire:confirm="{{ __('user-projects::ui.transfer_ownership_confirm') }}"
                                                                class="text-xs font-medium text-amber-600 hover:text-amber-800 dark:text-amber-400 dark:hover:text-amber-300 mr-3">
                                                                {{ __('user-projects::ui.transfer_ownership') }}
                                                            </button>
                                                        @endif
                                                        <button type="button" wire:click="removeMember({{ $member->id }})" wire:confirm="{{ __('user-projects::ui.remove_member_confirm') }}"
                                                            class="text-xs font-medium text-rose-600 hover:text-rose-800 dark:text-rose-400 dark:hover:text-rose-300">
                                                            {{ __('user-projects::ui.remove') }}
                                                        </button>
                                                    @endif
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <x-user-projects::empty-state :title="__('user-projects::ui.no_members')" icon="users" />
                    @endif
                </div>
            </div>

            @if ($currentUserRole !== null && $currentUserRole !== 'owner')
                <div class="border border-rose-200 bg-white shadow-sm dark:border-rose-900 dark:bg-gray-900">
                    <div class="p-4 sm:p-6">
                        <p class="text-sm font-semibold text-rose-700 dark:text-rose-300">{{ __('user-projects::ui.leave_project_title') }}</p>
                        <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ __('user-projects::ui.leave_project_description') }}</p>
                        <button type="button" wire:click="leaveProject" wire:confirm="{{ __('user-projects::ui.leave_project_confirm') }}" wire:loading.attr="disabled"
                            class="mt-3 inline-flex items-center gap-1.5 border border-rose-300 bg-white px-3 py-1.5 text-xs font-medium text-rose-700 hover:bg-rose-50 disabled:opacity-50 dark:border-rose-700 dark:bg-gray-800 dark:text-rose-300 dark:hover:bg-rose-900/20">
                            <span wire:loading.remove wire:target="leaveProject"><x-user-projects::icon name="arrow-left-on-rectangle" class="h-3.5 w-3.5" /></span>
                            <span wire:loading wire:target="leaveProject"><x-user-projects::icon name="loader" class="h-3.5 w-3.5 animate-spin" /></span>
                            {{ __('user-projects::ui.leave_project') }}
                        </button>
                    </div>
                </div>
            @endif
        @endif

        @if ($activeTab === 'addons')
            <div class="border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <div class="px-4 pt-4 sm:px-6 sm:pt-6">
                    <x-user-projects::section-header :title="__('user-projects::ui.addons')" color="indigo" />
                </div>
                <div class="p-4 sm:p-6 pt-0">
                    @if (! empty($availableAddons))
                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach ($availableAddons as $addon)
                                @php
                                    $isActive = $addonActivations[$addon->key] ?? false;
                                @endphp
                                <div class="relative border p-4 @if ($isActive) border-indigo-300 bg-indigo-50/30 dark:border-indigo-700 dark:bg-indigo-950/20 @else border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800 @endif">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <x-user-projects::icon :name="$addon->icon" class="h-5 w-5 shrink-0 text-gray-400" />
                                            <div class="min-w-0">
                                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $addon->label }}</h3>
                                                <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ $addon->description }}</p>
                                            </div>
                                        </div>
                                        <button type="button" wire:click="toggleAddon('{{ $addon->key }}')"
                                            @class([
                                                'shrink-0 relative inline-flex h-6 w-11 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none',
                                                'bg-indigo-600' => $isActive,
                                                'bg-gray-200 dark:bg-gray-600' => ! $isActive,
                                            ])
                                            role="switch"
                                            aria-checked="{{ $isActive ? 'true' : 'false' }}"
                                        >
                                            <span
                                                @class([
                                                    'pointer-events-none relative inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                                                    'translate-x-5' => $isActive,
                                                    'translate-x-0' => ! $isActive,
                                                ])
                                            ></span>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <x-user-projects::empty-state :title="__('user-projects::ui.no_addons')" icon="puzzle-block" />
                    @endif
                </div>
            </div>
        @endif

        @if ($activeTab === 'activity')
            <div class="border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <div class="px-4 pt-4 sm:px-6 sm:pt-6">
                    <x-user-projects::section-header :title="__('user-projects::ui.activity')" color="indigo" />
                </div>
                <div class="p-4 sm:p-6 pt-0">
                    <div class="mb-4">
                        <select wire:model.live="activityFilter"
                            class="block w-full max-w-xs border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-indigo-500 focus:outline-none dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:focus:border-indigo-400">
                            @foreach ($activityTypes as $type => $label)
                                <option value="{{ $type }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if ($activities->isNotEmpty())
                        <div class="relative">
                            <div class="absolute left-4 top-2 bottom-2 w-px bg-gray-200 dark:bg-gray-700"></div>
                            <div class="space-y-4">
                                @foreach ($activities as $activity)
                                    @php
                                        $iconMap = [
                                            'project_created' => 'folder',
                                            'project_updated' => 'cog-6-tooth',
                                            'member_invited' => 'mail',
                                            'member_accepted' => 'check',
                                            'member_declined' => 'x',
                                            'member_removed' => 'trash',
                                            'role_changed' => 'arrows-up-down',
                                            'member_left' => 'arrow-left-on-rectangle',
                                            'member_invitation_cancelled' => 'x',
                                        ];
                                        $colorMap = [
                                            'project_created' => 'border-indigo-300 bg-indigo-100 text-indigo-600 dark:border-indigo-600 dark:bg-indigo-950/50 dark:text-indigo-400',
                                            'project_updated' => 'border-blue-300 bg-blue-100 text-blue-600 dark:border-blue-600 dark:bg-blue-950/50 dark:text-blue-400',
                                            'member_invited' => 'border-amber-300 bg-amber-100 text-amber-600 dark:border-amber-600 dark:bg-amber-950/50 dark:text-amber-400',
                                            'member_accepted' => 'border-emerald-300 bg-emerald-100 text-emerald-600 dark:border-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-400',
                                            'member_declined' => 'border-gray-300 bg-gray-100 text-gray-600 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400',
                                            'member_removed' => 'border-rose-300 bg-rose-100 text-rose-600 dark:border-rose-600 dark:bg-rose-950/50 dark:text-rose-400',
                                            'role_changed' => 'border-violet-300 bg-violet-100 text-violet-600 dark:border-violet-600 dark:bg-violet-950/50 dark:text-violet-400',
                                            'member_left' => 'border-orange-300 bg-orange-100 text-orange-600 dark:border-orange-600 dark:bg-orange-950/50 dark:text-orange-400',
                                            'member_invitation_cancelled' => 'border-gray-300 bg-gray-100 text-gray-600 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400',
                                        ];
                                        $activityIcon = $iconMap[$activity->type] ?? 'information-circle';
                                        $activityColor = $colorMap[$activity->type] ?? 'border-gray-300 bg-gray-100 text-gray-600 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400';
                                    @endphp
                                    <div class="relative flex items-start gap-4">
                                        <div class="relative z-10 flex h-8 w-8 shrink-0 items-center justify-center rounded-full border-2 {{ $activityColor }}">
                                            <x-user-projects::icon :name="$activityIcon" class="h-4 w-4" />
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1">
                                            <p class="text-sm text-gray-900 dark:text-white">
                                                @if ($activity->user)
                                                    <span class="font-medium">{{ $activity->user->name ?? $activity->user->email ?? __('user-projects::ui.unknown') }}</span>
                                                @endif
                                                {{ $activity->description }}
                                            </p>
                                            <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ $activity->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <x-user-projects::empty-state :title="__('user-projects::ui.no_activity')" icon="clock" />
                    @endif
                </div>
            </div>
        @endif

        @if ($activeTab === 'settings')
            <div class="grid gap-6 lg:grid-cols-2">
                <div class="space-y-6">
                    <div class="border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
                        <div class="px-4 pt-4 sm:px-6 sm:pt-6">
                            <x-user-projects::section-header :title="__('user-projects::ui.project_photo')" color="indigo" />
                        </div>
                        <div class="p-4 sm:p-6 pt-0">
                            <div class="flex items-center gap-6">
                                <div class="flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-full border-2 border-gray-200 bg-gray-100 dark:border-gray-600 dark:bg-gray-800">
                                    @if ($project->photo_url)
                                        <img src="{{ $project->photo_url }}" alt="{{ $project->name }}" class="h-full w-full object-cover">
                                    @else
                                        <span class="text-lg font-bold text-gray-400 dark:text-gray-500">{{ strtoupper(substr($project->name, 0, 2)) }}</span>
                                    @endif
                                </div>
                                <div class="flex-1 space-y-2">
                                    <div>
                                        <input type="file" wire:model="uploadPhoto" accept="image/*"
                                            class="block w-full text-sm text-gray-500 file:mr-3 file:border file:border-gray-300 file:bg-white file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-gray-700 hover:file:bg-gray-50 dark:text-gray-400 dark:file:border-gray-600 dark:file:bg-gray-800 dark:file:text-gray-300 dark:hover:file:bg-gray-700">
                                        @error('uploadPhoto')
                                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button type="button" wire:click="saveProjectPhoto" wire:loading.attr="disabled"
                                            class="inline-flex items-center gap-1 border border-indigo-300 bg-white px-3 py-1.5 text-xs font-medium text-indigo-700 hover:bg-indigo-50 disabled:opacity-50 dark:border-indigo-700 dark:bg-gray-800 dark:text-indigo-300 dark:hover:bg-indigo-900/20">
                                            <span wire:loading.remove wire:target="saveProjectPhoto"><x-user-projects::icon name="check" class="h-3 w-3" /></span>
                                            <span wire:loading wire:target="saveProjectPhoto"><x-user-projects::icon name="loader" class="h-3 w-3 animate-spin" /></span>
                                            {{ __('user-projects::ui.upload') }}
                                        </button>
                                        @if ($project->photo_path)
                                            <button type="button" wire:click="removeProjectPhoto" wire:confirm="{{ __('user-projects::ui.remove_photo_confirm') }}" wire:loading.attr="disabled"
                                                class="inline-flex items-center gap-1 border border-rose-300 bg-white px-3 py-1.5 text-xs font-medium text-rose-700 hover:bg-rose-50 disabled:opacity-50 dark:border-rose-700 dark:bg-gray-800 dark:text-rose-300 dark:hover:bg-rose-900/20">
                                                <span wire:loading.remove wire:target="removeProjectPhoto"><x-user-projects::icon name="trash" class="h-3 w-3" /></span>
                                                <span wire:loading wire:target="removeProjectPhoto"><x-user-projects::icon name="loader" class="h-3 w-3 animate-spin" /></span>
                                                {{ __('user-projects::ui.remove') }}
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
                        <div class="px-4 pt-4 sm:px-6 sm:pt-6">
                            <x-user-projects::section-header :title="__('user-projects::ui.details')" color="indigo" />
                        </div>
                        <form wire:submit="saveProjectSettings" class="p-4 sm:p-6 pt-0 space-y-4">
                            @if (session('success'))
                                <div class="rounded border border-green-200 bg-green-50 px-3 py-2 text-xs text-green-700 dark:border-green-700 dark:bg-green-900/20 dark:text-green-300">
                                    {{ session('success') }}
                                </div>
                            @endif
                            @if (session('error'))
                                <div class="rounded border border-red-200 bg-red-50 px-3 py-2 text-xs text-red-700 dark:border-red-700 dark:bg-red-900/20 dark:text-red-300">
                                    {{ session('error') }}
                                </div>
                            @endif

                            <div>
                                <label for="editName" class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('user-projects::ui.name_label') }}</label>
                                <input id="editName" type="text" wire:model="editName"
                                    class="mt-1 block w-full border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500 dark:focus:border-indigo-400"
                                    placeholder="{{ __('user-projects::ui.name_label') }}">
                                @error('editName')
                                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="editDescription" class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('user-projects::ui.description_label') }}</label>
                                <textarea id="editDescription" wire:model="editDescription" rows="3"
                                    class="mt-1 block w-full border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500 dark:focus:border-indigo-400"
                                    placeholder="{{ __('user-projects::ui.description_label') }}"></textarea>
                                @error('editDescription')
                                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="editStatus" class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('user-projects::ui.status_label') }}</label>
                                <select id="editStatus" wire:model="editStatus"
                                    class="mt-1 block w-full border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:focus:border-indigo-400">
                                    @foreach (config('user-projects.projects.statuses', ['active' => 'Active']) as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('editStatus')
                                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="pt-2">
                                <button type="submit" wire:loading.attr="disabled"
                                    class="inline-flex items-center gap-1.5 border border-indigo-300 bg-white px-3 py-2 text-xs font-medium text-indigo-700 hover:bg-indigo-50 disabled:opacity-50 dark:border-indigo-700 dark:bg-gray-800 dark:text-indigo-300 dark:hover:bg-indigo-900/20">
                                    <span wire:loading.remove wire:target="saveProjectSettings"><x-user-projects::icon name="check" class="h-3.5 w-3.5" /></span>
                                    <span wire:loading wire:target="saveProjectSettings"><x-user-projects::icon name="loader" class="h-3.5 w-3.5 animate-spin" /></span>
                                    {{ __('user-projects::ui.save_changes') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
                    <div class="px-4 pt-4 sm:px-6 sm:pt-6">
                        <x-user-projects::section-header :title="__('user-projects::ui.duplicate_project')" color="indigo" />
                    </div>
                    <div class="p-4 sm:p-6 pt-0">
                        <p class="text-sm text-gray-600 dark:text-gray-300">{{ __('user-projects::ui.duplicate_project_description') }}</p>
                        <button type="button" wire:click="duplicateProject" wire:loading.attr="disabled"
                            class="mt-4 inline-flex items-center gap-1.5 border border-indigo-300 bg-white px-3 py-2 text-xs font-medium text-indigo-700 hover:bg-indigo-50 disabled:opacity-50 dark:border-indigo-700 dark:bg-gray-800 dark:text-indigo-300 dark:hover:bg-indigo-900/20">
                            <span wire:loading.remove wire:target="duplicateProject"><x-user-projects::icon name="document-duplicate" class="h-3.5 w-3.5" /></span>
                            <span wire:loading wire:target="duplicateProject"><x-user-projects::icon name="loader" class="h-3.5 w-3.5 animate-spin" /></span>
                            {{ __('user-projects::ui.duplicate_project') }}
                        </button>
                    </div>
                </div>

                <div class="border border-rose-200 bg-white shadow-sm dark:border-rose-900 dark:bg-gray-900">
                    <div class="px-4 pt-4 sm:px-6 sm:pt-6">
                        <x-user-projects::section-header :title="__('user-projects::ui.danger_zone')" color="rose" />
                    </div>
                    <div class="p-4 sm:p-6 pt-0">
                        <p class="text-sm text-rose-600 dark:text-rose-300">{{ __('user-projects::ui.delete_project_description') }}</p>
                        <button type="button" wire:click="deleteProject('{{ $project->getRouteKey() }}')" wire:confirm="{{ __('user-projects::ui.delete_project_confirm') }}" wire:loading.attr="disabled"
                            class="mt-4 inline-flex items-center gap-1.5 border border-rose-300 bg-white px-3 py-2 text-xs font-medium text-rose-700 hover:bg-rose-50 disabled:opacity-50 dark:border-rose-700 dark:bg-gray-800 dark:text-rose-300 dark:hover:bg-rose-900/20">
                            <span wire:loading.remove wire:target="deleteProject"><x-user-projects::icon name="trash" class="h-3.5 w-3.5" /></span>
                            <span wire:loading wire:target="deleteProject"><x-user-projects::icon name="loader" class="h-3.5 w-3.5 animate-spin" /></span>
                            {{ __('user-projects::ui.delete_project') }}
                        </button>
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>
