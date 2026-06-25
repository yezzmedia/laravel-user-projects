<div class="space-y-6">
    <x-user-projects::page-header
        :title="__('user-projects::ui.settings_title')"
        :subtitle="__('user-projects::ui.settings_description')"
        color="indigo"
    >
        <x-slot:icon><x-user-projects::icon name="cog-6-tooth" class="h-5 w-5" /></x-slot:icon>
    </x-user-projects::page-header>

    <div class="border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
        <div class="bg-gradient-to-r from-indigo-50 via-purple-50 to-fuchsia-50 px-6 py-4 border-b border-gray-200 dark:border-gray-700 dark:from-indigo-950/30 dark:via-purple-950/30 dark:to-fuchsia-950/30">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('user-projects::ui.display_settings') }}</h2>
        </div>
        <div class="p-4 sm:p-6">
            <form wire:submit="save" class="space-y-4">
                <div>
                    <label for="displayLimit" class="block text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Projects Per Page</label>
                    <input wire:model="displayLimit" id="displayLimit" type="number" min="5" max="100"
                        class="mt-1 block w-32 border border-gray-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Number of projects shown per page on the overview.</p>
                </div>
                <div class="flex justify-end pt-2">
                    <button type="submit"
                        class="inline-flex items-center gap-1.5 border border-indigo-300 bg-white px-3 py-2 text-xs font-medium text-indigo-700 hover:bg-indigo-50 dark:border-indigo-700 dark:bg-gray-800 dark:text-indigo-300 dark:hover:bg-indigo-900/20">
                        <x-user-projects::icon name="check" class="h-3.5 w-3.5" />
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
        <div class="bg-gradient-to-r from-indigo-50 via-purple-50 to-fuchsia-50 px-6 py-4 border-b border-gray-200 dark:border-gray-700 dark:from-indigo-950/30 dark:via-purple-950/30 dark:to-fuchsia-950/30">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('user-projects::ui.available_roles') }}</h2>
        </div>
        <div class="p-4 sm:p-6">
            <p class="mb-4 text-xs text-gray-400 dark:text-gray-500">These are the member roles configured for the platform. Use the Permissions page to manage role capabilities.</p>
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach (($pageData['availableRoles'] ?? []) as $roleKey => $roleLabel)
                    <div class="flex items-center justify-between py-3 first:pt-0 last:pb-0">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $roleLabel }}</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 font-mono">{{ $roleKey }}</p>
                        </div>
                        @if ($roleKey === ($pageData['defaultRole'] ?? 'member'))
                            <x-user-projects::badge class="bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300">Default</x-user-projects::badge>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
