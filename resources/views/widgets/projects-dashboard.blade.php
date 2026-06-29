<div class="rounded-xl border border-gray-200/80 bg-white/50 p-4 shadow-sm dark:border-gray-800/80 dark:bg-gray-900/50">
    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('user-projects::ui.your_projects') }}</h3>
    @if (empty($projects))
        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ __('user-projects::ui.no_projects') }}</p>
    @else
        <div class="mt-3 space-y-2">
            @foreach ($projects as $proj)
                <a href="{{ url('/hub/projects?project='.$proj['id']) }}"
                    class="flex items-center gap-3 rounded-lg border border-gray-200/60 bg-white px-3 py-2 text-sm transition-colors hover:border-indigo-200 hover:bg-indigo-50/30 dark:border-gray-700/60 dark:bg-gray-800 dark:hover:border-indigo-700 dark:hover:bg-indigo-950/20">
                    @if ($proj['photo_url'])
                        <img src="{{ $proj['photo_url'] }}" alt="" class="h-8 w-8 shrink-0 rounded-full object-cover">
                    @else
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-xs font-bold text-indigo-600 dark:bg-indigo-950/50 dark:text-indigo-400">{{ strtoupper(substr($proj['name'], 0, 2)) }}</span>
                    @endif
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-medium text-gray-900 dark:text-white">{{ $proj['name'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('user-projects::ui.role') }}: {{ __('user-projects::ui.role_' . ($proj['owner_id'] === auth()->id() ? 'owner' : 'member')) }}</p>
                    </div>
                </a>
            @endforeach
        </div>
        <a href="{{ url('/hub/projects') }}"
            class="mt-3 block text-center text-xs font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300">
            {{ __('user-projects::ui.all_projects') }}
        </a>
    @endif
</div>
