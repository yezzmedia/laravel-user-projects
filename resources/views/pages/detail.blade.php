@php
    /** @var \YezzMedia\UserProjects\Models\Project|null $project */
    /** @var \Illuminate\Support\Collection $members */
    /** @var string $activeTab */
@endphp

<div class="space-y-6">
    @if ($project === null)
        <x-user-projects::page-header :title="__('user-projects::ui.project_not_found')" color="rose">
            <x-slot:icon><x-user-projects::icon name="exclamation-triangle" class="h-5 w-5" /></x-slot:icon>
        </x-user-projects::page-header>
    @else
        <x-user-projects::hero
            :title="$project->name"
            :subtitle="$project->description ?? __('user-projects::ui.project_overview_description')"
            color="indigo"
        >
            <x-slot:avatar>
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-sm font-bold text-white shadow-md">
                    {{ strtoupper(substr($project->name, 0, 2)) }}
                </div>
            </x-slot:avatar>
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
        </x-user-projects::hero>

        <div class="flex gap-1 border border-gray-200 bg-white p-1 shadow-sm dark:border-gray-700 dark:bg-gray-900">
            @foreach (['overview' => __('user-projects::ui.overview'), 'members' => __('user-projects::ui.members'), 'settings' => __('user-projects::ui.settings')] as $tab => $label)
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

        @if ($activeTab === 'overview')
            <div class="grid gap-6 lg:grid-cols-2">
                <div class="border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
                    <div class="bg-gradient-to-r from-indigo-50 via-purple-50 to-fuchsia-50 px-6 py-4 border-b border-gray-200 dark:border-gray-700 dark:from-indigo-950/30 dark:via-purple-950/30 dark:to-fuchsia-950/30">
                        <h2 class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('user-projects::ui.details') }}</h2>
                    </div>
                    <div class="p-4 sm:p-6">
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
                    <div class="bg-gradient-to-r from-indigo-50 via-purple-50 to-fuchsia-50 px-6 py-4 border-b border-gray-200 dark:border-gray-700 dark:from-indigo-950/30 dark:via-purple-950/30 dark:to-fuchsia-950/30">
                        <h2 class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('user-projects::ui.info') }}</h2>
                    </div>
                    <div class="p-4 sm:p-6 space-y-4">
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
            <div class="border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <div class="bg-gradient-to-r from-indigo-50 via-purple-50 to-fuchsia-50 px-6 py-4 border-b border-gray-200 dark:border-gray-700 dark:from-indigo-950/30 dark:via-purple-950/30 dark:to-fuchsia-950/30">
                    <h2 class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('user-projects::ui.team_members') }}</h2>
                </div>
                <div class="p-4 sm:p-6">
                    @if ($members->isNotEmpty())
                        <div class="-mx-4 -mb-4 overflow-hidden sm:-mx-6 sm:-mb-6">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-800">
                                    <tr>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 sm:px-6">User</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 sm:px-6">Role</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 sm:px-6">Joined</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-900">
                                    @foreach ($members as $member)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                            <td class="whitespace-nowrap px-4 py-4 text-sm font-medium text-gray-900 dark:text-white sm:px-6">
                                                {{ $member->user?->name ?? $member->user?->email ?? 'Unknown' }}
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-4 text-sm sm:px-6">
                                                <x-user-projects::badge :class="match ($member->role) {
                                                    'owner' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
                                                    'admin' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
                                                    default => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
                                                }">
                                                    {{ ucfirst($member->role) }}
                                                </x-user-projects::badge>
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-500 dark:text-gray-400 sm:px-6">
                                                {{ $member->created_at->diffForHumans() }}
                                            </td>
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
        @endif

        @if ($activeTab === 'settings')
            <div class="grid gap-6 lg:grid-cols-2">
                <div class="border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
                    <div class="bg-gradient-to-r from-indigo-50 via-purple-50 to-fuchsia-50 px-6 py-4 border-b border-gray-200 dark:border-gray-700 dark:from-indigo-950/30 dark:via-purple-950/30 dark:to-fuchsia-950/30">
                        <h2 class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('user-projects::ui.details') }}</h2>
                    </div>
                    <div class="p-4 sm:p-6 space-y-4">
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Project ID</p>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 font-mono break-all">{{ $project->id }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</p>
                            <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ ucfirst($project->status) }}</p>
                        </div>
                    </div>
                </div>

                <div class="border border-rose-200 bg-white shadow-sm dark:border-rose-900 dark:bg-gray-900">
                    <div class="bg-gradient-to-r from-rose-50 via-red-50 to-pink-50 px-6 py-4 border-b border-rose-200 dark:border-rose-900 dark:from-rose-950/30 dark:via-red-950/30 dark:to-pink-950/30">
                        <h2 class="text-sm font-semibold text-rose-700 dark:text-rose-300">{{ __('user-projects::ui.danger_zone') }}</h2>
                    </div>
                    <div class="p-4 sm:p-6">
                        <p class="text-sm text-rose-600 dark:text-rose-300">Once you delete a project, there is no going back. Please be certain.</p>
                        <button type="button" wire:click="deleteProject('{{ $project->getRouteKey() }}')" wire:confirm="Are you sure you want to delete this project?"
                            class="mt-4 inline-flex items-center gap-1.5 border border-rose-300 bg-white px-3 py-2 text-xs font-medium text-rose-700 hover:bg-rose-50 dark:border-rose-700 dark:bg-gray-800 dark:text-rose-300 dark:hover:bg-rose-900/20">
                            <x-user-projects::icon name="trash" class="h-3.5 w-3.5" />
                            {{ __('user-projects::ui.delete_project') }}
                        </button>
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>
