<div class="space-y-6">
    <x-user-projects::page-header
        :title="__('user-projects::ui.stats_title')"
        :subtitle="__('user-projects::ui.stats_description')"
        color="indigo"
    >
        <x-slot:icon><x-user-projects::icon name="chart-bar" class="h-5 w-5" /></x-slot:icon>
    </x-user-projects::page-header>

    <div class="border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
        <div class="p-4 sm:p-6">
            <x-user-projects::section-header :title="__('user-projects::ui.overview')" color="indigo" />
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <x-user-projects::stat-card :label="__('user-projects::ui.total_projects')" :value="$pageData['total_projects']" />
                <x-user-projects::stat-card :label="__('user-projects::ui.active_projects')" :value="$pageData['active_projects']" color="text-emerald-600 dark:text-emerald-400" />
                <x-user-projects::stat-card :label="__('user-projects::ui.total_members')" :value="$pageData['total_members']" />
                <x-user-projects::stat-card :label="__('user-projects::ui.avg_members_per_project')" :value="$pageData['avg_members_per_project']" color="text-indigo-600 dark:text-indigo-400" />
            </div>
        </div>
    </div>

    <div class="border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
        <div class="p-4 sm:p-6">
            <x-user-projects::section-header :title="__('user-projects::ui.creation_activity')" color="purple" />
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <x-user-projects::stat-card :label="__('user-projects::ui.created_today')" :value="$pageData['created_today']" />
                <x-user-projects::stat-card :label="__('user-projects::ui.created_this_week')" :value="$pageData['created_this_week']" />
                <x-user-projects::stat-card :label="__('user-projects::ui.created_this_month')" :value="$pageData['created_this_month']" />
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <div class="p-4 sm:p-6">
                <x-user-projects::section-header :title="__('user-projects::ui.role_distribution')" color="blue" />
                @if (($pageData['role_distribution'] ?? collect())->isNotEmpty())
                    <div class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach (($pageData['role_distribution'] ?? collect()) as $role => $count)
                            <div class="flex items-center justify-between py-2">
                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ ucfirst($role) }}</span>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $count }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <x-user-projects::empty-state :title="__('user-projects::ui.no_data')" icon="users" />
                @endif
            </div>
        </div>

        <div class="border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <div class="p-4 sm:p-6">
                <x-user-projects::section-header :title="__('user-projects::ui.status_distribution')" color="emerald" />
                @if (($pageData['status_distribution'] ?? collect())->isNotEmpty())
                    <div class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach (($pageData['status_distribution'] ?? collect()) as $status => $count)
                            <div class="flex items-center justify-between py-2">
                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ ucfirst($status) }}</span>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $count }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <x-user-projects::empty-state :title="__('user-projects::ui.no_data')" icon="folder" />
                @endif
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <div class="p-4 sm:p-6">
                <x-user-projects::section-header :title="__('user-projects::ui.monthly_creation')" color="purple" />
                @if (($pageData['monthly_creation'] ?? collect())->isNotEmpty())
                    <div class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach (($pageData['monthly_creation'] ?? collect()) as $month => $count)
                            <div class="flex items-center justify-between py-2">
                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ $month }}</span>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $count }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <x-user-projects::empty-state :title="__('user-projects::ui.no_data')" icon="calendar" />
                @endif
            </div>
        </div>

        <div class="border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <div class="p-4 sm:p-6">
                <x-user-projects::section-header :title="__('user-projects::ui.top_by_members')" color="amber" />
                @if (($pageData['top_projects'] ?? collect())->isNotEmpty())
                    <div class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach (($pageData['top_projects'] ?? collect()) as $proj)
                            <div class="flex items-center justify-between py-2">
                                <span class="text-sm text-gray-600 dark:text-gray-400 truncate">{{ $proj->name }}</span>
                                <span class="ml-2 text-sm font-semibold text-gray-900 dark:text-white shrink-0">{{ trans_choice('user-projects::ui.member_count', $proj->members_count ?? 0, ['count' => $proj->members_count ?? 0]) }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <x-user-projects::empty-state :title="__('user-projects::ui.no_data')" icon="folder" />
                @endif
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <div class="p-4 sm:p-6">
                <x-user-projects::section-header :title="__('user-projects::ui.top_owners')" color="sky" />
                @if (($pageData['top_owners'] ?? collect())->isNotEmpty())
                    <div class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach (($pageData['top_owners'] ?? collect()) as $owner)
                            <div class="flex items-center justify-between py-2">
                                <span class="text-sm text-gray-600 dark:text-gray-400 truncate">{{ $owner['user'] }}</span>
                                <span class="ml-2 text-sm font-semibold text-gray-900 dark:text-white shrink-0">{{ $owner['count'] }} projects</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <x-user-projects::empty-state :title="__('user-projects::ui.no_data')" icon="users" />
                @endif
            </div>
        </div>

        <div class="border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <div class="p-4 sm:p-6">
                <x-user-projects::section-header :title="__('user-projects::ui.latest')" color="indigo" />
                @if (($pageData['latest_projects'] ?? collect())->isNotEmpty())
                    <div class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach (($pageData['latest_projects'] ?? collect()) as $proj)
                            <div class="flex items-center justify-between py-2">
                                <div class="min-w-0">
                                    <span class="block text-sm font-medium text-gray-900 dark:text-white truncate">{{ $proj->name }}</span>
                                    <span class="text-xs text-gray-400 dark:text-gray-500">{{ $proj->owner?->name ?? 'Unknown' }} · {{ $proj->created_at->diffForHumans() }}</span>
                                </div>
                                <span class="ml-2 text-xs text-gray-400 dark:text-gray-500 shrink-0">{{ trans_choice('user-projects::ui.member_count', $proj->members_count ?? 0, ['count' => $proj->members_count ?? 0]) }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <x-user-projects::empty-state :title="__('user-projects::ui.no_data')" icon="folder" />
                @endif
            </div>
        </div>
    </div>
</div>
