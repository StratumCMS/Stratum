@props([
    'name',
    'label',
    'value' => '',
    'description' => null,
])

<div class="space-y-1">
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
        {{ $label }}
    </label>

    <select name="{{ $name }}" id="{{ $name }}"
        {{ $attributes->merge(['class' => 'mt-1 block w-full border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm']) }}>
        {{ $slot }}
    </select>

    @if($description)
        <p class="text-xs text-muted-foreground mt-1">{{ $description }}</p>
    @endif
</div>
