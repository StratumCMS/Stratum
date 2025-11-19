@props([
    'name',
    'label',
    'value' => '',
    'required' => false,
    'description' => null,
])

<div class="space-y-2">
    <label for="{{ $name }}" class="block text-sm font-medium text-foreground">
        {{ $label }}
        @if($required)
            <span class="text-destructive">*</span>
        @endif
    </label>

    <div class="relative">
        <select
            name="{{ $name }}"
            id="{{ $name }}"
            @if($required) required @endif
            {{ $attributes->merge([
                'class' => 'flex h-10 w-full rounded-lg border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 appearance-none transition-colors'
            ]) }}
        >
            {{ $slot }}
        </select>
        <i class="fas fa-chevron-down absolute right-3 top-1/2 transform -translate-y-1/2 text-muted-foreground pointer-events-none"></i>
    </div>

    @if($description)
        <p class="text-xs text-muted-foreground">{{ $description }}</p>
    @endif

    @error($name)
    <p class="text-sm text-destructive flex items-center space-x-1">
        <i class="fas fa-exclamation-circle w-4 h-4"></i>
        <span>{{ $message }}</span>
    </p>
    @enderror
</div>
