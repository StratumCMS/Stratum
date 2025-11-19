@props([
    'name',
    'label',
    'value' => '',
    'required' => false,
    'description' => null,
    'rows' => 4,
    'placeholder' => null,
])

<div class="space-y-2">
    <label for="{{ $name }}" class="block text-sm font-medium text-foreground">
        {{ $label }}
        @if($required)
            <span class="text-destructive">*</span>
        @endif
    </label>

    <textarea
        name="{{ $name }}"
        id="{{ $name }}"
        rows="{{ $rows }}"
        @if($placeholder) placeholder="{{ $placeholder }}" @endif
        @if($required) required @endif
        {{ $attributes->merge([
            'class' => 'flex min-h-[80px] w-full rounded-lg border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 resize-none transition-colors'
        ]) }}
    >{{ old($name, $value) }}</textarea>

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
