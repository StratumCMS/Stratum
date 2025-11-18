@props([
    'title' => '',
    'icon' => '',
    'value' => '',
    'color' => 'primary'
])

@php
    $colorClasses = [
        'primary' => 'bg-blue-500/10 text-blue-500',
        'success' => 'bg-green-500/10 text-green-500',
        'purple-600' => 'bg-purple-500/10 text-purple-500'
    ][$color] ?? 'bg-gray-500/10 text-gray-500';
@endphp

<div class="rounded-xl border bg-card text-card-foreground shadow-sm p-4 sm:p-6 transition-all duration-200 hover:shadow-md">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-xs sm:text-sm font-medium text-muted-foreground mb-1">{{ $title }}</p>
            <h3 class="text-xl sm:text-2xl font-bold text-foreground">{{ $value }}</h3>
        </div>
        <div class="w-10 h-10 rounded-full {{ $colorClasses }} flex items-center justify-center">
            <i class="{{ $icon }} text-sm"></i>
        </div>
    </div>
</div>
