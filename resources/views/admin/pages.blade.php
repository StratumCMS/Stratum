@extends('admin.layouts.admin')

@section('title', 'Gestion des pages')

@section('content')
    <div x-data="pages()" x-init="init()" class="space-y-6">

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <template x-for="(val, key) in stats" :key="key">
                <div class="rounded-lg border bg-card text-card-foreground shadow-sm border-l-4 border-l-primary glow-purple" :class="{
                    'border-l-primary glow-primary': key === 'total',
                    'border-l-success glow-success': key === 'published',
                    'border-l-warning glow-warning': key === 'draft',
                    'border-l-blue-500 glow-blue-500': key === 'homepage'
                }">
                    <div class="flex flex-col space-y-1.5 p-6 pb-2">
                        <p class="text-sm font-medium text-muted-foreground" x-text="labels[key]"></p>
                        <h3 class="text-2xl font-bold" :class="key !== 'total' ? 'text-' + colors[key] : ''" x-text="val || 'Aucune'"></h3>
                    </div>
                </div>
            </template>
        </div>

        <div class="rounded-lg border bg-card text-card-foreground shadow-sm flex flex-col md:flex-row gap-4 items-center">
            <div class="flex flex-col space-y-1.5 p-6">
                <div class="text-2xl font-semibold leading-none tracking-tight">
                    <i class="fa-regular fa-file h-5 w-5"></i> Filtres
                </div>
            </div>


            <div class="flex-1">
                <i class="fas fa-search absolute top-3 left-3 text-muted-foreground"></i>
                <input type="text" x-model="filters.search" placeholder="Rechercher une page..." class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm" />
            </div>

            <select x-model="filters.status" class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 [&>span]:line-clamp-1 md:w-48">
                <option value="all">Tous statuts</option>
                <option value="published">Publié</option>
                <option value="draft">Brouillon</option>
                <option value="archived">Archivé</option>
            </select>

            <a href="{{ route('admin.pages.create') }}" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 h-9 px-3 border border-input bg-background hover:bg-accent hover:text-accent-foreground">
                <i class="fas fa-plus mr-2"></i> Nouvelle page
            </a>
        </div>

        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
            <div class="flex flex-col space-y-1.5 p-6">
                <h2 class="text-2xl font-semibold leading-none tracking-tight">Pages (<span x-text="filtered.length"></span>)</h2>
            </div>

            <div class="overflow-auto">
                <table class="w-full table-auto text-sm">
                    <thead>
                    <tr class="bg-muted/10 text-muted-foreground">
                        <th class="px-4 py-2 text-left">Titre</th>
                        <th class="px-4 py-2 text-left">Slug</th>
                        <th class="px-4 py-2 text-left">Template</th>
                        <th class="px-4 py-2 text-left">Statut</th>
                        <th class="px-4 py-2 text-left">Créé le</th>
                        <th class="px-4 py-2 text-left">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <template x-for="page in filtered" :key="page.id">
                        <tr class="hover:bg-muted/20">
                            <td class="px-4 py-2 flex items-center gap-2">
                                <span x-text="page.title"></span>
                                <span x-show="page.is_home" class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 bg-blue-50 text-blue-600 border-blue-200">Accueil</span>
                            </td>
                            <td class="px-4 py-2 font-mono text-muted-foreground">
                                <i class="fas fa-globe"></i>
                                <span x-text="page.slug"></span>
                            </td>
                            <td class="px-4 py-2">
                                <span class="badge badge-outline" x-text="getTemplateLabel(page.template)"></span>
                            </td>
                            <td class="px-4 py-2">
                                <template x-if="page.status === 'published'">
                                    <span class="badge badge-success">Publié</span>
                                </template>
                                <template x-if="page.status === 'draft'">
                                    <span class="badge badge-warning">Brouillon</span>
                                </template>
                                <template x-if="page.status === 'archived'">
                                    <span class="badge badge-muted">Archivé</span>
                                </template>
                            </td>
                            <td class="px-4 py-2 text-muted-foreground">
                                <i class="fas fa-calendar-alt"></i>
                                <span x-text="formatDate(page.created_at)"></span>
                            </td>
                            <td class="px-4 py-2 flex gap-2">
                                <a :href="`/admin/pages/${page.id}/edit`" class="btn btn-ghost btn-sm hover-glow-purple">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form :action="`/admin/pages/${page.id}`" method="POST" @submit.prevent="destroy(page)">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-ghost btn-sm text-destructive hover:bg-destructive/10">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    </template>

                    <tr x-show="filtered.length === 0">
                        <td colspan="6" class="py-8 text-center text-muted-foreground">
                            <i class="fas fa-file-alt fa-2x mb-4 opacity-50"></i>
                            <div>Aucune page trouvée</div>
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
        window.appData = {
            pages: {!! $pages->values()->toJson() !!},
            templates: {!! json_encode($templates) !!},
            stats: {!! json_encode($stats) !!}
        };
    </script>
    <script src="https://unpkg.com/alpinejs" defer></script>

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
                colors: {
                    total: 'primary',
                    published: 'success',
                    draft: 'warning',
                    homepage: 'blue-500'
                },
                filters: {
                    search: '',
                    status: 'all'
                },
                init() {
                    this.pages = window.appData.pages;
                    this.templates = window.appData.templates;
                    this.stats = window.appData.stats;
                },
                get filtered() {
                    return this.pages.filter(p =>
                        (p.title.toLowerCase().includes(this.filters.search.toLowerCase()) ||
                            p.slug.toLowerCase().includes(this.filters.search.toLowerCase())) &&
                        (this.filters.status === 'all' || p.status === this.filters.status)
                    );
                },
                getTemplateLabel(val) {
                    const t = this.templates.find(x => x.value === val);
                    return t ? t.label : val;
                },
                formatDate(date) {
                    return new Date(date).toLocaleDateString('fr-FR', {
                        day: 'numeric',
                        month: 'short',
                        year: 'numeric'
                    });
                },
                async destroy(page) {
                    if (confirm('Confirmer la suppression de cette page ?')) {
                        const res = await fetch(`/admin/pages/${page.id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });
                        if (res.ok) {
                            this.pages = this.pages.filter(p => p.id !== page.id);
                        }
                    }
                }
            }
        }
    </script>
@endpush
