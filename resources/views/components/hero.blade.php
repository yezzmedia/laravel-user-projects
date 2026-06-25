@props(['title' => null, 'subtitle' => null, 'color' => 'indigo'])

@php
    $hasMeta = isset($subtitle) || isset($badges);
    $gradientClass = match ($color) {
        'emerald' => 'from-emerald-50 via-teal-50 to-green-50 dark:from-emerald-950/30 dark:via-teal-950/30 dark:to-green-950/30',
        'amber' => 'from-amber-50 via-orange-50 to-yellow-50 dark:from-amber-950/30 dark:via-orange-950/30 dark:to-yellow-950/30',
        'rose' => 'from-rose-50 via-pink-50 to-red-50 dark:from-rose-950/30 dark:via-pink-950/30 dark:to-red-950/30',
        'blue' => 'from-blue-50 via-sky-50 to-indigo-50 dark:from-blue-950/30 dark:via-sky-950/30 dark:to-indigo-950/30',
        'purple' => 'from-purple-50 via-violet-50 to-fuchsia-50 dark:from-purple-950/30 dark:via-violet-950/30 dark:to-fuchsia-950/30',
        default => 'from-indigo-50 via-purple-50 to-fuchsia-50 dark:from-indigo-950/30 dark:via-purple-950/30 dark:to-fuchsia-950/30',
    };
    $borderClass = match ($color) {
        'emerald' => 'border-emerald-300 dark:border-emerald-700',
        'amber' => 'border-amber-300 dark:border-amber-700',
        'rose' => 'border-rose-300 dark:border-rose-700',
        'blue' => 'border-blue-300 dark:border-blue-700',
        'purple' => 'border-purple-300 dark:border-purple-700',
        default => 'border-indigo-300 dark:border-indigo-700',
    };
@endphp

<div {{ $attributes->merge(['class' => 'border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900']) }}>
    <div class="bg-gradient-to-r {{ $gradientClass }} px-6 py-8 border-b border-gray-200 dark:border-gray-700">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                @isset($avatar)
                    <div class="shrink-0">{{ $avatar }}</div>
                @endisset
                <div class="min-w-0 flex-1">
                    @if (isset($title))
                        <h1 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">{{ $title }}</h1>
                    @endif
                </div>
            </div>
            @isset($actions)
                <div class="flex shrink-0 flex-wrap items-center gap-2">{{ $actions }}</div>
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
                        <div class="flex flex-wrap items-center gap-2 shrink-0">{{ $badges }}</div>
                    @endisset
                </div>
            </div>
        @endif
    </div>
    {{ $slot ?? '' }}
</div>
