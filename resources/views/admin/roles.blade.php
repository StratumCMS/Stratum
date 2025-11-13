@extends('admin.layouts.admin')

@section('title', 'Gestion des rôles')

@section('content')
    <div x-data="{ showCreateModal: false, showEditModalId: null }" class="space-y-8">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="space-y-2">
                <h1 class="text-3xl font-bold bg-gradient-to-r from-primary to-primary/60 bg-clip-text text-transparent">
                    Rôles & Permissions
                </h1>
                <p class="text-muted-foreground text-lg">Gérez les rôles utilisateurs et leurs accès au système</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="flex flex-col items-center">
                    <div class="text-2xl font-bold text-primary">{{ $roles->count() }}</div>
                    <div class="text-sm text-muted-foreground">Rôles</div>
                </div>
                <div class="w-px h-10 bg-border"></div>
                <div class="flex flex-col items-center">
                    <div class="text-2xl font-bold text-primary">{{ \App\Models\User::count() }}</div>
                    <div class="text-sm text-muted-foreground">Utilisateurs</div>
                </div>
                <div class="w-px h-10 bg-border"></div>
                <button @click="showCreateModal = true"
                        class="h-11 rounded-md px-8 bg-primary text-primary-foreground hover:bg-primary/90 shadow-lg hover:shadow-xl transition-all flex items-center">
                    <i class="fas fa-plus mr-2"></i> Nouveau rôle
                </button>
            </div>
        </div>

        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3">
            @foreach($roles as $role)
                @php
                    $color = $role->color ?? '#3b82f6';
                    $icon = $role->icon ?? 'user';
                    $userCount = $role->users()->count();
                    $permissionCount = $role->permissions->count();
                @endphp

                <div class="rounded-lg border bg-card text-card-foreground shadow-sm group hover:shadow-xl transition-all duration-300 border-0 shadow-lg overflow-hidden">
                    <div class="flex flex-col space-y-1.5 p-6 pb-4">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-14 h-14 rounded-xl shadow-lg flex items-center justify-center transition-transform duration-300 transform group-hover:scale-110"
                                     style="background: {{ $color }};">
                                    <i class="fas fa-{{ $icon }} text-white text-xl"></i>
                                </div>
                                <div class="space-y-1">
                                    <h3 class="text-xl font-bold">{{ $role->name }}</h3>
                                    <p class="text-sm text-muted-foreground line-clamp-2">{{ $role->description }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 pb-6 space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-6">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-primary">{{ $userCount }}</div>
                                    <div class="text-xs text-muted-foreground">Utilisateurs</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-primary">{{ $permissionCount }}</div>
                                    <div class="text-xs text-muted-foreground">Permissions</div>
                                </div>
                            </div>
                            <span class="text-xs font-semibold bg-muted rounded px-2 py-1 shadow-sm">
                            {{ strtoupper($color) }}
                        </span>
                        </div>

                        <div class="space-y-2">
                            <h4 class="text-sm font-semibold text-muted-foreground mb-2">Permissions clés</h4>
                            <div class="flex flex-wrap gap-1">
                                @foreach($role->permissions->take(4) as $perm)
                                    <div class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold text-foreground">
                                        {{ $perm->name }}
                                    </div>
                                @endforeach
                                @if($permissionCount > 4)
                                    <div class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold text-foreground">
                                        +{{ $permissionCount - 4 }} autres
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="flex justify-between items-center pt-4 border-t">
                            <button @click="showEditModalId = {{ $role->id }}"
                                    class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 rounded-md px-3 flex-1 mr-2">
                                <i class="fas fa-edit w-4 h-4 mr-1"></i> Modifier
                            </button>
                            @if($userCount === 0)
                                <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" onsubmit="return confirm('Supprimer ce rôle ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 rounded-md px-3 text-destructive hover:text-destructive hover:bg-destructive/10">
                                        <i class="fas fa-trash-alt w-4 h-4"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                <x-role-modal :role="$role" :isEdit="true" x-model="showEditModalId" />
            @endforeach
        </div>

        <x-role-modal :role="null" :isEdit="false" x-model="showCreateModal" />
    </div>
@endsection
