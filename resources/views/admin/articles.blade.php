@extends('admin.layouts.admin')

@section('title', 'Articles')

@section('content')
    <div x-data="articles()" x-init="init()" class="space-y-4 sm:space-y-6">

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
                        Cette action est irréversible et supprimera définitivement l'article.
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
                    <i class="fas fa-newspaper text-primary text-sm"></i>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-semibold text-foreground">Gestion des Articles</h1>
                    <p class="text-sm text-muted-foreground hidden sm:block">
                        Gérez et organisez vos articles
                    </p>
                </div>
            </div>

            <a href="{{ route('admin.articles.create') }}"
               class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2 text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-ring w-full sm:w-auto">
                <i class="fas fa-plus w-4 h-4"></i>
                <span>Nouvel article</span>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <div class="rounded-xl border bg-card text-card-foreground shadow-sm p-4 sm:p-6 border-l-4 border-l-primary">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs sm:text-sm font-medium text-muted-foreground mb-1">Total Articles</p>
                        <h3 class="text-xl sm:text-2xl font-bold text-foreground" x-text="stats.total"></h3>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
                        <i class="fas fa-newspaper text-primary text-sm"></i>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border bg-card text-card-foreground shadow-sm p-4 sm:p-6 border-l-4 border-l-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs sm:text-sm font-medium text-muted-foreground mb-1">Publiés</p>
                        <h3 class="text-xl sm:text-2xl font-bold text-foreground" x-text="stats.published"></h3>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-green-500/10 flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-500 text-sm"></i>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border bg-card text-card-foreground shadow-sm p-4 sm:p-6 border-l-4 border-l-amber-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs sm:text-sm font-medium text-muted-foreground mb-1">Brouillons</p>
                        <h3 class="text-xl sm:text-2xl font-bold text-foreground" x-text="stats.draft"></h3>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-amber-500/10 flex items-center justify-center">
                        <i class="fas fa-edit text-amber-500 text-sm"></i>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border bg-card text-card-foreground shadow-sm p-4 sm:p-6 border-l-4 border-l-gray-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs sm:text-sm font-medium text-muted-foreground mb-1">Archivés</p>
                        <h3 class="text-xl sm:text-2xl font-bold text-foreground" x-text="stats.archived"></h3>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-gray-500/10 flex items-center justify-center">
                        <i class="fas fa-archive text-gray-500 text-sm"></i>
                    </div>
                </div>
            </div>
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
                           x-model="filters.search"
                           placeholder="Rechercher un article..."
                           class="flex h-10 w-full rounded-lg border border-input bg-background pl-10 pr-4 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 transition-colors">
                </div>

                <div class="relative w-full sm:w-48">
                    <select x-model="filters.status"
                            class="flex h-10 w-full rounded-lg border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 appearance-none">
                        <option value="">Tous statuts</option>
                        <option value="published">Publié</option>
                        <option value="draft">Brouillon</option>
                        <option value="archived">Archivé</option>
                    </select>
                    <i class="fas fa-chevron-down absolute right-3 top-1/2 transform -translate-y-1/2 text-muted-foreground pointer-events-none"></i>
                </div>

                <div class="relative w-full sm:w-48">
                    <select x-model="filters.type"
                            class="flex h-10 w-full rounded-lg border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 appearance-none">
                        <option value="">Toutes catégories</option>
                        @foreach($availableTypes as $type)
                            <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                        @endforeach
                    </select>
                    <i class="fas fa-chevron-down absolute right-3 top-1/2 transform -translate-y-1/2 text-muted-foreground pointer-events-none"></i>
                </div>

                <button x-show="filters.search || filters.status || filters.type"
                        @click="filters.search = ''; filters.status = ''; filters.type = ''"
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
                        <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center">
                            <i class="fas fa-list text-primary text-sm"></i>
                        </div>
                        <h2 class="text-lg sm:text-xl font-semibold text-foreground">Articles</h2>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-muted-foreground" x-text="`${filtered.length} article(s)`"></span>
                        <button x-show="filters.search || filters.status || filters.type"
                                @click="filters.search = ''; filters.status = ''; filters.type = ''"
                                class="hidden sm:inline-flex items-center justify-center gap-2 rounded-lg border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-3 text-sm font-medium transition-colors">
                            <i class="fas fa-times mr-1"></i>
                            Réinitialiser
                        </button>
                    </div>
                </div>
            </div>

            <div class="block sm:hidden">
                <div class="divide-y divide-border">
                    <template x-for="article in filtered" :key="article.id">
                        <div class="p-4 hover:bg-muted/20 transition-colors">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-medium text-foreground truncate mb-1" x-text="article.title || 'Sans titre'"></h3>
                                    <p class="text-sm text-muted-foreground line-clamp-2" x-text="truncate(article.description, 80)"></p>
                                </div>
                                <img :src="article.thumbnail || 'https://placehold.co/400x300?text=Sans+image'"
                                     :alt="article.title"
                                     class="w-16 h-12 object-cover rounded-lg ml-3 flex-shrink-0">
                            </div>

                            <div class="flex items-center justify-between text-xs text-muted-foreground mb-3">
                                <div class="flex items-center space-x-3">
                                    <span class="inline-flex items-center rounded-full border border-border bg-background px-2.5 py-0.5 text-xs font-medium"
                                          x-text="article.type || '—'"></span>

                                    <template x-if="article.is_published">
                                        <span class="inline-flex items-center rounded-full bg-green-500/10 text-green-600 px-2 py-1 text-xs font-medium">
                                            <i class="fas fa-check-circle mr-1 w-3 h-3"></i>
                                            Publié
                                        </span>
                                    </template>
                                    <template x-if="article.archived">
                                        <span class="inline-flex items-center rounded-full bg-gray-500/10 text-gray-600 px-2 py-1 text-xs font-medium">
                                            <i class="fas fa-archive mr-1 w-3 h-3"></i>
                                            Archivé
                                        </span>
                                    </template>
                                    <template x-if="!article.is_published && !article.archived">
                                        <span class="inline-flex items-center rounded-full bg-amber-500/10 text-amber-600 px-2 py-1 text-xs font-medium">
                                            <i class="fas fa-edit mr-1 w-3 h-3"></i>
                                            Brouillon
                                        </span>
                                    </template>
                                </div>
                            </div>

                            <div class="flex items-center justify-between text-xs text-muted-foreground mb-4">
                                <div class="flex items-center space-x-4">
                                    <span class="flex items-center space-x-1">
                                        <i class="fas fa-user w-3 h-3"></i>
                                        <span x-text="article.author?.name || '—'"></span>
                                    </span>
                                    <span class="flex items-center space-x-1">
                                        <i class="fas fa-calendar w-3 h-3"></i>
                                        <span x-text="formatDate(article.created_at)"></span>
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-1">
                                    <a :href="`/articles/${article.id}`"
                                       target="_blank"
                                       class="inline-flex items-center justify-center rounded-lg p-2 text-muted-foreground hover:text-foreground hover:bg-accent transition-colors"
                                       title="Voir">
                                        <i class="fas fa-eye w-4 h-4"></i>
                                    </a>
                                    <a :href="`/admin/articles/${article.id}/edit`"
                                       class="inline-flex items-center justify-center rounded-lg p-2 text-muted-foreground hover:text-foreground hover:bg-accent transition-colors"
                                       title="Modifier">
                                        <i class="fas fa-edit w-4 h-4"></i>
                                    </a>
                                </div>
                                <div class="flex items-center space-x-1">
                                    <form :action="`/admin/articles/${article.id}/toggle`" method="POST" @submit.prevent="togglePublish(article)">
                                        @csrf
                                        <button type="submit"
                                                class="inline-flex items-center justify-center rounded-lg border border-input bg-background hover:bg-accent hover:text-accent-foreground h-8 px-3 text-xs font-medium transition-colors"
                                                x-text="article.is_published ? 'Dépublier' : 'Publier'"></button>
                                    </form>
                                    <button @click="openDeleteModal(article.id, article.title)"
                                            class="inline-flex items-center justify-center rounded-lg p-2 text-muted-foreground hover:text-destructive hover:bg-destructive/10 transition-colors"
                                            title="Supprimer">
                                        <i class="fas fa-trash-alt w-4 h-4"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>

                    <div x-show="filtered.length === 0" class="p-8 text-center">
                        <div class="w-16 h-16 rounded-full bg-muted/50 flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-newspaper text-2xl text-muted-foreground"></i>
                        </div>
                        <h3 class="text-lg font-medium text-foreground mb-2">Aucun article trouvé</h3>
                        <p class="text-muted-foreground mb-4" x-text="filters.search || filters.status || filters.type ? 'Essayez de modifier vos critères de recherche' : 'Commencez par créer votre premier article'"></p>
                        <a href="{{ route('admin.articles.create') }}"
                           class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2 text-sm font-medium transition-colors">
                            <i class="fas fa-plus mr-2"></i>
                            Créer un article
                        </a>
                    </div>
                </div>
            </div>

            <div class="hidden sm:block overflow-auto">
                <table class="w-full">
                    <thead>
                    <tr class="border-b border-border bg-muted/10">
                        <th class="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Article</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Catégorie</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Statut</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Auteur</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Créé le</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider w-32">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                    <template x-for="article in filtered" :key="article.id">
                        <tr class="hover:bg-muted/20 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center space-x-3 min-w-0">
                                    <img :src="article.thumbnail || 'https://placehold.co/400x300?text=Sans+image'"
                                         :alt="article.title"
                                         class="w-12 h-9 object-cover rounded-lg flex-shrink-0">
                                    <div class="min-w-0 flex-1">
                                        <h4 class="font-medium text-foreground truncate" x-text="article.title || 'Sans titre'"></h4>
                                        <p class="text-sm text-muted-foreground truncate" x-text="truncate(article.description, 60)"></p>
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full border border-border bg-background px-2.5 py-0.5 text-xs font-medium"
                                          x-text="article.type || '—'"></span>
                            </td>

                            <td class="px-4 py-3">
                                <template x-if="article.is_published">
                                        <span class="inline-flex items-center rounded-full bg-green-500/10 text-green-600 px-2.5 py-0.5 text-xs font-medium">
                                            <i class="fas fa-check-circle mr-1.5 w-3 h-3"></i>
                                            Publié
                                        </span>
                                </template>
                                <template x-if="article.archived">
                                        <span class="inline-flex items-center rounded-full bg-gray-500/10 text-gray-600 px-2.5 py-0.5 text-xs font-medium">
                                            <i class="fas fa-archive mr-1.5 w-3 h-3"></i>
                                            Archivé
                                        </span>
                                </template>
                                <template x-if="!article.is_published && !article.archived">
                                        <span class="inline-flex items-center rounded-full bg-amber-500/10 text-amber-600 px-2.5 py-0.5 text-xs font-medium">
                                            <i class="fas fa-edit mr-1.5 w-3 h-3"></i>
                                            Brouillon
                                        </span>
                                </template>
                            </td>

                            <td class="px-4 py-3">
                                <div class="flex items-center space-x-2 text-sm text-muted-foreground">
                                    <i class="fas fa-user w-4 h-4"></i>
                                    <span x-text="article.author?.name || '—'"></span>
                                </div>
                            </td>

                            <td class="px-4 py-3">
                                <div class="flex items-center space-x-2 text-sm text-muted-foreground">
                                    <i class="fas fa-calendar w-4 h-4"></i>
                                    <span x-text="formatDate(article.created_at)"></span>
                                </div>
                            </td>

                            <td class="px-4 py-3">
                                <div class="flex items-center space-x-1">
                                    <a :href="`/articles/${article.id}`"
                                       target="_blank"
                                       class="inline-flex items-center justify-center rounded-lg p-2 text-muted-foreground hover:text-foreground hover:bg-accent transition-colors focus:outline-none focus:ring-2 focus:ring-ring"
                                       title="Voir l'article">
                                        <i class="fas fa-eye w-4 h-4"></i>
                                    </a>
                                    <form :action="`/admin/articles/${article.id}/toggle`" method="POST" @submit.prevent="togglePublish(article)">
                                        @csrf
                                        <button type="submit"
                                                class="inline-flex items-center justify-center rounded-lg p-2 text-muted-foreground hover:text-foreground hover:bg-accent transition-colors focus:outline-none focus:ring-2 focus:ring-ring"
                                                :title="article.is_published ? 'Dépublier' : 'Publier'">
                                            <i class="fas" :class="article.is_published ? 'fa-eye-slash' : 'fa-eye'"></i>
                                        </button>
                                    </form>
                                    <a :href="`/admin/articles/${article.id}/edit`"
                                       class="inline-flex items-center justify-center rounded-lg p-2 text-muted-foreground hover:text-foreground hover:bg-accent transition-colors focus:outline-none focus:ring-2 focus:ring-ring"
                                       title="Modifier">
                                        <i class="fas fa-edit w-4 h-4"></i>
                                    </a>
                                    <button @click="openDeleteModal(article.id, article.title)"
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
                                <i class="fas fa-newspaper text-2xl text-muted-foreground"></i>
                            </div>
                            <h3 class="text-lg font-medium text-foreground mb-2">Aucun article trouvé</h3>
                            <p class="text-muted-foreground mb-4 max-w-sm mx-auto"
                               x-text="filters.search || filters.status || filters.type ? 'Essayez de modifier vos critères de recherche' : 'Commencez par créer votre premier article'"></p>
                            <a href="{{ route('admin.articles.create') }}"
                               class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2 text-sm font-medium transition-colors">
                                <i class="fas fa-plus mr-2"></i>
                                Créer un article
                            </a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        function articles() {
            return {
                articles: {!! $articles->map(function($a) {
                    return [
                        'id' => $a->id,
                        'title' => $a->title,
                        'description' => $a->description,
                        'thumbnail' => $a->thumbnail,
                        'type' => $a->type,
                        'is_published' => $a->is_published,
                        'archived' => $a->archived,
                        'author' => ['name' => $a->author->name ?? null],
                        'created_at' => $a->created_at,
                    ];
                })->toJson() !!},
                stats: {!! json_encode($stats) !!},
                filters: {
                    search: '',
                    status: '',
                    type: ''
                },
                showDeleteModal: false,
                isDeleting: false,
                deleteItemId: null,
                deleteItemName: '',

                init() {
                },

                get filtered() {
                    return this.articles.filter(a =>
                        (a.title?.toLowerCase().includes(this.filters.search.toLowerCase()) ||
                            a.description?.toLowerCase().includes(this.filters.search.toLowerCase())) &&
                        (!this.filters.status || this.filters.status === this.statusOf(a)) &&
                        (!this.filters.type || a.type === this.filters.type)
                    );
                },

                statusOf(article) {
                    if (article.archived) return 'archived';
                    if (article.is_published) return 'published';
                    return 'draft';
                },

                formatDate(date) {
                    return new Date(date).toLocaleDateString('fr-FR', {
                        day: 'numeric',
                        month: 'short',
                        year: 'numeric'
                    });
                },

                truncate(text, length) {
                    if (!text) return '';
                    return text.length > length ? text.substring(0, length) + '...' : text;
                },

                openDeleteModal(itemId, itemName) {
                    this.deleteItemId = itemId;
                    this.deleteItemName = itemName || 'Sans titre';
                    this.showDeleteModal = true;
                    this.isDeleting = false;
                },

                async confirmDelete() {
                    if (!this.deleteItemId) return;

                    this.isDeleting = true;

                    try {
                        const response = await fetch(`/admin/articles/${this.deleteItemId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });

                        if (response.ok) {
                            this.articles = this.articles.filter(a => a.id !== this.deleteItemId);
                            this.showNotification('Article supprimé avec succès', 'success');
                        } else {
                            throw new Error('Erreur lors de la suppression');
                        }
                    } catch (error) {
                        console.error('Error deleting article:', error);
                        this.showNotification('Erreur lors de la suppression', 'error');
                    } finally {
                        this.isDeleting = false;
                        this.showDeleteModal = false;
                        this.deleteItemId = null;
                        this.deleteItemName = '';
                    }
                },

                async togglePublish(article) {
                    try {
                        const response = await fetch(`/admin/articles/${article.id}/toggle`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });

                        if (response.ok) {
                            article.is_published = !article.is_published;
                            article.archived = false;

                            const action = article.is_published ? 'publié' : 'dépublié';
                            this.showNotification(`Article ${action} avec succès`, 'success');
                        } else {
                            throw new Error('Erreur lors de la modification');
                        }
                    } catch (error) {
                        console.error('Error toggling publish:', error);
                        this.showNotification('Erreur lors de la modification', 'error');
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
