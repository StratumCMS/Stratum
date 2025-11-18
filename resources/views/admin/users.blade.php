@extends('admin.layouts.admin')

@section('title', 'Utilisateurs')

@section('content')
    <div x-data="usersManager()" x-init="init()" class="space-y-4 sm:space-y-6">

        <div x-show="showDeleteModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
            <div x-show="showDeleteModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="bg-card border border-border rounded-xl shadow-2xl max-w-md w-full p-6">

                <div class="flex items-center justify-center w-16 h-16 rounded-full bg-destructive/10 mx-auto mb-4">
                    <i class="fas fa-exclamation-triangle text-destructive text-2xl"></i>
                </div>

                <div class="text-center mb-6">
                    <h3 class="text-lg font-semibold text-foreground mb-2">
                        Confirmer la suppression
                    </h3>
                    <p class="text-muted-foreground" x-text="`Êtes-vous sûr de vouloir supprimer « ${deleteItemName} » ?`"></p>
                    <p class="text-sm text-muted-foreground mt-2">
                        Cette action est irréversible et supprimera définitivement l'utilisateur.
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <button @click="showDeleteModal = false"
                            class="flex-1 inline-flex items-center justify-center gap-2 rounded-lg border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2 text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-ring">
                        <i class="fas fa-times w-4 h-4"></i>
                        Annuler
                    </button>
                    <button @click="confirmDelete()"
                            :disabled="isDeleting"
                            class="flex-1 inline-flex items-center justify-center gap-2 rounded-lg bg-destructive text-destructive-foreground hover:bg-destructive/90 h-10 px-4 py-2 text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-ring disabled:opacity-50 disabled:cursor-not-allowed"
                            :class="{'opacity-50 cursor-not-allowed': isDeleting}">
                        <i class="fas" :class="isDeleting ? 'fa-spinner fa-spin' : 'fa-trash-alt'"></i>
                        <span x-text="isDeleting ? 'Suppression...' : 'Supprimer'"></span>
                    </button>
                </div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center space-x-4">
                <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
                    <i class="fas fa-users text-primary text-sm"></i>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-semibold text-foreground">Gestion des Utilisateurs</h1>
                    <p class="text-sm text-muted-foreground hidden sm:block">
                        Gérez les comptes utilisateurs et leurs permissions
                    </p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            @foreach ($roles as $role)
                <div class="rounded-xl border bg-card text-card-foreground shadow-sm p-4 sm:p-6 transition-all duration-200 hover:shadow-md cursor-pointer"
                     @click="filterByRole('{{ $role['name'] }}')"
                     :class="{ 'ring-2 ring-primary': activeRole === '{{ $role['name'] }}' }">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs sm:text-sm font-medium text-muted-foreground mb-1">{{ $role['name'] }}</p>
                            <h3 class="text-xl sm:text-2xl font-bold text-foreground">{{ $role['count'] }}</h3>
                        </div>
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white" style="background-color: {{ $role['color'] }}">
                            <i class="fas fa-{{ $role['icon'] }} text-sm"></i>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
            <div class="p-4 sm:p-6 border-b border-border">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center">
                        <i class="fas fa-filter text-blue-500 text-sm"></i>
                    </div>
                    <h2 class="text-lg sm:text-xl font-semibold text-foreground">Filtres et recherche</h2>
                </div>
            </div>
            <div class="p-4 sm:p-6 space-y-4 sm:space-y-0 sm:flex sm:items-center sm:space-x-4">
                <div class="flex-1 relative">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-muted-foreground w-4 h-4"></i>
                    <input type="text"
                           x-model="searchQuery"
                           placeholder="Rechercher par nom ou email..."
                           class="flex h-10 w-full rounded-lg border border-input bg-background pl-10 pr-4 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 transition-colors">
                </div>

                <div class="flex flex-wrap gap-2">
                    <button @click="filterByRole(null)"
                            class="inline-flex items-center justify-center gap-2 rounded-lg px-3 py-2 text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-ring"
                            :class="activeRole === null ? 'bg-primary text-primary-foreground hover:bg-primary/90' : 'border border-input bg-background hover:bg-accent hover:text-accent-foreground'">
                        Tous les utilisateurs
                    </button>
                    @foreach ($roles as $role)
                        <button @click="filterByRole('{{ $role['name'] }}')"
                                class="inline-flex items-center justify-center gap-2 rounded-lg px-3 py-2 text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-ring"
                                :class="activeRole === '{{ $role['name'] }}' ? 'bg-primary text-primary-foreground hover:bg-primary/90' : 'border border-input bg-background hover:bg-accent hover:text-accent-foreground'">
                            {{ $role['name'] }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
            <div class="p-4 sm:p-6 border-b border-border">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center">
                            <i class="fas fa-list text-primary text-sm"></i>
                        </div>
                        <h2 class="text-lg sm:text-xl font-semibold text-foreground">Liste des utilisateurs</h2>
                    </div>
                    <div class="flex items-center space-x-2 text-sm text-muted-foreground">
                        <span x-text="`${filteredUsers.length} utilisateur(s)`"></span>
                    </div>
                </div>
            </div>

            <div class="block sm:hidden">
                <div class="divide-y divide-border">
                    @foreach ($users as $user)
                        <div class="p-4 hover:bg-muted/20 transition-colors">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center space-x-3 flex-1 min-w-0">
                                    <img src="{{ $user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=6366f1&color=fff' }}"
                                         alt="Avatar de {{ $user->name }}"
                                         class="w-10 h-10 rounded-full object-cover flex-shrink-0">
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-medium text-foreground truncate">{{ $user->name }}</h4>
                                        <p class="text-sm text-muted-foreground truncate flex items-center space-x-1 mt-1">
                                            <i class="fas fa-envelope w-3 h-3"></i>
                                            <span>{{ $user->email }}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between text-xs text-muted-foreground mb-3">
                                <div class="flex items-center space-x-3">
                                    <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium text-white" style="background-color: {{ $user->role_color }}">
                                        {{ $user->display_role }}
                                    </span>

                                    <span class="inline-flex items-center rounded-full bg-green-500/10 text-green-600 px-2 py-1 text-xs font-medium">
                                        <i class="fas fa-circle mr-1 w-2 h-2"></i>
                                        {{ $user->status }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between text-xs text-muted-foreground mb-4">
                                <span class="flex items-center space-x-1">
                                    <i class="fas fa-clock w-3 h-3"></i>
                                    <span>{{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Jamais connecté' }}</span>
                                </span>
                            </div>

                            <div class="flex items-center justify-between">
                                <a href="{{ route('admin.users.edit', $user->id) }}"
                                   class="inline-flex items-center justify-center gap-1 rounded-lg p-2 text-muted-foreground hover:text-foreground hover:bg-accent transition-colors text-xs">
                                    <i class="fas fa-edit w-3 h-3"></i>
                                    Modifier
                                </a>
                                <button @click="openDeleteModal('{{ $user->id }}', '{{ $user->name }}')"
                                        class="inline-flex items-center justify-center gap-1 rounded-lg p-2 text-muted-foreground hover:text-destructive hover:bg-destructive/10 transition-colors text-xs">
                                    <i class="fas fa-trash-alt w-3 h-3"></i>
                                    Supprimer
                                </button>
                            </div>
                        </div>
                    @endforeach

                    @if($users->isEmpty())
                        <div class="p-8 text-center">
                            <div class="w-16 h-16 rounded-full bg-muted/50 flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-users text-2xl text-muted-foreground"></i>
                            </div>
                            <h3 class="text-lg font-medium text-foreground mb-2">Aucun utilisateur</h3>
                            <p class="text-muted-foreground">Aucun utilisateur n'est inscrit pour le moment</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="hidden sm:block overflow-auto">
                <table class="w-full">
                    <thead>
                    <tr class="border-b border-border bg-muted/10">
                        <th class="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Utilisateur</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Rôle</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Statut</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Dernière connexion</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider w-24">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                    @foreach ($users as $user)
                        <tr class="hover:bg-muted/20 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center space-x-3 min-w-0">
                                    <img src="{{ $user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=6366f1&color=fff' }}"
                                         alt="Avatar de {{ $user->name }}"
                                         class="w-10 h-10 rounded-full object-cover flex-shrink-0">
                                    <div class="min-w-0 flex-1">
                                        <h4 class="font-medium text-foreground truncate">{{ $user->name }}</h4>
                                        <p class="text-sm text-muted-foreground truncate flex items-center space-x-1 mt-1">
                                            <i class="fas fa-envelope w-4 h-4"></i>
                                            <span>{{ $user->email }}</span>
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium text-white" style="background-color: {{ $user->role_color }}">
                                        {{ $user->display_role }}
                                    </span>
                            </td>

                            <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full bg-green-500/10 text-green-600 px-2.5 py-0.5 text-xs font-medium">
                                        <i class="fas fa-circle mr-1.5 w-2 h-2"></i>
                                        {{ $user->status }}
                                    </span>
                            </td>

                            <td class="px-4 py-3">
                                <div class="flex items-center space-x-2 text-sm text-muted-foreground">
                                    <i class="fas fa-clock w-4 h-4"></i>
                                    <span>{{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Jamais connecté' }}</span>
                                </div>
                            </td>

                            <td class="px-4 py-3">
                                <div class="flex items-center space-x-1">
                                    <a href="{{ route('admin.users.edit', $user->id) }}"
                                       class="inline-flex items-center justify-center rounded-lg p-2 text-muted-foreground hover:text-foreground hover:bg-accent transition-colors focus:outline-none focus:ring-2 focus:ring-ring"
                                       title="Modifier l'utilisateur">
                                        <i class="fas fa-edit w-4 h-4"></i>
                                    </a>
                                    <button @click="openDeleteModal('{{ $user->id }}', '{{ $user->name }}')"
                                            class="inline-flex items-center justify-center rounded-lg p-2 text-muted-foreground hover:text-destructive hover:bg-destructive/10 transition-colors focus:outline-none focus:ring-2 focus:ring-ring"
                                            title="Supprimer l'utilisateur">
                                        <i class="fas fa-trash-alt w-4 h-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach

                    @if($users->isEmpty())
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center">
                                <div class="w-16 h-16 rounded-full bg-muted/50 flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-users text-2xl text-muted-foreground"></i>
                                </div>
                                <h3 class="text-lg font-medium text-foreground mb-2">Aucun utilisateur</h3>
                                <p class="text-muted-foreground">Aucun utilisateur n'est inscrit pour le moment</p>
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        function usersManager() {
            return {
                searchQuery: '',
                activeRole: null,
                showDeleteModal: false,
                isDeleting: false,
                deleteItemId: null,
                deleteItemName: '',

                get filteredUsers() {
                    return [];
                },

                filterByRole(role) {
                    this.activeRole = role;

                    const url = new URL(window.location.href);
                    if (role) {
                        url.searchParams.set('role', role);
                    } else {
                        url.searchParams.delete('role');
                    }

                    window.location.href = url.toString();
                },

                openDeleteModal(userId, userName) {
                    this.deleteItemId = userId;
                    this.deleteItemName = userName;
                    this.showDeleteModal = true;
                    this.isDeleting = false;
                },

                async confirmDelete() {
                    if (!this.deleteItemId) return;

                    this.isDeleting = true;

                    try {
                        const response = await fetch(`/admin/users/${this.deleteItemId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });

                        if (response.ok) {
                            window.location.reload();
                        } else {
                            throw new Error('Erreur lors de la suppression');
                        }
                    } catch (error) {
                        console.error('Error deleting user:', error);
                        this.showNotification('Erreur lors de la suppression', 'error');
                        this.isDeleting = false;
                        this.showDeleteModal = false;
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
