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
    <div x-data="userEdit()" x-init="init()" class="max-w-6xl mx-auto space-y-4 sm:space-y-6">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.users') }}"
                   class="inline-flex items-center justify-center gap-2 rounded-lg border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2 text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-ring">
                    <i class="fas fa-arrow-left w-4 h-4"></i>
                    <span class="hidden sm:inline">Retour aux utilisateurs</span>
                    <span class="sm:hidden">Retour</span>
                </a>
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
                        <i class="fas fa-user-edit text-primary text-sm"></i>
                    </div>
                    <div>
                        <h1 class="text-xl sm:text-2xl font-semibold text-foreground">Modifier l'utilisateur</h1>
                        <p class="text-sm text-muted-foreground hidden sm:block">
                            Modifiez les informations et permissions de {{ $user->name }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap gap-2">
                <span class="inline-flex items-center rounded-full bg-muted px-3 py-1 text-xs font-medium text-foreground">
                    ID: {{ $user->id }}
                </span>
                <span class="inline-flex items-center rounded-full bg-green-500/10 text-green-600 px-3 py-1 text-xs font-medium">
                    <i class="fas fa-circle mr-1.5 w-2 h-2"></i>
                    {{ $user->status }}
                </span>
            </div>
        </div>

        <form action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data" class="space-y-4 sm:space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 sm:gap-6">
                <div class="xl:col-span-2 space-y-4 sm:space-y-6">
                    <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
                        <div class="p-4 sm:p-6 border-b border-border">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center">
                                    <i class="fas fa-user text-blue-500 text-sm"></i>
                                </div>
                                <h2 class="text-lg sm:text-xl font-semibold text-foreground">Informations générales</h2>
                            </div>
                        </div>
                        <div class="p-4 sm:p-6 space-y-4 sm:space-y-6">
                            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                                <div class="flex-shrink-0">
                                    <div class="relative group">
                                        <img src="{{ $user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=6366f1&color=fff&size=128' }}"
                                             alt="Avatar de {{ $user->name }}"
                                             class="w-20 h-20 rounded-full object-cover border-4 border-background shadow-lg">
                                        <div class="absolute inset-0 rounded-full bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                            <i class="fas fa-camera text-white text-lg"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <label for="avatar"
                                           class="cursor-pointer inline-flex items-center justify-center gap-2 rounded-lg border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2 text-sm font-medium transition-colors w-full sm:w-auto">
                                        <i class="fas fa-upload w-4 h-4"></i>
                                        Changer l'avatar
                                    </label>
                                    <input id="avatar"
                                           type="file"
                                           name="avatar"
                                           class="hidden"
                                           accept="image/*"
                                           @change="previewAvatar($event)">
                                    <p class="text-xs text-muted-foreground mt-2">
                                        PNG, JPG ou WEBP - Max. 2 Mo
                                    </p>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium text-foreground flex items-center justify-between">
                                    <span>Nom complet</span>
                                    <span class="text-xs text-muted-foreground font-normal">Requis</span>
                                </label>
                                <input type="text"
                                       name="name"
                                       value="{{ old('name', $user->name) }}"
                                       class="flex h-10 w-full rounded-lg border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 transition-colors"
                                       placeholder="Nom complet de l'utilisateur"
                                       required>
                                @error('name')
                                <p class="text-sm text-destructive flex items-center space-x-1">
                                    <i class="fas fa-exclamation-circle w-4 h-4"></i>
                                    <span>{{ $message }}</span>
                                </p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium text-foreground flex items-center justify-between">
                                    <span>Adresse email</span>
                                    <span class="text-xs text-muted-foreground font-normal">Requis</span>
                                </label>
                                <div class="relative">
                                    <i class="fas fa-envelope absolute left-3 top-1/2 transform -translate-y-1/2 text-muted-foreground w-4 h-4"></i>
                                    <input type="email"
                                           name="email"
                                           value="{{ old('email', $user->email) }}"
                                           class="flex h-10 w-full rounded-lg border border-input bg-background pl-10 pr-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 transition-colors"
                                           placeholder="adresse@exemple.com"
                                           required>
                                </div>
                                @error('email')
                                <p class="text-sm text-destructive flex items-center space-x-1">
                                    <i class="fas fa-exclamation-circle w-4 h-4"></i>
                                    <span>{{ $message }}</span>
                                </p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
                        <div class="p-4 sm:p-6 border-b border-border">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-lg bg-green-500/10 flex items-center justify-center">
                                    <i class="fas fa-info-circle text-green-500 text-sm"></i>
                                </div>
                                <h2 class="text-lg sm:text-xl font-semibold text-foreground">Informations supplémentaires</h2>
                            </div>
                        </div>
                        <div class="p-4 sm:p-6">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6 text-sm">
                                <div class="space-y-1">
                                    <label class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Créé le</label>
                                    <p class="font-medium text-foreground flex items-center space-x-2">
                                        <i class="fas fa-calendar-plus w-4 h-4 text-muted-foreground"></i>
                                        <span>{{ $user->created_at->format('d/m/Y à H:i') }}</span>
                                    </p>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Dernière modification</label>
                                    <p class="font-medium text-foreground flex items-center space-x-2">
                                        <i class="fas fa-edit w-4 h-4 text-muted-foreground"></i>
                                        <span>{{ $user->updated_at->format('d/m/Y à H:i') }}</span>
                                    </p>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Dernière connexion</label>
                                    <p class="font-medium text-foreground flex items-center space-x-2">
                                        <i class="fas fa-sign-in-alt w-4 h-4 text-muted-foreground"></i>
                                        <span>{{ $user->last_login_at ? $user->last_login_at->format('d/m/Y à H:i') : 'Jamais connecté' }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-4 sm:space-y-6">
                    <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
                        <div class="p-4 sm:p-6 border-b border-border">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-lg bg-purple-500/10 flex items-center justify-center">
                                    <i class="fas fa-shield-alt text-purple-500 text-sm"></i>
                                </div>
                                <h2 class="text-lg font-semibold text-foreground">Rôle & statut</h2>
                            </div>
                        </div>
                        <div class="p-4 sm:p-6 space-y-4">
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-foreground">Rôle</label>
                                <select name="role"
                                        x-model="selectedRole"
                                        class="flex h-10 w-full rounded-lg border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 appearance-none transition-colors">
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}"
                                                @selected($user->hasRole($role->name))
                                                data-icon="{{ $role->icon }}"
                                                data-color="{{ $role->color }}"
                                                data-permissions="{{ $role->permissions->pluck('name') }}">
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div x-show="selectedRole"
                                 class="p-4 rounded-lg bg-muted/50 border border-border transition-all duration-200">
                                <template x-if="getRoleData(selectedRole)">
                                    <div>
                                        <div class="flex items-center space-x-3 mb-3">
                                            <div class="w-8 h-8 rounded flex items-center justify-center text-white"
                                                 :style="`background-color: ${getRoleData(selectedRole).color}`">
                                                <i class="fas text-sm" :class="`fa-${getRoleData(selectedRole).icon}`"></i>
                                            </div>
                                            <div>
                                                <h4 class="font-semibold text-foreground" x-text="selectedRole"></h4>
                                                <p class="text-xs text-muted-foreground" x-text="`${getRoleData(selectedRole).permissions.length} permission(s)`"></p>
                                            </div>
                                        </div>

                                        <div x-show="getRoleData(selectedRole).permissions.length > 0">
                                            <p class="text-xs font-medium text-muted-foreground mb-2">Permissions :</p>
                                            <div class="space-y-1 max-h-32 overflow-y-auto">
                                                <template x-for="permission in getRoleData(selectedRole).permissions" :key="permission">
                                                    <div class="flex items-center space-x-2 text-xs">
                                                        <i class="fas fa-check text-green-500 w-3 h-3"></i>
                                                        <span class="text-foreground" x-text="permission"></span>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium text-foreground">Statut</label>
                                <select name="status"
                                        class="flex h-10 w-full rounded-lg border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 appearance-none transition-colors">
                                    <option value="Actif" @selected($user->status === 'Actif')>Actif</option>
                                    <option value="Inactif" @selected($user->status === 'Inactif')>Inactif</option>
                                    <option value="Suspendu" @selected($user->status === 'Suspendu')>Suspendu</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
                        <div class="p-4 sm:p-6 space-y-3">
                            <button type="submit"
                                    :disabled="isSubmitting"
                                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2 text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-ring disabled:opacity-50 disabled:cursor-not-allowed w-full"
                                    :class="{'opacity-50 cursor-not-allowed': isSubmitting}">
                                <i class="fas" :class="isSubmitting ? 'fa-spinner fa-spin' : 'fa-save'"></i>
                                <span x-text="isSubmitting ? 'Enregistrement...' : 'Enregistrer'"></span>
                            </button>
                            <a href="{{ route('admin.users') }}"
                               class="inline-flex items-center justify-center gap-2 rounded-lg border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2 text-sm font-medium transition-colors w-full">
                                <i class="fas fa-times w-4 h-4"></i>
                                Annuler
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>

    </div>
@endsection

@push('scripts')
    <script>
        function userEdit() {
            return {
                roles: @json($roleData),
                selectedRole: '{{ $user->roles->first()->name ?? $roles->first()->name }}',
                isSubmitting: false,

                getRoleData(roleName) {
                    return this.roles[roleName] || null;
                },

                previewAvatar(event) {
                    const file = event.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            // Mettre à jour l'aperçu de l'avatar
                            const img = document.querySelector('img[alt*="Avatar de"]');
                            if (img) {
                                img.src = e.target.result;
                            }
                        };
                        reader.readAsDataURL(file);
                    }
                },

                showNotification(message, type = 'info') {
                    const toast = document.createElement('div');
                    toast.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg border transition-all duration-300 transform translate-x-full ${
                        type === 'success' ? 'bg-green-500/10 border-green-500/20 text-green-600' :
                            type === 'error' ? 'bg-red-500/10 border-red-500/20 text-red-600' :
                                'bg-blue-500/10 border-blue-500/20 text-blue-600'
                    }`;
                    toast.innerHTML = `
                        <div class="flex items-center space-x-2">
                            <i class="fas ${
                        type === 'success' ? 'fa-check-circle' :
                            type === 'error' ? 'fa-exclamation-circle' :
                                'fa-info-circle'
                    }"></i>
                            <span class="text-sm font-medium">${message}</span>
                        </div>
                    `;

                    document.body.appendChild(toast);

                    setTimeout(() => toast.classList.remove('translate-x-full'), 100);

                    setTimeout(() => {
                        toast.classList.add('translate-x-full');
                        setTimeout(() => {
                            if (toast.parentNode) {
                                toast.parentNode.removeChild(toast);
                            }
                        }, 300);
                    }, 3000);
                }
            }
        }
    </script>
@endpush
