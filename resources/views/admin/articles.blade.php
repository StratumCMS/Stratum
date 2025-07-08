@extends('admin.layouts.admin')

@section('title', 'Articles')

@section('content')
    <div x-data="articles()" x-init="init()" class="space-y-6">

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <template x-for="(val, key) in stats" :key="key">
                <div class="rounded-lg border bg-card text-card-foreground shadow-sm border-l-4" :class="{
                    'border-l-primary glow-primary': key === 'total',
                    'border-l-success glow-success': key === 'published',
                    'border-l-warning glow-warning': key === 'draft',
                    'border-l-muted-foreground glow-muted': key === 'archived'
                }">
                    <div class="flex flex-col space-y-1.5 p-6 pb-2">
                        <p class="text-sm font-medium text-muted-foreground" x-text="labels[key]"></p>
                        <h3 class="text-2xl font-bold" x-text="val"></h3>
                    </div>
                </div>
            </template>
        </div>

        {{-- Filtres + Bouton --}}
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm flex flex-col md:flex-row gap-4 items-center px-6 py-4">
            <div class="flex-1">
                <i class="fas fa-search absolute top-3 left-3 text-muted-foreground"></i>
                <input type="text" x-model="filters.search" placeholder="Rechercher un article..." class="w-full h-10 pl-10 pr-4 rounded-md border border-input bg-background text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring" />
            </div>

            <select x-model="filters.status" class="h-10 px-3 rounded-md border border-input bg-background text-sm md:w-48">
                <option value="">Tous statuts</option>
                <option value="published">Publié</option>
                <option value="draft">Brouillon</option>
                <option value="archived">Archivé</option>
            </select>

            <select x-model="filters.type" class="h-10 px-3 rounded-md border border-input bg-background text-sm md:w-48">
                <option value="">Toutes catégories</option>
                @foreach($availableTypes as $type)
                    <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                @endforeach
            </select>

            <a href="{{ route('admin.articles.create') }}" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-3">
                <i class="fas fa-plus"></i> Créer
            </a>
        </div>

        <div class="rounded-lg border bg-card text-card-foreground shadow-sm overflow-auto">
            <table class="w-full table-auto text-sm divide-y divide-muted">
                <thead class="bg-muted/10 text-muted-foreground">
                <tr>
                    <th class="px-4 py-2 text-left">Titre</th>
                    <th class="px-4 py-2 text-left">Catégorie</th>
                    <th class="px-4 py-2 text-left">Statut</th>
                    <th class="px-4 py-2 text-left">Auteur</th>
                    <th class="px-4 py-2 text-left">Créé le</th>
                    <th class="px-4 py-2 text-left">Actions</th>
                </tr>
                </thead>
                <tbody>
                <template x-for="article in filtered" :key="article.id">
                    <tr class="hover:bg-muted/20">
                        <td class="flex items-center gap-3">
                            <img
                                :src=article.thumbnail
                                alt={article.title}
                                class="w-12 h-8 object-cover rounded"
                            />
                            <div>
                                <span class="font-medium text-foreground" x-text="article.title"></span>
                                <div class="text-sm text-muted-foreground line-clamp-1" x-text="truncate(article.description, 60)"></div>
                            </div>
                        </td>
                        <td class="px-4 py-2">
                            <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 text-foreground" x-text="article.type || '—'"></span>
                        </td>
                        <td class="px-4 py-2">
                            <template x-if="article.is_published">
                                <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 bg-success/10 text-success border-success/20">Publié</span>
                            </template>
                            <template x-if="article.archived">
                                <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 bg-muted text-muted-foreground border-muted-foreground/20">Archivé</span>
                            </template>
                            <template x-if="!article.is_published && !article.archived">
                                <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 bg-warning/10 text-warning border-warning/20">Brouillon</span>
                            </template>
                        </td>
                        <td class="px-4 py-2">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-user h-4 w-4 text-muted-foreground"></i>
                                <span x-text="article.author?.name || '—'"></span>
                            </div>
                        </td>
                        <td class="px-4 py-2">
                            <div class="flex items-center gap-2 text-sm text-muted-foreground">
                                <i class="fas fa-calendar h-4 w-4"></i>
                                <span x-text="formatDate(article.created_at)"></span>
                            </div>
                        </td>
                        <td class="px-4 py-2">
                            <div class="flex items-center gap-1">
                                <a :href="`/articles/${article.id}`" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-3 hover-glow-purple"><i class="fas fa-eye"></i></a>
                                <form :action="`/admin/articles/${article.id}/toggle`" method="POST" @submit.prevent="togglePublish(article)">
                                    @csrf
                                    <button class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-3 hover-glow-purple" type="submit" x-text="article.is_published ? 'Dépublier' : 'Publier'"></button>
                                </form>
                                <a :href="`/admin/articles/${article.id}/edit`" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-3 hover-glow-purple"><i class="fa-regular fa-pen-to-square"></i></a>
                                <form :action="`/admin/articles/${article.id}`" method="POST" @submit.prevent="destroy(article)">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 border border-input bg-background h-9 px-3 hover:bg-destructive/10 hover:text-destructive"><i class="fas fa-trash h-4 w-4"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                </template>
                <tr x-show="filtered.length === 0">
                    <td colspan="6" class="py-8 text-center text-muted-foreground">Aucun article trouvé</td>
                </tr>
                </tbody>
            </table>
        </div>

    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/alpinejs" defer></script>
    <script>
        function articles() {
            return {
                articles: {!! $articles->values()->toJson() !!},
                stats: {!! json_encode($stats) !!},
                filters: { search: '', status: '', type: '' },
                labels: {
                    total: 'Total Articles',
                    published: 'Publiés',
                    draft: 'Brouillons',
                    archived: 'Archivés'
                },
                init() {},
                get filtered() {
                    return this.articles.filter(a =>
                        (a.title.toLowerCase().includes(this.filters.search.toLowerCase()) ||
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
                        day: 'numeric', month: 'short', year: 'numeric'
                    });
                },
                truncate(text, length) {
                    return text?.length > length ? text.substring(0, length) + '...' : text;
                },
                async destroy(article) {
                    if (confirm('Supprimer cet article ?')) {
                        const res = await fetch(`/admin/articles/${article.id}`, {
                            method: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                        });
                        if (res.ok) this.articles = this.articles.filter(a => a.id !== article.id);
                    }
                },
                async togglePublish(article) {
                    const res = await fetch(`/admin/articles/${article.id}/toggle`, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    });
                    if (res.ok) {
                        article.is_published = !article.is_published;
                        article.archived = false;
                    }
                }
            }
        }
    </script>
@endpush
