<div class="space-y-6">
    @if ($this->project === null)
        <x-user-projects::page-header
            :title="__('user-projects::ui.projects_title')"
            :subtitle="__('user-projects::ui.projects_description')"
            color="indigo"
        >
            <x-slot:icon><x-user-projects::icon name="folder-open" class="h-5 w-5" /></x-slot:icon>
            <x-slot:actions>
                <a href="{{ \YezzMedia\UserProjects\Pages\ProjectsCreatePage::getUrl() }}" class="inline-flex items-center gap-1.5 border border-indigo-300 bg-white px-3 py-1.5 text-xs font-medium text-indigo-700 hover:bg-indigo-50 dark:border-indigo-700 dark:bg-gray-800 dark:text-indigo-300 dark:hover:bg-indigo-900/20">
                    <x-user-projects::icon name="plus" class="h-3.5 w-3.5" />
                    {{ __('user-projects::ui.create_project') }}
                </a>
            </x-slot:actions>
        </x-user-projects::page-header>

        @if (($pageData['userPendingInvitations'] ?? collect())->isNotEmpty())
            <div class="border border-amber-200 bg-amber-50 shadow-sm dark:border-amber-700 dark:bg-amber-900/20">
                <div class="p-4 sm:p-6">
                    <x-user-projects::section-header :title="__('user-projects::ui.pending_invitations')" color="amber" :meta="($pageData['userPendingInvitations'] ?? collect())->count().' '.__('user-projects::ui.total')" />
                    <div class="mt-2 space-y-2">
                        @foreach ($pageData['userPendingInvitations'] as $invitation)
                            <div class="flex items-center justify-between border border-amber-200 bg-white px-4 py-3 dark:border-amber-700 dark:bg-gray-800">
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $invitation->project->name ?? __('user-projects::ui.unknown_project') }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('user-projects::ui.invited_by') }}: {{ $invitation->inviter->name ?? '—' }}</p>
                                </div>
                                <div class="flex shrink-0 items-center gap-2">
                                    <span class="inline-flex items-center rounded bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-800 dark:bg-amber-800 dark:text-amber-200">
                                        {{ __('user-projects::ui.role') }}: {{ __("user-projects::ui.role_{$invitation->role}") }}
                                    </span>
                                    <button type="button" wire:click="acceptInvitationFor({{ $invitation->project_id }})"
                                        class="inline-flex items-center gap-1 rounded bg-green-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-green-700">
                                        {{ __('user-projects::ui.accept') }}
                                    </button>
                                    <button type="button" wire:click="declineInvitationFor({{ $invitation->project_id }})"
                                        wire:confirm="{{ __('user-projects::ui.decline_invitation_confirm') }}"
                                        class="inline-flex items-center gap-1 rounded border border-gray-300 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                                        {{ __('user-projects::ui.decline') }}
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <div class="border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <div class="p-4 sm:p-6">
                <x-user-projects::section-header :title="__('user-projects::ui.projects')" color="indigo" :meta="($pageData['projects'] ?? collect())->count().' '.__('user-projects::ui.total')" />

                <div class="mb-4 flex items-center gap-3">
                    <div class="relative flex-1">
                        <x-user-projects::icon name="magnifying-glass" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ __('user-projects::ui.search_projects') }}"
                            class="block w-full border border-gray-300 bg-white py-2 pl-10 pr-3 text-sm text-gray-900 placeholder-gray-400 focus:border-indigo-500 focus:outline-none dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500 dark:focus:border-indigo-400">
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" wire:click="toggleArchived"
                            @class(['inline-flex items-center gap-1 border px-2.5 py-2 text-xs font-medium transition-colors', 'border-amber-300 bg-amber-50 text-amber-700 dark:border-amber-700 dark:bg-amber-950/50 dark:text-amber-300' => $pageData['showArchived'], 'border-gray-300 bg-white text-gray-600 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700' => ! $pageData['showArchived']])>
                            <x-user-projects::icon name="archive-box" class="h-3.5 w-3.5" />
                            {{ $pageData['showArchived'] ? __('user-projects::ui.hide_archived') : __('user-projects::ui.show_archived') }}
                        </button>
                        <button type="button" wire:click="toggleSort('name')"
                            @class(['inline-flex items-center gap-1 border px-2.5 py-2 text-xs font-medium transition-colors', 'border-indigo-300 bg-indigo-50 text-indigo-700 dark:border-indigo-700 dark:bg-indigo-950/50 dark:text-indigo-300' => $pageData['sortField'] === 'name', 'border-gray-300 bg-white text-gray-600 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700' => $pageData['sortField'] !== 'name'])>
                            {{ __('user-projects::ui.name') }}
                            @if ($pageData['sortField'] === 'name')
                                <x-user-projects::icon :name="$pageData['sortDirection'] === 'asc' ? 'arrow-up' : 'arrow-down'" class="h-3 w-3" />
                            @endif
                        </button>
                        <button type="button" wire:click="toggleSort('created_at')"
                            @class(['inline-flex items-center gap-1 border px-2.5 py-2 text-xs font-medium transition-colors', 'border-indigo-300 bg-indigo-50 text-indigo-700 dark:border-indigo-700 dark:bg-indigo-950/50 dark:text-indigo-300' => $pageData['sortField'] === 'created_at', 'border-gray-300 bg-white text-gray-600 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700' => $pageData['sortField'] !== 'created_at'])>
                            {{ __('user-projects::ui.date') }}
                            @if ($pageData['sortField'] === 'created_at')
                                <x-user-projects::icon :name="$pageData['sortDirection'] === 'asc' ? 'arrow-up' : 'arrow-down'" class="h-3 w-3" />
                            @endif
                        </button>
                        <button type="button" wire:click="toggleSort('members_count')"
                            @class(['inline-flex items-center gap-1 border px-2.5 py-2 text-xs font-medium transition-colors', 'border-indigo-300 bg-indigo-50 text-indigo-700 dark:border-indigo-700 dark:bg-indigo-950/50 dark:text-indigo-300' => $pageData['sortField'] === 'members_count', 'border-gray-300 bg-white text-gray-600 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700' => $pageData['sortField'] !== 'members_count'])>
                            {{ __('user-projects::ui.members') }}
                            @if ($pageData['sortField'] === 'members_count')
                                <x-user-projects::icon :name="$pageData['sortDirection'] === 'asc' ? 'arrow-up' : 'arrow-down'" class="h-3 w-3" />
                            @endif
                        </button>
                    </div>
                </div>

                @if (($pageData['projects'] ?? collect())->isNotEmpty())
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($pageData['projects'] as $proj)
                            <button type="button" wire:click="selectProject('{{ $proj->getRouteKey() }}')"
                                class="group w-full border border-gray-200 bg-white p-4 text-left transition-all hover:border-indigo-300 hover:bg-indigo-50/30 dark:border-gray-700 dark:bg-gray-800 dark:hover:border-indigo-600 dark:hover:bg-indigo-950/20">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex min-w-0 flex-1 items-center gap-3">
                                        @if ($proj->photo_url)
                                            <img src="{{ $proj->photo_url }}" alt="{{ $proj->name }}" class="h-10 w-10 shrink-0 rounded-full object-cover">
                                        @else
                                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-xs font-bold text-indigo-600 dark:bg-indigo-950/50 dark:text-indigo-400">{{ strtoupper(substr($proj->name, 0, 2)) }}</span>
                                        @endif
                                        <div class="min-w-0">
                                            <h3 class="text-sm font-semibold text-gray-900 truncate group-hover:text-indigo-700 dark:text-white dark:group-hover:text-indigo-300">{{ $proj->name }}</h3>
                                            @if ($proj->description)
                                                <p class="mt-0.5 text-xs text-gray-500 line-clamp-1 dark:text-gray-400">{{ $proj->description }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <x-user-projects::icon name="arrow-right" class="h-4 w-4 shrink-0 text-gray-300 group-hover:text-indigo-500 dark:text-gray-600" />
                                </div>
                                <div class="mt-4 flex items-center gap-4 text-xs text-gray-400 dark:text-gray-500">
                                    <span class="inline-flex items-center gap-1">
                                        <x-user-projects::icon name="users" class="h-3.5 w-3.5" />
                                        {{ trans_choice('user-projects::ui.member_count', $proj->members_count ?? 0, ['count' => $proj->members_count ?? 0]) }}
                                    </span>
                                    <span class="inline-flex items-center gap-1">
                                        <x-user-projects::icon name="clock" class="h-3.5 w-3.5" />
                                        {{ $proj->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </button>
                        @endforeach
                    </div>
                    @if ($pageData['hasMoreProjects'])
                        <div class="mt-4 text-center">
                            <button type="button" wire:click="loadMoreProjects" wire:loading.attr="disabled"
                                class="inline-flex items-center gap-1.5 border border-gray-300 bg-white px-4 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                                <span wire:loading.remove wire:target="loadMoreProjects"><x-user-projects::icon name="chevron-down" class="h-3.5 w-3.5" /></span>
                                <span wire:loading wire:target="loadMoreProjects"><x-user-projects::icon name="loader" class="h-3.5 w-3.5 animate-spin" /></span>
                                {{ __('user-projects::ui.load_more') }}
                            </button>
                        </div>
                    @endif
                @else
                    <x-user-projects::empty-state
                        :title="__('user-projects::ui.no_projects')"
                        :description="__('user-projects::ui.no_projects_description')"
                        icon="folder"
                    >
                        <x-slot:action>
                            <a href="{{ \YezzMedia\UserProjects\Pages\ProjectsCreatePage::getUrl() }}" class="inline-flex items-center gap-1.5 border border-indigo-300 bg-white px-3 py-1.5 text-xs font-medium text-indigo-700 hover:bg-indigo-50 dark:border-indigo-700 dark:bg-gray-800 dark:text-indigo-300 dark:hover:bg-indigo-900/20">
                                <x-user-projects::icon name="plus" class="h-3.5 w-3.5" />
                                {{ __('user-projects::ui.create_project') }}
                            </a>
                        </x-slot:action>
                    </x-user-projects::empty-state>
                @endif
            </div>
        </div>
    @else
        @include('user-projects::pages.detail', [
            'project' => $pageData['selectedProject'],
            'members' => $pageData['members'] ?? collect(),
            'activeTab' => $pageData['activeTab'] ?? 'overview',
            'availableAddons' => $pageData['availableAddons'] ?? [],
            'addonActivations' => $pageData['addonActivations'] ?? [],
            'currentUserRole' => $pageData['currentUserRole'] ?? null,
            'assignableRoles' => $pageData['assignableRoles'] ?? [],
            'pendingInvitations' => $pageData['pendingInvitations'] ?? collect(),
            'currentUserInvitation' => $pageData['currentUserInvitation'] ?? null,
            'activities' => $pageData['activities'] ?? collect(),
            'activityTypes' => $pageData['activityTypes'] ?? [],
        ])
    @endif
</div>
