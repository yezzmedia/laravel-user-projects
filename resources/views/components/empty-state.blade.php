@props(['title' => null, 'description' => null, 'icon' => 'folder'])

<div class="py-8 text-center">
    <x-user-projects::icon :name="$icon" class="mx-auto h-10 w-10 text-gray-300 dark:text-gray-600" />
    @if (isset($title))
        <h3 class="mt-3 text-sm font-medium text-gray-900 dark:text-white">{{ $title }}</h3>
    @endif
    @if (isset($description))
        <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">{{ $description }}</p>
    @endif
    @isset($action)
        <div class="mt-4">{{ $action }}</div>
    @endisset
</div>
