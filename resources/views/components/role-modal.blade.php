@props([
    'role' => null,
    'isEdit' => false,
    'xModel' => 'modalOpen'
])

@php
    $iconList = ['user', 'shield-alt', 'cog', 'edit', 'lock', 'crown', 'users', 'tools', 'chart-line', 'key', 'eye', 'wrench'];
    $roleName = old('name', $role->name ?? '');
    $roleDescription = old('description', $role->description ?? '');
    $roleColor = old('color', $role->color ?? '#3b82f6');
    $roleIcon = old('icon', $role->icon ?? 'user');
    $selectedPermissions = old('permissions', $role ? $role->permissions->pluck('id')->toArray() : []);
@endphp

<div x-show="{{ $xModel }} @if($isEdit) === {{ $role->id }} @endif"
     x-transition
     x-cloak
     @click.outside="{{ $xModel }} = false"
     @keydown.escape.window="{{ $xModel }} = false"
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 px-4">
    <div class="bg-card rounded-lg shadow-lg w-full max-w-2xl p-6 overflow-y-auto max-h-[90vh] space-y-6">
        <div class="flex justify-between items-start">
            <h2 class="text-2xl font-bold">
                {{ $isEdit ? "Modifier « {$role->name} »" : "Créer un nouveau rôle" }}
            </h2>
            <button class="text-gray-400 hover:text-gray-600" @click="{{ $xModel }} = false">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <form method="POST" action="{{ $isEdit ? route('admin.roles.update', $role) : route('admin.roles.store') }}"
              class="space-y-6">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <div>
                <label class="block text-sm font-medium mb-1 text-foreground">Nom</label>
                <input type="text" name="name" value="{{ $roleName }}"
                       class="w-full h-10 px-3 rounded-md border border-border bg-background text-foreground placeholder:text-muted-foreground shadow-sm focus:outline-none focus:ring-2 focus:ring-primary transition" required>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1 text-foreground">Description</label>
                <input type="text" name="description" value="{{ $roleDescription }}"
                       class="w-full h-10 px-3 rounded-md border border-border bg-background text-foreground placeholder:text-muted-foreground shadow-sm focus:outline-none focus:ring-2 focus:ring-primary transition">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1 text-foreground">Couleur</label>
                <input type="color" name="color" value="{{ $roleColor }}"
                       class="w-16 h-10 p-0 border-none cursor-pointer">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1 text-foreground">Icône</label>
                <select name="icon"
                        class="w-full h-10 px-3 rounded-md border border-border bg-background text-foreground shadow-sm focus:outline-none focus:ring-2 focus:ring-primary transition">
                    @foreach($iconList as $ico)
                        <option value="{{ $ico }}" @selected($ico === $roleIcon)">
                        &#xf007; {{ $ico }}
                        </option>
                    @endforeach
                </select>
                <div class="mt-2 text-sm text-muted-foreground">
                    Icône actuelle : <i class="fas fa-{{ $roleIcon }} text-primary"></i> <span class="ml-1">{{ $roleIcon }}</span>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-2 text-foreground">Permissions</label>
                <div class="grid grid-cols-2 gap-2">
                    @foreach(\App\Models\Permission::all() as $permission)
                        <label class="flex items-center space-x-2 text-sm text-foreground">
                            <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                   @checked(in_array($permission->id, $selectedPermissions))
                                   class="h-4 w-4 text-primary border-border rounded focus:ring-primary focus:ring-2 focus:ring-offset-0 transition">
                            <span>{{ $permission->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-4 border-t mt-6">
                <button type="button" class="btn btn-secondary" @click="{{ $xModel }} = false">Annuler</button>
                <button type="submit" class="btn btn-primary">
                    {{ $isEdit ? 'Enregistrer' : 'Créer' }}
                </button>
            </div>
        </form>
    </div>
</div>
