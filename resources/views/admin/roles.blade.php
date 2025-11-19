@extends('admin.layouts.admin')

@section('title', 'Gestion des rôles')

@section('content')
    <div x-data="rolesManager()" x-init="init()" class="space-y-4 sm:space-y-6">

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
                        Cette action est irréversible. Les utilisateurs avec ce rôle devront être réaffectés.
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

        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center space-x-4">
                <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
                    <i class="fas fa-shield-alt text-primary text-sm"></i>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-semibold text-foreground">Rôles & Permissions</h1>
                    <p class="text-sm text-muted-foreground hidden sm:block">
                        Gérez les rôles utilisateurs et leurs accès au système
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="flex items-center gap-6">
                    <div class="text-center">
                        <div class="text-xl sm:text-2xl font-bold text-primary">{{ $roles->count() }}</div>
                        <div class="text-xs text-muted-foreground">Rôles</div>
                    </div>
                    <div class="w-px h-8 bg-border"></div>
                    <div class="text-center">
                        <div class="text-xl sm:text-2xl font-bold text-primary">{{ \App\Models\User::count() }}</div>
                        <div class="text-xs text-muted-foreground">Utilisateurs</div>
                    </div>
                </div>

                <button @click="showCreateModal = true"
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2 text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-ring w-full sm:w-auto">
                    <i class="fas fa-plus w-4 h-4"></i>
                    <span>Nouveau rôle</span>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-6">
            @foreach($roles as $role)
                @php
                    $color = $role->color ?? '#3b82f6';
                    $icon = $role->icon ?? 'user';
                    $userCount = $role->users()->count();
                    $permissionCount = $role->permissions->count();
                @endphp

                <div class="rounded-xl border bg-card text-card-foreground shadow-sm transition-all duration-300 hover:shadow-lg group">
                    <div class="p-4 sm:p-6 border-b border-border">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center space-x-4 flex-1 min-w-0">
                                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white shadow-lg transition-transform duration-300 group-hover:scale-110 flex-shrink-0"
                                     style="background: {{ $color }};">
                                    <i class="fas fa-{{ $icon }} text-lg"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-lg font-semibold text-foreground truncate">{{ $role->name }}</h3>
                                    <p class="text-sm text-muted-foreground line-clamp-2 mt-1" title="{{ $role->description }}">
                                        {{ $role->description ?: 'Aucune description' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 sm:p-6 space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-6">
                                <div class="text-center">
                                    <div class="text-xl font-bold text-primary">{{ $userCount }}</div>
                                    <div class="text-xs text-muted-foreground">Utilisateurs</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-xl font-bold text-primary">{{ $permissionCount }}</div>
                                    <div class="text-xs text-muted-foreground">Permissions</div>
                                </div>
                            </div>
                            <span class="text-xs font-medium bg-muted rounded-lg px-2 py-1 font-mono" title="Couleur du rôle">
                                {{ $color }}
                            </span>
                        </div>

                        <div class="space-y-2">
                            <h4 class="text-sm font-medium text-muted-foreground">Permissions principales</h4>
                            <div class="flex flex-wrap gap-1">
                                @foreach($role->permissions->take(3) as $perm)
                                    <span class="inline-flex items-center rounded-full bg-muted px-2 py-1 text-xs font-medium text-foreground">
                                        {{ $perm->name }}
                                    </span>
                                @endforeach
                                @if($permissionCount > 3)
                                    <span class="inline-flex items-center rounded-full bg-muted px-2 py-1 text-xs font-medium text-foreground">
                                        +{{ $permissionCount - 3 }} autres
                                    </span>
                                @endif
                                @if($permissionCount === 0)
                                    <span class="inline-flex items-center rounded-full bg-muted px-2 py-1 text-xs font-medium text-muted-foreground">
                                        Aucune permission
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-4 border-t border-border">
                            <button @click="showEditModalId = {{ $role->id }}"
                                    class="inline-flex items-center justify-center gap-2 rounded-lg border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-3 text-sm font-medium transition-colors flex-1 mr-2">
                                <i class="fas fa-edit w-4 h-4"></i>
                                <span class="hidden sm:inline">Modifier</span>
                            </button>
                            @if($userCount === 0)
                                <button @click="openDeleteModal('{{ $role->id }}', '{{ $role->name }}')"
                                        class="inline-flex items-center justify-center rounded-lg p-2 text-muted-foreground hover:text-destructive hover:bg-destructive/10 transition-colors"
                                        title="Supprimer le rôle">
                                    <i class="fas fa-trash-alt w-4 h-4"></i>
                                </button>
                            @else
                                <button disabled
                                        class="inline-flex items-center justify-center rounded-lg p-2 text-muted-foreground/50 cursor-not-allowed"
                                        title="Impossible de supprimer - {{ $userCount }} utilisateur(s) assigné(s)">
                                    <i class="fas fa-trash-alt w-4 h-4"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <x-role-modal :role="$role" :isEdit="true" x-model="showEditModalId" />
            @endforeach
        </div>

        @if($roles->isEmpty())
            <div class="rounded-xl border bg-card text-card-foreground shadow-sm p-8 text-center">
                <div class="w-16 h-16 rounded-full bg-muted/50 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-shield-alt text-2xl text-muted-foreground"></i>
                </div>
                <h3 class="text-lg font-medium text-foreground mb-2">Aucun rôle configuré</h3>
                <p class="text-muted-foreground mb-4">Commencez par créer votre premier rôle pour organiser les permissions</p>
                <button @click="showCreateModal = true"
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2 text-sm font-medium transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    Créer un rôle
                </button>
            </div>
        @endif

        <x-role-modal :role="null" :isEdit="false" x-model="showCreateModal" />

    </div>
@endsection

@push('scripts')
    <script>
        function rolesManager() {
            return {
                showCreateModal: false,
                showEditModalId: null,
                showDeleteModal: false,
                isDeleting: false,
                deleteItemId: null,
                deleteItemName: '',

                init(){

                },

                openDeleteModal(roleId, roleName) {
                    this.deleteItemId = roleId;
                    this.deleteItemName = roleName;
                    this.showDeleteModal = true;
                    this.isDeleting = false;
                },

                async confirmDelete() {
                    if (!this.deleteItemId) return;

                    this.isDeleting = true;

                    try {
                        const response = await fetch(`/admin/roles/${this.deleteItemId}`, {
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
                        console.error('Error deleting role:', error);
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
