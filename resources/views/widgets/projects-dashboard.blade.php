<div class="border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
    <div class="p-4 sm:p-6">
        <div class="flex items-center gap-2 mb-3">
            <span class="w-1 h-4 bg-indigo-400"></span>
            <h2 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ __('user-projects::ui.your_projects') }}
            </h2>
        </div>
        @if (empty($projects))
            <x-account::empty-state
                title="{{ __('user-projects::ui.no_projects') }}"
                description="{{ __('user-projects::ui.no_projects_description') }}"
                icon="folder"
            />
        @else
            <div class="space-y-0.5">
                @foreach ($projects as $proj)
                    <a href="{{ url('/hub/projects?project='.$proj['id']) }}"
                        class="flex items-center gap-3 px-3 py-3 border-l-2 border-transparent hover:border-indigo-300 dark:hover:border-indigo-700 hover:bg-indigo-50/60 dark:hover:bg-indigo-900/10 transition">
                        @if ($proj['photo_url'])
                            <img src="{{ $proj['photo_url'] }}" alt="" class="h-8 w-8 shrink-0 rounded-full object-cover">
                        @else
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center bg-indigo-100 dark:bg-indigo-900/20 text-xs font-bold text-indigo-600 dark:text-indigo-400">
                                {{ strtoupper(substr($proj['name'], 0, 2)) }}
                            </span>
                        @endif
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium text-gray-900 dark:text-white">{{ $proj['name'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('user-projects::ui.role') }}: {{ __('user-projects::ui.role_' . ($proj['owner_id'] === auth()->id() ? 'owner' : 'member')) }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-800">
                <a href="{{ url('/hub/projects') }}"
                    class="inline-flex items-center gap-1 text-xs font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300">
                    {{ __('user-projects::ui.all_projects') }}
                    <x-account::icon name="arrow-right" class="h-3 w-3" />
                </a>
            </div>
        @endif
    </div>
</div>
