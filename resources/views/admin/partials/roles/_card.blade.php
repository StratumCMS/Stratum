@php
    $color = $role->color ?? 'from-blue-500 to-blue-600';
    $icon = $role->icon ?? 'user';
@endphp

<div class="card hover:shadow-xl transition-all duration-300 border-0 shadow-lg overflow-hidden">
    <div class="p-5 border-b flex justify-between items-center">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-xl bg-gradient-to-r {{ $color }} flex items-center justify-center text-white shadow-md">
                <i class="fas fa-{{ $icon }}"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold">{{ $role->name }}</h2>
                <p class="text-sm text-muted-foreground">{{ $role->description }}</p>
            </div>
        </div>
    </div>

    <div class="p-5 space-y-2">
        <div class="flex justify-between items-center">
            <div class="text-sm text-muted-foreground">Utilisateurs : {{ $role->users()->count() }}</div>
            <div class="text-sm text-muted-foreground">Permissions : {{ $role->permissions->count() }}</div>
        </div>

        <div class="flex flex-wrap gap-1">
            @foreach($role->permissions->take(4) as $permission)
                <span class="badge badge-outline text-xs">{{ $permission->name }}</span>
            @endforeach
            @if($role->permissions->count() > 4)
                <span class="badge badge-outline text-xs">+{{ $role->permissions->count() - 4 }} autres</span>
            @endif
        </div>

        <div class="flex justify-between items-center pt-4 border-t">
            <button x-on:click="$dispatch('open-modal', 'editRoleModal-{{ $role->id }}')" class="btn btn-outline btn-sm">
                <i class="fas fa-edit mr-1"></i> Modifier
            </button>

            @if ($role->users()->count() === 0)
                <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" onsubmit="return confirm('Supprimer ce rôle ?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-outline btn-sm text-red-600 hover:bg-red-50">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </form>
            @endif
        </div>
    </div>

    @include('admin.partials.roles._edit', ['role' => $role])
</div>
