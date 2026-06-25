@props(['title' => null, 'color' => 'indigo'])

@php
    $barClass = match ($color) {
        'emerald' => 'bg-emerald-400',
        'amber' => 'bg-amber-400',
        'rose' => 'bg-rose-400',
        'blue' => 'bg-blue-400',
        'purple' => 'bg-purple-400',
        'sky' => 'bg-sky-400',
        default => 'bg-indigo-400',
    };
@endphp

<div class="flex items-center gap-2 mb-4">
    <span class="w-1 h-4 {{ $barClass }}"></span>
    @if (isset($title))
        <h2 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
            {{ $title }}
        </h2>
    @endif
    @isset($meta)
        <span class="text-xs text-gray-400">{{ $meta }}</span>
    @endisset
</div>
