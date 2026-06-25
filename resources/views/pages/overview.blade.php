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

        <div class="border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <div class="p-4 sm:p-6">
                <x-user-projects::section-header :title="__('user-projects::ui.projects')" color="indigo" :meta="($pageData['projects'] ?? collect())->count().' total'" />

                @if (($pageData['projects'] ?? collect())->isNotEmpty())
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($pageData['projects'] as $proj)
                            <button type="button" wire:click="selectProject('{{ $proj->getRouteKey() }}')"
                                class="group w-full border border-gray-200 bg-white p-4 text-left transition-all hover:border-indigo-300 hover:bg-indigo-50/30 dark:border-gray-700 dark:bg-gray-800 dark:hover:border-indigo-600 dark:hover:bg-indigo-950/20">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0 flex-1">
                                        <h3 class="text-sm font-semibold text-gray-900 truncate group-hover:text-indigo-700 dark:text-white dark:group-hover:text-indigo-300">{{ $proj->name }}</h3>
                                        @if ($proj->description)
                                            <p class="mt-1 text-xs text-gray-500 line-clamp-2 dark:text-gray-400">{{ $proj->description }}</p>
                                        @endif
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
        ])
    @endif
</div>
