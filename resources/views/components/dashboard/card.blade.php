@props([
    'title' => '',
    'icon' => '',
    'value' => '',
])

<div class="bg-card border text-card-foreground rounded-2xl p-4 shadow-sm transition hover:shadow-md hover:-translate-y-1 hover:scale-[1.01] duration-150">
    <div class="flex items-center justify-between mb-2">
        <h4 class="text-sm font-medium text-muted-foreground">{{ $title }}</h4>
        <i class="{{ $icon }} text-muted-foreground text-sm"></i>
    </div>
    <div class="text-2xl font-bold text-primary">{{ $value }}</div>
</div>
