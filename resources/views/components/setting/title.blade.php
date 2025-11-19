@props([
    'label',
    'icon' => null,
])

<div class="flex items-center space-x-3">
    @if($icon)
        <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center">
            <i class="{{ $icon }} text-primary text-sm"></i>
        </div>
    @endif
    <h2 class="text-lg sm:text-xl font-semibold text-foreground">{{ $label }}</h2>
</div>
