@props([
    'label',
    'icon' => null,
])

<h2 class="text-lg font-semibold mb-4 flex items-center space-x-2">
    @if($icon)
        <i class="{{ $icon }}"></i>
    @endif
    <span>{{ $label }}</span>
</h2>
