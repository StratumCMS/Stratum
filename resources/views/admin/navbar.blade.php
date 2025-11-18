@extends('admin.layouts.admin')

@section('title', 'Gestion Navigation')

@section('content')
    <div x-data="navbar()" x-init="init()" class="space-y-4 sm:space-y-6">

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
                        Cette action est irréversible et supprimera définitivement l'élément de navigation.
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
                    <i class="fas fa-bars text-primary text-sm"></i>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-semibold text-foreground">Gestion de la Navigation</h1>
                    <p class="text-sm text-muted-foreground hidden sm:block">
                        Organisez le menu de navigation de votre site
                    </p>
                </div>
            </div>

            <a href="{{ route('navbar.create') }}"
               class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2 text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-ring w-full sm:w-auto">
                <i class="fas fa-plus w-4 h-4"></i>
                <span>Ajouter un élément</span>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
            <div class="rounded-xl border bg-card text-card-foreground shadow-sm p-4 sm:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs sm:text-sm font-medium text-muted-foreground mb-1">Éléments totaux</p>
                        <h3 class="text-xl sm:text-2xl font-bold text-foreground">{{ $items->count() }}</h3>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-blue-500/10 flex items-center justify-center">
                        <i class="fas fa-link text-blue-500 text-sm"></i>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border bg-card text-card-foreground shadow-sm p-4 sm:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs sm:text-sm font-medium text-muted-foreground mb-1">Menus déroulants</p>
                        <h3 class="text-xl sm:text-2xl font-bold text-foreground">{{ $items->where('type', 'dropdown')->count() }}</h3>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-purple-500/10 flex items-center justify-center">
                        <i class="fas fa-caret-down text-purple-500 text-sm"></i>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border bg-card text-card-foreground shadow-sm p-4 sm:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs sm:text-sm font-medium text-muted-foreground mb-1">Liens simples</p>
                        <h3 class="text-xl sm:text-2xl font-bold text-foreground">{{ $items->where('type', 'link')->count() }}</h3>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-green-500/10 flex items-center justify-center">
                        <i class="fas fa-external-link-alt text-green-500 text-sm"></i>
                    </div>
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
                        <h2 class="text-lg sm:text-xl font-semibold text-foreground">Éléments de Navigation</h2>
                    </div>
                    <div class="flex items-center space-x-2 text-xs text-muted-foreground">
                        <i class="fas fa-arrows-alt w-3 h-3"></i>
                        <span class="hidden sm:inline">Glissez pour réorganiser</span>
                    </div>
                </div>
            </div>

            <div class="block sm:hidden">
                <div id="sortable-mobile" class="divide-y divide-border">
                    @foreach($items as $item)
                        @include('admin.partials.navbar-row-mobile', ['item' => $item, 'isChild' => false])
                    @endforeach

                    @if($items->isEmpty())
                        <div class="p-8 text-center">
                            <div class="w-16 h-16 rounded-full bg-muted/50 flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-bars text-2xl text-muted-foreground"></i>
                            </div>
                            <h3 class="text-lg font-medium text-foreground mb-2">Aucun élément de navigation</h3>
                            <p class="text-muted-foreground mb-4">Commencez par créer votre premier élément de menu</p>
                            <a href="{{ route('navbar.create') }}"
                               class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2 text-sm font-medium transition-colors">
                                <i class="fas fa-plus mr-2"></i>
                                Créer un élément
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="hidden sm:block overflow-auto">
                <table class="w-full">
                    <thead>
                    <tr class="border-b border-border bg-muted/10">
                        <th class="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider w-8"></th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Nom</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">URL / Cible</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Position</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider w-24">Actions</th>
                    </tr>
                    </thead>
                    <tbody id="sortable" class="divide-y divide-border">
                    @foreach($items as $item)
                        @include('admin.partials.navbar-row', ['item' => $item, 'isChild' => false])
                    @endforeach

                    @if($items->isEmpty())
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center">
                                <div class="w-16 h-16 rounded-full bg-muted/50 flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-bars text-2xl text-muted-foreground"></i>
                                </div>
                                <h3 class="text-lg font-medium text-foreground mb-2">Aucun élément de navigation</h3>
                                <p class="text-muted-foreground mb-4">Commencez par créer votre premier élément de menu</p>
                                <a href="{{ route('navbar.create') }}"
                                   class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2 text-sm font-medium transition-colors">
                                    <i class="fas fa-plus mr-2"></i>
                                    Créer un élément
                                </a>
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
            <div class="p-4 sm:p-6 border-b border-border">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center">
                        <i class="fas fa-info-circle text-blue-500 text-sm"></i>
                    </div>
                    <h2 class="text-lg sm:text-xl font-semibold text-foreground">Informations</h2>
                </div>
            </div>
            <div class="p-4 sm:p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-3">
                        <h3 class="text-sm font-medium text-foreground flex items-center space-x-2">
                            <i class="fas fa-arrows-alt text-muted-foreground w-4 h-4"></i>
                            <span>Organisation</span>
                        </h3>
                        <ul class="text-sm text-muted-foreground space-y-2">
                            <li class="flex items-center space-x-2">
                                <i class="fas fa-grip-lines text-muted-foreground/60 w-4 h-4"></i>
                                <span>Glissez-déposez les éléments pour réorganiser</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <i class="fas fa-sitemap text-muted-foreground/60 w-4 h-4"></i>
                                <span>Les éléments de type "Dropdown" peuvent contenir des sous-éléments</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <i class="fas fa-eye text-muted-foreground/60 w-4 h-4"></i>
                                <span>La position détermine l'ordre d'affichage dans le menu</span>
                            </li>
                        </ul>
                    </div>

                    <div class="space-y-3">
                        <h3 class="text-sm font-medium text-foreground flex items-center space-x-2">
                            <i class="fas fa-puzzle-piece text-muted-foreground w-4 h-4"></i>
                            <span>Types d'éléments</span>
                        </h3>
                        <ul class="text-sm text-muted-foreground space-y-2">
                            <li class="flex items-center space-x-2">
                                <span class="inline-flex items-center rounded-full bg-green-500/10 text-green-600 px-2 py-1 text-xs font-medium">
                                    Lien
                                </span>
                                <span>Redirige vers une URL spécifique</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <span class="inline-flex items-center rounded-full bg-purple-500/10 text-purple-600 px-2 py-1 text-xs font-medium">
                                    Dropdown
                                </span>
                                <span>Menu déroulant avec sous-éléments</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    <script>
        function navbar() {
            return {
                isReordering: false,
                showDeleteModal: false,
                isDeleting: false,
                deleteItemId: null,
                deleteItemName: '',

                init() {
                    this.initSortable();
                },

                initSortable() {
                    if (document.getElementById('sortable')) {
                        new Sortable(document.getElementById('sortable'), {
                            animation: 150,
                            handle: '.sortable-handle',
                            ghostClass: 'sortable-ghost',
                            chosenClass: 'sortable-chosen',
                            dragClass: 'sortable-drag',
                            onStart: () => {
                                this.isReordering = true;
                            },
                            onEnd: (evt) => {
                                this.isReordering = false;
                                this.saveOrder();
                            }
                        });
                    }

                    if (document.getElementById('sortable-mobile')) {
                        new Sortable(document.getElementById('sortable-mobile'), {
                            animation: 150,
                            handle: '.sortable-handle',
                            ghostClass: 'sortable-ghost',
                            chosenClass: 'sortable-chosen',
                            dragClass: 'sortable-drag',
                            onStart: () => {
                                this.isReordering = true;
                            },
                            onEnd: (evt) => {
                                this.isReordering = false;
                                this.saveOrder();
                            }
                        });
                    }
                },

                async saveOrder() {
                    try {
                        const desktopItems = document.querySelectorAll('#sortable tr[data-id]');
                        const mobileItems = document.querySelectorAll('#sortable-mobile [data-id]');

                        const order = [...(desktopItems.length ? desktopItems : mobileItems)].map(el => el.dataset.id);

                        const response = await fetch("{{ route('navbar.reorder') }}", {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({ order })
                        });

                        if (response.ok) {
                            this.showNotification('Ordre mis à jour avec succès', 'success');
                        } else {
                            throw new Error('Erreur lors de la mise à jour');
                        }
                    } catch (error) {
                        console.error('Error saving order:', error);
                        this.showNotification('Erreur lors de la mise à jour de l\'ordre', 'error');
                    }
                },

                openDeleteModal(itemId, itemName) {
                    this.deleteItemId = itemId;
                    this.deleteItemName = itemName;
                    this.showDeleteModal = true;
                    this.isDeleting = false;
                },

                async confirmDelete() {
                    if (!this.deleteItemId) return;

                    this.isDeleting = true;

                    try {
                        const response = await fetch(`/admin/navbar/${this.deleteItemId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });

                        if (response.ok) {
                            document.querySelector(`[data-id="${this.deleteItemId}"]`).remove();
                            this.showNotification('Élément supprimé avec succès', 'success');
                        } else {
                            throw new Error('Erreur lors de la suppression');
                        }
                    } catch (error) {
                        console.error('Error deleting item:', error);
                        this.showNotification('Erreur lors de la suppression', 'error');
                    } finally {
                        this.isDeleting = false;
                        this.showDeleteModal = false;
                        this.deleteItemId = null;
                        this.deleteItemName = '';
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

@push('styles')
    <style>
        .sortable-ghost {
            opacity: 0.4;
            background: rgba(59, 130, 246, 0.1);
        }

        .sortable-chosen {
            transform: rotate(2deg);
        }

        .sortable-drag {
            opacity: 1 !important;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .sortable-handle {
            cursor: grab;
            transition: color 0.2s ease;
        }

        .sortable-handle:hover {
            color: rgb(59, 130, 246);
        }

        .sortable-handle:active {
            cursor: grabbing;
        }

        .sortable-item {
            transition: transform 0.15s ease;
        }
    </style>
@endpush
