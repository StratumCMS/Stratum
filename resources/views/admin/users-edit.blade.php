@extends('admin.layouts.admin')

@section('title', 'Modifier un utilisateur')

@php
    $roleData = [];
    $allPermissions = \App\Models\Permission::pluck('name');

    foreach ($roles as $role) {
        $roleData[$role->name] = [
            'icon' => $role->icon,
            'color' => $role->color,
            'permissions' => $role->permissions->pluck('name'),
        ];
    }
@endphp

@section('content')
    <div class="space-y-6 max-w-5xl mx-auto">

        <div class="flex items-center justify-between">
            <a href="{{ route('admin.users') }}" class="inline-flex items-center justify-center gap-2 rounded-md text-sm font-medium border border-input bg-background hover:bg-accent hover:text-accent-foreground hover-glow-purple h-9 px-3">
                <i class="fas fa-arrow-left mr-2"></i> Retour aux utilisateurs
            </a>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold text-foreground">ID: {{ $user->id }}</span>
                <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold {{ $user->status === 'Actif' ? 'bg-green-500 text-white' : 'bg-muted' }}">
                    {{ $user->status }}
                </span>
            </div>
        </div>

        <form action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="rounded-lg border bg-card shadow-sm">
                    <div class="p-6 pb-0">
                        <h2 class="text-2xl font-semibold flex items-center"><i class="fas fa-user mr-2"></i> Informations générales</h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex items-center space-x-4">
                            <div class="w-16 h-16 rounded-full overflow-hidden bg-gray-200">
                                @if ($user->avatar)
                                    <img src="{{ asset('storage/' . $user->avatar) }}" class="object-cover w-full h-full">
                                @else
                                    <div class="flex items-center justify-center w-full h-full bg-primary text-white font-semibold">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                @endif
                            </div>
                            <div>
                                <label for="avatar" class="cursor-pointer inline-flex items-center gap-2 rounded-md border border-input px-3 py-1.5 text-sm font-medium hover:bg-accent hover:text-accent-foreground">
                                    <i class="fas fa-upload mr-1"></i> Changer d'avatar
                                </label>
                                <input id="avatar" type="file" name="avatar" class="hidden" accept="image/*">
                                <p class="text-xs text-muted-foreground mt-1">PNG, JPG ou GIF (max. 2 Mo)</p>
                            </div>
                        </div>

                        <div>
                            <label class="label">Nom complet</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full h-10 px-3 rounded-md border bg-background text-foreground shadow-sm focus:ring-2 focus:ring-primary" required>
                        </div>

                        <div>
                            <label class="label">Adresse email</label>
                            <div class="relative">
                                <i class="fas fa-envelope absolute left-3 top-1/2 transform -translate-y-1/2 text-muted"></i>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="pl-10 w-full h-10 px-3 rounded-md border bg-background text-foreground shadow-sm focus:ring-2 focus:ring-primary" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-lg border bg-card shadow-sm">
                    <div class="p-6 pb-0">
                        <h2 class="text-2xl font-semibold flex items-center"><i class="fas fa-shield-alt mr-2"></i> Rôle & statut</h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="label">Rôle</label>
                            <select name="role" id="role-select" class="w-full h-10 px-3 rounded-md border bg-background text-foreground shadow-sm focus:ring-2 focus:ring-primary" required>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}" @selected($user->hasRole($role->name)) data-icon="{{ $role->icon }}" data-color="{{ $role->color }}">
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div id="role-summary" class="p-3 bg-muted/50 rounded-lg">
                        </div>

                        <div>
                            <label class="label">Statut</label>
                            <select name="status" class="w-full h-10 px-3 rounded-md border bg-background text-foreground shadow-sm focus:ring-2 focus:ring-primary" required>
                                <option value="Actif" @selected($user->status === 'Actif')>Actif</option>
                                <option value="Inactif" @selected($user->status === 'Inactif')>Inactif</option>
                                <option value="Suspendu" @selected($user->status === 'Suspendu')>Suspendu</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-lg border bg-card shadow-sm">
                <div class="p-6 pb-0">
                    <h2 class="text-2xl font-semibold">Informations supplémentaires</h2>
                </div>
                <div class="p-6 grid md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <label class="text-muted-foreground text-sm font-medium">Créé le</label>
                        <p class="font-medium">{{ $user->created_at->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <label class="text-muted-foreground text-sm font-medium">Dernière modification</label>
                        <p class="font-medium">{{ $user->updated_at->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <label class="text-muted-foreground text-sm font-medium">Dernière connexion</label>
                        <p class="font-medium">
                            {{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Jamais connecté' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.users') }}" class="inline-flex items-center gap-2 rounded-md border border-input px-3 h-9 text-sm hover:bg-accent hover:text-accent-foreground">Annuler</a>
                <button type="submit" class="inline-flex items-center gap-2 bg-primary text-white px-3 h-9 rounded-md hover:bg-primary/90">
                    <i class="fas fa-save mr-2"></i> Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>

    <script>
        const roles = @json($roleData);
        const allPermissions = @json($allPermissions);

        const roleSelect = document.getElementById('role-select');
        const roleSummary = document.getElementById('role-summary');

        function updateRoleSummary(roleName) {
            const role = roles[roleName];
            if (!role) return;

            const hasAll = JSON.stringify(role.permissions.sort()) === JSON.stringify(allPermissions.sort());

            let html = `
    <div class="flex items-center space-x-2 mb-2">
        <div class="w-6 h-6 rounded flex items-center justify-center" style="background-color: ${role?.color}">
            <i class="fas fa-${role?.icon} text-white"></i>
        </div>
        <span class="font-medium">${roleName}</span>
    </div>
    <p class="text-sm text-muted-foreground">
        ${
                hasAll
                    ? "Accès complet à toutes les fonctionnalités."
                    : `Ce rôle possède les permissions suivantes :
                <div class="mt-2">
                    <ul class="list-disc pl-6 space-y-1 text-xs text-foreground">
                        ${role.permissions.map(p => `<li class="leading-relaxed">${p}</li>`).join('')}
                    </ul>
                </div>`
            }
    </p>
`;


            roleSummary.innerHTML = html;
        }

        updateRoleSummary(roleSelect.value);
        roleSelect.addEventListener('change', e => updateRoleSummary(e.target.value));
    </script>
@endsection
