@php
    $colors = [
        'from-red-500 to-red-600' => 'Rouge',
        'from-blue-500 to-blue-600' => 'Bleu',
        'from-green-500 to-green-600' => 'Vert',
        'from-purple-500 to-purple-600' => 'Violet',
        'from-orange-500 to-orange-600' => 'Orange',
        'from-pink-500 to-pink-600' => 'Rose',
        'from-indigo-500 to-indigo-600' => 'Indigo',
        'from-teal-500 to-teal-600' => 'Teal',
    ];

    $icons = [
        'crown', 'edit', 'user', 'shield-alt', 'cogs',
        'file-alt', 'users', 'database', 'key', 'eye',
        'lock', 'user-check', 'bolt',
    ];
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <x-form.input name="name" label="Nom" :value="$role->name ?? ''" required />
    <x-form.input name="description" label="Description" :value="$role->description ?? ''" />

    <x-form.select name="color" label="Couleur">
        @foreach($colors as $val => $label)
            <option value="{{ $val }}" @selected(old('color', $role->color ?? '') == $val)>
                {{ $label }}
            </option>
        @endforeach
    </x-form.select>

    <x-form.select name="icon" label="Icône (FontAwesome)">
        @foreach($icons as $icon)
            <option value="{{ $icon }}" @selected(old('icon', $role->icon ?? '') == $icon)>
                {{ ucfirst($icon) }}
            </option>
        @endforeach
    </x-form.select>
</div>

<hr class="my-4">

<h4 class="font-semibold mb-2">Permissions</h4>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    @foreach($permissions->groupBy('category') as $category => $group)
        <div>
            <div class="font-medium text-muted-foreground mb-2 uppercase text-sm">
                {{ ucfirst($category) }}
            </div>
            @foreach($group as $permission)
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                        @checked(optional($role)->permissions->pluck('id')->contains($permission->id))>
                    {{ $permission->name }}
                </label>
            @endforeach
        </div>
    @endforeach
</div>
