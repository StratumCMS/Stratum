@props([
    'name',
    'label',
    'description' => null,
    'checked' => false,
])

<div class="flex items-center justify-between p-4 border border-border rounded-lg">
    <div>
        <div class="font-medium text-gray-800 dark:text-gray-200">{{ $label }}</div>
        @if($description)
            <div class="text-sm text-muted-foreground">{{ $description }}</div>
        @endif
    </div>
    <label class="inline-flex items-center cursor-pointer">
        <input type="checkbox"
               name="{{ $name }}"
               value="1"
               class="sr-only peer"
            {{ $checked ? 'checked' : '' }}>
        <div class="w-11 h-6 bg-input rounded-full peer peer-checked:bg-primary relative transition">
            <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full shadow transition peer-checked:translate-x-5"></div>
        </div>
    </label>
</div>
