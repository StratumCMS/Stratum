@props([
    'label' => '',
    'name',
    'type' => 'text',
    'value' => '',
    'required' => false,
])

<div class="space-y-1">
    @if ($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700">
            {{ $label }}
            @if($required)<span class="text-red-500">*</span>@endif
        </label>
    @endif

    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ old($name, $value) }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge(['class' => 'w-full rounded-md border border-gray-300 p-2.5 text-sm shadow-sm focus:ring focus:ring-blue-200']) }}
    >

    @error($name)
    <div class="text-sm text-red-500 mt-1">{{ $message }}</div>
    @enderror
</div>
