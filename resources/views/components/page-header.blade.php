@props(['title' => null, 'subtitle' => null, 'color' => 'indigo'])

@php
    $iconClass = match ($color) {
        'emerald' => 'text-emerald-500 bg-emerald-50 dark:bg-emerald-900/20',
        'amber' => 'text-amber-500 bg-amber-50 dark:bg-amber-900/20',
        'rose' => 'text-rose-500 bg-rose-50 dark:bg-rose-900/20',
        'blue' => 'text-blue-500 bg-blue-50 dark:bg-blue-900/20',
        'purple' => 'text-purple-500 bg-purple-50 dark:bg-purple-900/20',
        'sky' => 'text-sky-500 bg-sky-50 dark:bg-sky-900/20',
        default => 'text-indigo-500 bg-indigo-50 dark:bg-indigo-900/20',
    };

    $borderClass = match ($color) {
        'emerald' => 'border-emerald-300 dark:border-emerald-700',
        'amber' => 'border-amber-300 dark:border-amber-700',
        'rose' => 'border-rose-300 dark:border-rose-700',
        'blue' => 'border-blue-300 dark:border-blue-700',
        'purple' => 'border-purple-300 dark:border-purple-700',
        'sky' => 'border-sky-300 dark:border-sky-700',
        default => 'border-indigo-300 dark:border-indigo-700',
    };

    $hasMeta = isset($subtitle) || isset($meta) || isset($badges);
@endphp

<div {{ $attributes->merge(['class' => 'border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900']) }}>
    <div class="p-4 sm:p-6 border-b border-gray-200 dark:border-gray-700">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div class="flex min-w-0 flex-1 items-start gap-3">
                @isset($icon)
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center {{ $iconClass }}">
                        {{ $icon }}
                    </div>
                @endisset
                <div class="min-w-0 flex-1">
                    @if (isset($title))
                        <h1 class="truncate text-xl font-semibold text-gray-900 dark:text-white">{{ $title }}</h1>
                    @endif
                </div>
            </div>
            @isset($actions)
                <div class="flex shrink-0 flex-wrap items-center gap-2">
                    {{ $actions }}
                </div>
            @endisset
        </div>
        @if ($hasMeta)
            <div class="mt-4 border-t border-gray-100 pt-4 dark:border-gray-800">
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                    <div class="flex-1 min-w-0">
                        @if (isset($subtitle))
                            <p class="text-xs text-gray-400 dark:text-gray-500 leading-relaxed border-l-2 {{ $borderClass }} pl-3">
                                {{ $subtitle }}
                            </p>
                        @endif
                        @isset($meta)
                            {{ $meta }}
                        @endisset
                    </div>
                    @isset($badges)
                        <div class="flex flex-wrap items-center gap-2 shrink-0">
                            {{ $badges }}
                        </div>
                    @endisset
                </div>
            </div>
        @endif
    </div>
</div>
