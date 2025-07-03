@props(['href', 'label'])

<a href="{{ $href }}"
    {{ $attributes->merge(['class' => 'block px-4 py-2 rounded-md hover:bg-primary transition']) }}>
    {{ $label }}
</a>
