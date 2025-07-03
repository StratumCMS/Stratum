@props([
    'name',
    'label',
    'value' => '',
])

<div>
    <label for="{{ $name }}" class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">
        {{ $label }}
    </label>
    <input type="text"
           name="{{ $name }}"
           id="{{ $name }}"
           value="{{ old($name, $value) }}"
           class="w-full px-4 py-2 rounded border border-border bg-transparent text-gray-900 dark:text-white focus:ring-primary focus:outline-none">
</div>
