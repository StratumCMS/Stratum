@props([
    'text' => 'Titre par défaut',
    'text_color' => '#111111',
    'font_size' => '2rem',
    'text_align' => 'center',
    'padding' => '1rem',
    'margin' => '0 0 2rem',
    'animation' => 'none',
])

<h1 style="
    color: {{ $text_color }};
    font-size: {{ $font_size }};
    text-align: {{ $text_align }};
    padding: {{ $padding }};
    margin: {{ $margin }};
    animation: {{ $animation }};
">
    {{ $text }}
</h1>
