@props([
    'name',
    'label',
    'value' => '',
])

<div>
    <label for="{{ $name }}" class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">
        {{ $label }}
    </label>
    <textarea
        name="{{ $name }}"
        id="{{ $name }}"
        rows="3"
        class="w-full px-4 py-2 rounded border border-border bg-transparent text-gray-900 dark:text-white focus:ring-primary focus:outline-none resize-none">{{ old($name, $value) }}</textarea>
</div>
