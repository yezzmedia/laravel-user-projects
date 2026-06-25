<div {{ $attributes->merge(['class' => 'border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900']) }}>
    @if (isset($label))
        <div class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ $label }}</div>
    @endif
    @if (isset($value))
        <div class="{{ $color ?? 'text-gray-900 dark:text-white' }} mt-1 text-2xl font-semibold">{{ $value }}</div>
    @endif
    @if (isset($change))
        <div class="mt-1 text-xs text-gray-400 dark:text-gray-500">{{ $change }}</div>
    @endif
</div>
