<div class="space-y-6">
    <x-user-projects::page-header
        :title="__('user-projects::ui.create_project_title')"
        :subtitle="__('user-projects::ui.create_project_description')"
        color="indigo"
    >
        <x-slot:icon><x-user-projects::icon name="plus" class="h-5 w-5" /></x-slot:icon>
        <x-slot:actions>
            <a href="{{ \YezzMedia\UserProjects\Pages\ProjectsOverviewPage::getUrl() }}" class="inline-flex items-center gap-1.5 border border-gray-300 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                <x-user-projects::icon name="arrow-left" class="h-3.5 w-3.5" />
                {{ __('user-projects::ui.back_to_hub') }}
            </a>
        </x-slot:actions>
    </x-user-projects::page-header>

    <div class="border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
        <div class="bg-gradient-to-r from-indigo-50 via-purple-50 to-fuchsia-50 px-6 py-4 border-b border-gray-200 dark:border-gray-700 dark:from-indigo-950/30 dark:via-purple-950/30 dark:to-fuchsia-950/30">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('user-projects::ui.create_project') }}</h2>
        </div>
        <div class="p-4 sm:p-6">
            <form wire:submit="create" class="space-y-4">
                <div>
                    <label for="name" class="block text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Project Name</label>
                    <input wire:model="name" id="name" type="text" required
                        class="mt-1 block w-full border border-gray-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="description" class="block text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Description (optional)</label>
                    <textarea wire:model="description" id="description" rows="4"
                        class="mt-1 block w-full border border-gray-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"></textarea>
                    @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit"
                        class="inline-flex items-center gap-1.5 border border-indigo-300 bg-white px-3 py-2 text-xs font-medium text-indigo-700 hover:bg-indigo-50 dark:border-indigo-700 dark:bg-gray-800 dark:text-indigo-300 dark:hover:bg-indigo-900/20">
                        <x-user-projects::icon name="plus" class="h-3.5 w-3.5" />
                        {{ __('user-projects::ui.create_project') }}
                    </button>
                    <a href="{{ \YezzMedia\UserProjects\Pages\ProjectsOverviewPage::getUrl() }}"
                        class="inline-flex items-center gap-1.5 border border-gray-300 bg-white px-3 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
