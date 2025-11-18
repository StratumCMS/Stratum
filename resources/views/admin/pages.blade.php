@extends('admin.layouts.admin')

@section('title', 'Gestion des pages')

@section('content')
    <div x-data="pages()" x-init="init()" class="space-y-4 sm:space-y-6">

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <template x-for="(val, key) in stats" :key="key">
                <div class="rounded-xl border bg-card text-card-foreground shadow-sm p-4 sm:p-6 border-l-4 transition-all duration-300 hover:shadow-md"
                     :class="{
                        'border-l-primary': key === 'total',
                        'border-l-green-500': key === 'published',
                        'border-l-amber-500': key === 'draft',
                        'border-l-blue-500': key === 'homepage'
                     }">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs sm:text-sm font-medium text-muted-foreground mb-1" x-text="labels[key]"></p>
                            <h3 class="text-xl sm:text-2xl font-bold text-foreground" x-text="val || '0'"></h3>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center"
                             :class="{
                                'bg-primary/10': key === 'total',
                                'bg-green-500/10': key === 'published',
                                'bg-amber-500/10': key === 'draft',
                                'bg-blue-500/10': key === 'homepage'
                             }">
                            <i class="text-sm"
                               :class="{
                                'fas fa-file text-primary': key === 'total',
                                'fas fa-check-circle text-green-500': key === 'published',
                                'fas fa-edit text-amber-500': key === 'draft',
                                'fas fa-home text-blue-500': key === 'homepage'
                               }"></i>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
            <div class="p-4 sm:p-6 border-b border-border">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
                            <i class="fas fa-filter text-primary text-sm"></i>
                        </div>
                        <div>
                            <h2 class="text-lg sm:text-xl font-semibold text-foreground">Filtres et recherche</h2>
                            <p class="text-sm text-muted-foreground hidden sm:block">
                                Filtrez et recherchez dans vos pages
                            </p>
                        </div>
                    </div>

                    <a href="{{ route('admin.pages.create') }}"
                       class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-lg text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2 w-full sm:w-auto order-first sm:order-last">
                        <i class="fas fa-plus mr-2"></i>
                        <span>Nouvelle page</span>
                    </a>
                </div>
            </div>

            <div class="p-4 sm:p-6 space-y-4 sm:space-y-0 sm:flex sm:items-center sm:space-x-4">
                <div class="flex-1 relative">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-muted-foreground w-4 h-4"></i>
                    <input type="text"
                           x-model="filters.search"
                           placeholder="Rechercher une page..."
                           class="flex h-10 w-full rounded-lg border border-input bg-background pl-10 pr-4 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                </div>

                <div class="relative w-full sm:w-48">
                    <select x-model="filters.status"
                            class="flex h-10 w-full rounded-lg border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 appearance-none">
                        <option value="all">Tous les statuts</option>
                        <option value="published">Publié</option>
                        <option value="draft">Brouillon</option>
                        <option value="archived">Archivé</option>
                    </select>
                    <i class="fas fa-chevron-down absolute right-3 top-1/2 transform -translate-y-1/2 text-muted-foreground pointer-events-none"></i>
                </div>

                <button x-show="filters.search || filters.status !== 'all'"
                        @click="filters.search = ''; filters.status = 'all'"
                        class="sm:hidden w-full inline-flex items-center justify-center gap-2 rounded-lg border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2 text-sm font-medium transition-colors">
                    <i class="fas fa-times mr-2"></i>
                    Réinitialiser
                </button>
            </div>
        </div>

        <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
            <div class="p-4 sm:p-6 border-b border-border">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
                            <i class="fas fa-file text-primary text-sm"></i>
                        </div>
                        <div>
                            <h2 class="text-lg sm:text-xl font-semibold text-foreground">Pages</h2>
                            <p class="text-sm text-muted-foreground">
                                <span x-text="filtered.length"></span> page(s) trouvée(s)
                            </p>
                        </div>
                    </div>

                    <button x-show="filters.search || filters.status !== 'all'"
                            @click="filters.search = ''; filters.status = 'all'"
                            class="hidden sm:inline-flex items-center justify-center gap-2 rounded-lg border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-3 text-sm font-medium transition-colors">
                        <i class="fas fa-times mr-1"></i>
                        Réinitialiser
                    </button>
                </div>
            </div>

            <div class="block sm:hidden">
                <template x-for="page in filtered" :key="page.id">
                    <div class="p-4 border-b border-border last:border-b-0 hover:bg-muted/20 transition-colors">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1 min-w-0">
                                <h3 class="font-medium text-foreground truncate" x-text="page.title"></h3>
                                <p class="text-sm text-muted-foreground font-mono mt-1 truncate" x-text="page.slug"></p>
                            </div>
                            <div class="flex items-center space-x-1 ml-2">
                                <a :href="`/admin/pages/${page.id}/edit`"
                                   class="p-2 rounded-lg hover:bg-accent transition-colors text-muted-foreground hover:text-foreground">
                                    <i class="fas fa-edit w-4 h-4"></i>
                                </a>
                                <button @click="destroy(page)"
                                        class="p-2 rounded-lg hover:bg-destructive/10 transition-colors text-muted-foreground hover:text-destructive">
                                    <i class="fas fa-trash-alt w-4 h-4"></i>
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-xs text-muted-foreground">
                            <div class="flex items-center space-x-3">
                                <span class="badge badge-outline text-xs" x-text="getTemplateLabel(page.template)"></span>
                                <span class="flex items-center space-x-1">
                                    <template x-if="page.status === 'published'">
                                        <span class="inline-flex items-center rounded-full bg-green-500/10 text-green-600 px-2 py-1 text-xs">
                                            <i class="fas fa-circle w-2 h-2 mr-1"></i>
                                            Publié
                                        </span>
                                    </template>
                                    <template x-if="page.status === 'draft'">
                                        <span class="inline-flex items-center rounded-full bg-amber-500/10 text-amber-600 px-2 py-1 text-xs">
                                            <i class="fas fa-pencil-alt w-2 h-2 mr-1"></i>
                                            Brouillon
                                        </span>
                                    </template>
                                    <template x-if="page.status === 'archived'">
                                        <span class="inline-flex items-center rounded-full bg-gray-500/10 text-gray-600 px-2 py-1 text-xs">
                                            <i class="fas fa-archive w-2 h-2 mr-1"></i>
                                            Archivé
                                        </span>
                                    </template>
                                </span>
                            </div>

                            <div class="flex items-center space-x-2">
                                <template x-if="page.is_home">
                                    <span class="inline-flex items-center rounded-full bg-blue-500/10 text-blue-600 px-2 py-1 text-xs">
                                        <i class="fas fa-home w-3 h-3 mr-1"></i>
                                        Accueil
                                    </span>
                                </template>
                                <span x-text="formatDate(page.created_at)"></span>
                            </div>
                        </div>
                    </div>
                </template>

                <div x-show="filtered.length === 0" class="p-8 text-center">
                    <div class="w-16 h-16 rounded-full bg-muted/50 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-file-alt text-2xl text-muted-foreground"></i>
                    </div>
                    <h3 class="text-lg font-medium text-foreground mb-2">Aucune page trouvée</h3>
                    <p class="text-muted-foreground mb-4" x-text="filters.search || filters.status !== 'all' ? 'Essayez de modifier vos critères de recherche' : 'Commencez par créer votre première page'"></p>
                    <a href="{{ route('admin.pages.create') }}"
                       class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2 text-sm font-medium transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Créer une page
                    </a>
                </div>
            </div>

            <div class="hidden sm:block overflow-auto">
                <table class="w-full">
                    <thead>
                    <tr class="border-b border-border bg-muted/10">
                        <th class="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Titre</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Slug</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Template</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Statut</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Créé le</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                    <template x-for="page in filtered" :key="page.id">
                        <tr class="hover:bg-muted/20 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center space-x-2 min-w-0">
                                    <span class="font-medium text-foreground truncate" x-text="page.title"></span>
                                    <template x-if="page.is_home">
                                            <span class="inline-flex items-center rounded-full bg-blue-500/10 text-blue-600 px-2 py-1 text-xs font-medium shrink-0">
                                                <i class="fas fa-home w-3 h-3 mr-1"></i>
                                                Accueil
                                            </span>
                                    </template>
                                </div>
                            </td>

                            <td class="px-4 py-3">
                                <div class="flex items-center space-x-2 text-muted-foreground font-mono text-sm">
                                    <i class="fas fa-link w-4 h-4"></i>
                                    <span class="truncate" x-text="page.slug"></span>
                                </div>
                            </td>

                            <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full border border-border bg-background px-2.5 py-0.5 text-xs font-medium"
                                          x-text="getTemplateLabel(page.template)"></span>
                            </td>

                            <td class="px-4 py-3">
                                <template x-if="page.status === 'published'">
                                        <span class="inline-flex items-center rounded-full bg-green-500/10 text-green-600 px-2.5 py-0.5 text-xs font-medium">
                                            <i class="fas fa-circle w-2 h-2 mr-1.5"></i>
                                            Publié
                                        </span>
                                </template>
                                <template x-if="page.status === 'draft'">
                                        <span class="inline-flex items-center rounded-full bg-amber-500/10 text-amber-600 px-2.5 py-0.5 text-xs font-medium">
                                            <i class="fas fa-pencil-alt w-2 h-2 mr-1.5"></i>
                                            Brouillon
                                        </span>
                                </template>
                                <template x-if="page.status === 'archived'">
                                        <span class="inline-flex items-center rounded-full bg-gray-500/10 text-gray-600 px-2.5 py-0.5 text-xs font-medium">
                                            <i class="fas fa-archive w-2 h-2 mr-1.5"></i>
                                            Archivé
                                        </span>
                                </template>
                            </td>

                            <td class="px-4 py-3 text-sm text-muted-foreground">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-calendar-alt w-4 h-4"></i>
                                    <span x-text="formatDate(page.created_at)"></span>
                                </div>
                            </td>

                            <td class="px-4 py-3">
                                <div class="flex items-center space-x-1">
                                    <a :href="`/admin/pages/${page.id}/edit`"
                                       class="inline-flex items-center justify-center rounded-lg p-2 text-muted-foreground hover:text-foreground hover:bg-accent transition-colors focus:outline-none focus:ring-2 focus:ring-ring"
                                       title="Modifier">
                                        <i class="fas fa-edit w-4 h-4"></i>
                                    </a>
                                    <button @click="destroy(page)"
                                            class="inline-flex items-center justify-center rounded-lg p-2 text-muted-foreground hover:text-destructive hover:bg-destructive/10 transition-colors focus:outline-none focus:ring-2 focus:ring-ring"
                                            title="Supprimer">
                                        <i class="fas fa-trash-alt w-4 h-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>

                    <tr x-show="filtered.length === 0">
                        <td colspan="6" class="px-4 py-12 text-center">
                            <div class="w-16 h-16 rounded-full bg-muted/50 flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-file-alt text-2xl text-muted-foreground"></i>
                            </div>
                            <h3 class="text-lg font-medium text-foreground mb-2">Aucune page trouvée</h3>
                            <p class="text-muted-foreground mb-4 max-w-sm mx-auto"
                               x-text="filters.search || filters.status !== 'all' ? 'Essayez de modifier vos critères de recherche' : 'Commencez par créer votre première page'"></p>
                            <a href="{{ route('admin.pages.create') }}"
                               class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2 text-sm font-medium transition-colors">
                                <i class="fas fa-plus mr-2"></i>
                                Créer une page
                            </a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div x-show="showDeleteModal"
             x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm transition-opacity duration-300"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">

            <div class="relative w-full max-w-md"
                 @click.outside="closeDeleteModal()"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95">

                <div class="bg-card rounded-xl border border-border shadow-xl overflow-hidden">
                    <div class="p-6 border-b border-border bg-destructive/5">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full bg-destructive/10 flex items-center justify-center">
                                <i class="fas fa-exclamation-triangle text-destructive text-sm"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-foreground">Supprimer la page</h3>
                                <p class="text-sm text-muted-foreground">Action irréversible</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 space-y-4">
                        <div class="flex items-start space-x-3">
                            <div class="w-6 h-6 rounded-full bg-destructive/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="fas fa-file text-destructive text-xs"></i>
                            </div>
                            <div>
                                <p class="text-foreground font-medium" x-text="`« ${pageToDelete?.title || ''} »`"></p>
                                <p class="text-sm text-muted-foreground mt-1">
                                    Êtes-vous sûr de vouloir supprimer cette page ? Cette action ne peut pas être annulée et toutes les données associées seront perdues.
                                </p>
                            </div>
                        </div>

                        <div class="bg-muted/30 rounded-lg p-4 space-y-2">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-muted-foreground">URL :</span>
                                <span class="font-mono text-foreground" x-text="pageToDelete?.slug || ''"></span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-muted-foreground">Statut :</span>
                                <span class="capitalize" x-text="pageToDelete?.status || ''"></span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-muted-foreground">Créée le :</span>
                                <span x-text="pageToDelete ? formatDate(pageToDelete.created_at) : ''"></span>
                            </div>
                        </div>

                        <div class="bg-amber-500/10 border border-amber-500/20 rounded-lg p-4">
                            <div class="flex items-start space-x-2">
                                <i class="fas fa-exclamation-circle text-amber-500 mt-0.5 flex-shrink-0"></i>
                                <p class="text-sm text-amber-700 dark:text-amber-300">
                                    <strong>Attention :</strong> Si cette page est définie comme page d'accueil, vous devrez en définir une autre.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 border-t border-border bg-muted/10 flex flex-col sm:flex-row gap-3">
                        <button @click="closeDeleteModal()"
                                class="flex-1 inline-flex items-center justify-center gap-2 rounded-lg border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2 text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-ring order-2 sm:order-1">
                            <i class="fas fa-times w-4 h-4"></i>
                            Annuler
                        </button>
                        <button @click="confirmDelete()"
                                :disabled="isDeleting"
                                class="flex-1 inline-flex items-center justify-center gap-2 rounded-lg bg-destructive text-destructive-foreground hover:bg-destructive/90 h-10 px-4 py-2 text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-ring disabled:opacity-50 disabled:cursor-not-allowed order-1 sm:order-2"
                                :class="{'opacity-50 cursor-not-allowed': isDeleting}">
                            <i class="fas" :class="isDeleting ? 'fa-spinner fa-spin' : 'fa-trash-alt'"></i>
                            <span x-text="isDeleting ? 'Suppression...' : 'Supprimer'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        window.appData = {
            pages: {!! $pages->values()->toJson() !!},
            templates: {!! json_encode($templates) !!},
            stats: {!! json_encode($stats) !!}
        };
    </script>

    <script>
        function pages() {
            return {
                pages: [],
                templates: [],
                stats: {},
                labels: {
                    total: 'Total Pages',
                    published: 'Publiées',
                    draft: 'Brouillons',
                    homepage: 'Page d\'accueil'
                },
                filters: {
                    search: '',
                    status: 'all'
                },
                showDeleteModal: false,
                pageToDelete: null,
                isDeleting: false,

                init() {
                    this.pages = window.appData.pages;
                    this.templates = window.appData.templates;
                    this.stats = window.appData.stats;

                    this.debouncedSearch = this.debounce((value) => {
                        this.filters.search = value;
                    }, 300);
                },

                get filtered() {
                    let filtered = this.pages;

                    if (this.filters.search) {
                        const searchTerm = this.filters.search.toLowerCase();
                        filtered = filtered.filter(p =>
                            p.title.toLowerCase().includes(searchTerm) ||
                            p.slug.toLowerCase().includes(searchTerm)
                        );
                    }

                    if (this.filters.status !== 'all') {
                        filtered = filtered.filter(p => p.status === this.filters.status);
                    }

                    return filtered;
                },

                getTemplateLabel(val) {
                    const template = this.templates.find(t => t.value === val);
                    return template ? template.label : val;
                },

                formatDate(date) {
                    return new Date(date).toLocaleDateString('fr-FR', {
                        day: 'numeric',
                        month: 'short',
                        year: 'numeric'
                    });
                },

                openDeleteModal(page) {
                    this.pageToDelete = page;
                    this.showDeleteModal = true;
                    document.body.style.overflow = 'hidden';
                },

                closeDeleteModal() {
                    this.showDeleteModal = false;
                    this.pageToDelete = null;
                    this.isDeleting = false;
                    document.body.style.overflow = '';
                },

                async confirmDelete() {
                    if (!this.pageToDelete) return;

                    this.isDeleting = true;

                    try {
                        const response = await fetch(`/admin/pages/${this.pageToDelete.id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            }
                        });

                        if (response.ok) {
                            this.pages = this.pages.filter(p => p.id !== this.pageToDelete.id);
                            this.updateStats();
                            this.showNotification('Page supprimée avec succès', 'success');
                            this.closeDeleteModal();
                        } else {
                            throw new Error('Erreur lors de la suppression');
                        }
                    } catch (error) {
                        this.showNotification('Erreur lors de la suppression', 'error');
                        console.error('Error:', error);
                        this.isDeleting = false;
                    }
                },

                destroy(page) {
                    this.openDeleteModal(page);
                },

                updateStats() {
                    this.stats = {
                        total: this.pages.length,
                        published: this.pages.filter(p => p.status === 'published').length,
                        draft: this.pages.filter(p => p.status === 'draft').length,
                        homepage: this.pages.filter(p => p.is_home).length
                    };
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
                            <span>${message}</span>
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
                },

                debounce(func, wait) {
                    let timeout;
                    return function executedFunction(...args) {
                        const later = () => {
                            clearTimeout(timeout);
                            func(...args);
                        };
                        clearTimeout(timeout);
                        timeout = setTimeout(later, wait);
                    };
                }
            }
        }
    </script>
@endpush

@push('styles')
    <style>
        [x-cloak] { display: none !important; }
    </style>
@endpush
