@props([
    'text',
    'required' => false,
])

<label {{ $attributes->merge(['class' => 'block text-sm font-medium text-foreground']) }}>
    {{ $text }}
    @if($required)
        <span class="text-destructive">*</span>
    @endif
</label>
