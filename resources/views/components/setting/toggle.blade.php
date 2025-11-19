@props([
    'name',
    'label',
    'description' => null,
    'checked' => false,
    'required' => false,
])

<div class="flex items-center justify-between p-4 rounded-lg border border-border bg-background hover:bg-accent/5 transition-colors">
    <div class="flex-1 space-y-1">
        <div class="flex items-center space-x-2">
            <label for="{{ $name }}" class="text-sm font-medium text-foreground cursor-pointer">
                {{ $label }}
            </label>
            @if($required)
                <span class="text-destructive text-xs">*</span>
            @endif
        </div>
        @if($description)
            <p class="text-xs text-muted-foreground">{{ $description }}</p>
        @endif
    </div>

    <label class="relative inline-flex items-center cursor-pointer">
        <input
            type="checkbox"
            name="{{ $name }}"
            id="{{ $name }}"
            value="1"
            @if($checked) checked @endif
            @if($required) required @endif
            class="sr-only peer"
        >
        <div class="w-11 h-6 bg-muted-foreground/20 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-ring rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
    </label>
</div>

@error($name)
<p class="text-sm text-destructive flex items-center space-x-1 mt-2">
    <i class="fas fa-exclamation-circle w-4 h-4"></i>
    <span>{{ $message }}</span>
</p>
@enderror
