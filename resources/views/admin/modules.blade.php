@extends('admin.layouts.admin')

@section('title', 'Modules')

@section('content')
    <div x-data="{
        tab: 'installed',
        search: ''
    }" class="space-y-6">

        <div class="mb-6">
            <div class="flex flex-col sm:flex-row gap-4 justify-between items-start sm:items-center">
                <div>
                    <h1 class="text-2xl font-semibold sm:text-3xl">Gestion des Modules</h1>
                    <p class="text-sm text-muted-foreground mt-1">Gérez les modules installés et découvrez-en de nouveaux</p>
                </div>

                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full sm:w-auto justify-end sm:justify-start">

                    <div class="relative w-full sm:w-64">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground w-4 h-4 pointer-events-none"></i>
                        <input
                            type="text"
                            x-model="search"
                            placeholder="Rechercher..."
                            class="w-full pl-10 pr-4 py-2 bg-background border border-border rounded-lg text-sm
                           focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>

                    <form action="{{ route('modules.scan') }}" method="POST">
                        @csrf
                        <button
                            type="submit"
                            title="Actualiser les modules"
                            aria-label="Actualiser les modules"
                            class="inline-flex items-center justify-center gap-2 rounded-md border border-input bg-background
               hover:bg-accent hover:text-accent-foreground transition h-10 px-3
               focus:outline-none focus:ring-2 focus:ring-ring text-sm font-medium">
                            <i class="fa-solid fa-rotate-right w-4 h-4"></i>
                        </button>
                    </form>

                </div>
            </div>
        </div>

    @if (session('success'))
            <div class="mb-6 rounded-xl border-l-4 border-green-500 bg-green-50 p-4 text-sm text-green-800 dark:bg-green-900/20 dark:text-green-400">
                <div class="flex items-start">
                    <i class="fa-solid fa-circle-check mr-3 mt-0.5"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 rounded-xl border-l-4 border-red-500 bg-red-50 p-4 text-sm text-red-800 dark:bg-red-900/20 dark:text-red-400">
                <div class="flex items-start">
                    <i class="fa-solid fa-circle-exclamation mr-3 mt-0.5"></i>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <div class="border-b border-border">
            <div class="flex gap-2 overflow-x-auto -mx-6 px-6 sm:mx-0 sm:px-0">
                <button x-on:click="tab = 'installed'; search = ''"
                        :class="tab === 'installed'
                            ? 'border-primary text-primary'
                            : 'border-transparent text-muted-foreground hover:text-foreground hover:border-border'"
                        class="inline-flex items-center gap-2 px-4 py-3 border-b-2 font-medium text-sm transition-colors whitespace-nowrap">
                    <i class="fa-solid fa-box"></i>
                    <span>Installés</span>
                    <span class="inline-flex items-center justify-center min-w-[20px] h-5 px-2 text-xs font-semibold rounded-full bg-primary/10 text-primary">
                        {{ $modules->count() }}
                    </span>
                </button>

                <button x-on:click="tab = 'marketplace'; search = ''"
                        :class="tab === 'marketplace'
                            ? 'border-primary text-primary'
                            : 'border-transparent text-muted-foreground hover:text-foreground hover:border-border'"
                        class="inline-flex items-center gap-2 px-4 py-3 border-b-2 font-medium text-sm transition-colors whitespace-nowrap">
                    <i class="fa-solid fa-store"></i>
                    <span>Marketplace</span>
                    <span class="inline-flex items-center justify-center min-w-[20px] h-5 px-2 text-xs font-semibold rounded-full bg-primary/10 text-primary">
                        {{ $marketModules->count() }}
                    </span>
                </button>
            </div>
        </div>

        <template x-if="tab === 'installed'">
            <div x-data="{
                page: 1,
                perPage: 9,
                modules: {{ $modules->toJson() }},
                get filteredModules() {
                    if (!this.search) return this.modules;
                    const searchTerm = this.search.toLowerCase();
                    return this.modules.filter(m =>
                        m.name.toLowerCase().includes(searchTerm) ||
                        (m.description && m.description.toLowerCase().includes(searchTerm)) ||
                        (m.author && m.author.toLowerCase().includes(searchTerm))
                    );
                },
                get total() { return this.filteredModules.length; },
                get pages() { return Math.ceil(this.total / this.perPage); },
                get paginated() {
                    return this.filteredModules.slice((this.page - 1) * this.perPage, this.page * this.perPage);
                }
            }" class="space-y-4">

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <template x-for="module in paginated" :key="module.id">
                        <div class="overflow-hidden rounded-xl border border-border bg-card shadow-sm hover:shadow-md transition-all flex flex-col">
                            <div class="p-4 flex-1 flex flex-col">
                                <div class="flex items-start gap-3 mb-3">
                                    <div class="flex-shrink-0 w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center border border-primary/20">
                                        <i class="fa-solid fa-puzzle-piece text-primary text-xl"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-semibold text-base leading-tight mb-1" x-text="module.name"></h4>

                                        <div class="flex flex-wrap items-center gap-2 text-xs text-muted-foreground">
                                            <span class="inline-flex items-center gap-1">
                                                <i class="fa-solid fa-tag text-[10px]"></i>
                                                <span x-text="`v${module.version}`"></span>
                                            </span>
                                            <span x-show="module.author" class="inline-flex items-center gap-1">
                                                <i class="fa-solid fa-user text-[10px]"></i>
                                                <span x-text="module.author"></span>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="flex flex-col gap-1 items-end flex-shrink-0">
                                        <span x-show="module.active"
                                              class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-semibold bg-green-500 text-white">
                                            <i class="fa-solid fa-circle-check text-[10px]"></i> Actif
                                        </span>
                                        <span x-show="!module.active"
                                              class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-semibold bg-gray-500 text-white">
                                            <i class="fa-solid fa-circle-xmark text-[10px]"></i> Inactif
                                        </span>

                                        @php
                                            $marketModulesArray = $marketModules->toArray();
                                        @endphp
                                        <template x-if="(() => {
                                            const market = {{ json_encode($marketModulesArray) }}.find(m => m.slug === module.slug);
                                            if (!market || !market.version) return false;
                                            const current = module.version.split('.').map(Number);
                                            const latest = market.version.split('.').map(Number);
                                            for (let i = 0; i < Math.max(current.length, latest.length); i++) {
                                                if ((latest[i] || 0) > (current[i] || 0)) return true;
                                                if ((latest[i] || 0) < (current[i] || 0)) return false;
                                            }
                                            return false;
                                        })()">
                                            <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-semibold bg-blue-500 text-white">
                                                <i class="fa-solid fa-arrow-up text-[10px]"></i> MAJ
                                            </span>
                                        </template>
                                    </div>
                                </div>

                                <p class="text-sm text-muted-foreground mb-4 line-clamp-2 flex-1"
                                   x-text="module.description || 'Aucune description disponible'"></p>

                                <div class="flex items-center justify-between mb-4 pb-4 border-t border-border pt-4">
                                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-semibold bg-primary/10 text-primary border border-primary/20">
                                        <i class="fa-solid fa-puzzle-piece text-[10px]"></i> Module
                                    </span>
                                </div>

                                <div class="flex gap-2">
                                    <form :action="`{{ url('admin/modules') }}/${module.active ? 'deactivate' : 'activate'}/${module.slug}`"
                                          method="POST"
                                          class="flex-1">
                                        @csrf
                                        <button type="submit"
                                                class="w-full inline-flex items-center justify-center gap-2 rounded-lg px-3 py-2 text-sm font-medium transition-colors"
                                                :class="module.active
                                                    ? 'bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 dark:bg-red-900/20 dark:text-red-400 dark:border-red-800 dark:hover:bg-red-900/30'
                                                    : 'bg-green-50 text-green-700 border border-green-200 hover:bg-green-100 dark:bg-green-900/20 dark:text-green-400 dark:border-green-800 dark:hover:bg-green-900/30'">
                                            <i :class="module.active ? 'fa-solid fa-power-off' : 'fa-solid fa-circle-check'"></i>
                                            <span x-text="module.active ? 'Désactiver' : 'Activer'"></span>
                                        </button>
                                    </form>

                                    <template x-if="(() => {
                                        const market = {{ json_encode($marketModulesArray) }}.find(m => m.slug === module.slug);
                                        if (!market || !market.version) return false;
                                        const current = module.version.split('.').map(Number);
                                        const latest = market.version.split('.').map(Number);
                                        for (let i = 0; i < Math.max(current.length, latest.length); i++) {
                                            if ((latest[i] || 0) > (current[i] || 0)) return true;
                                            if ((latest[i] || 0) < (current[i] || 0)) return false;
                                        }
                                        return false;
                                    })()">
                                        <form :action="`{{ url('admin/modules/update') }}/${module.slug}`" method="POST">
                                            @csrf
                                            <button type="submit"
                                                    class="inline-flex items-center justify-center gap-2 rounded-lg px-3 py-2 text-sm font-medium bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100 transition-colors dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-800 dark:hover:bg-blue-900/30 whitespace-nowrap">
                                                <i class="fa-solid fa-download"></i>
                                                <span>MAJ</span>
                                            </button>
                                        </form>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <div x-show="filteredModules.length === 0 && search !== ''" class="rounded-xl border border-border bg-card p-12 text-center shadow-sm">
                    <div class="flex flex-col items-center">
                        <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-muted/50">
                            <i class="fa-solid fa-magnifying-glass text-3xl text-muted-foreground/50"></i>
                        </div>
                        <p class="text-sm text-muted-foreground mb-1 font-medium">Aucun module trouvé</p>
                        <p class="text-xs text-muted-foreground">Aucun module ne correspond à "<span x-text="search" class="font-semibold"></span>"</p>
                    </div>
                </div>

                @if($modules->isEmpty())
                    <div class="rounded-xl border border-border bg-card p-12 text-center shadow-sm">
                        <div class="flex flex-col items-center">
                            <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-muted/50">
                                <i class="fa-solid fa-box-open text-3xl text-muted-foreground/50"></i>
                            </div>
                            <p class="text-sm text-muted-foreground mb-1 font-medium">Aucun module installé</p>
                            <p class="text-xs text-muted-foreground mb-4">Commencez par installer votre premier module</p>
                            <button x-on:click="tab = 'marketplace'"
                                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-medium text-primary-foreground transition-colors hover:bg-primary/90">
                                <i class="fa-solid fa-store"></i>
                                <span>Découvrir le marketplace</span>
                            </button>
                        </div>
                    </div>
                @endif

                <div x-show="pages > 1 && filteredModules.length > 0" class="flex justify-center pt-4">
                    <nav class="inline-flex items-center gap-2">
                        <button type="button"
                                :disabled="page === 1"
                                @click="page = Math.max(1, page - 1)"
                                class="inline-flex items-center justify-center rounded-lg border border-input bg-background px-3 py-2 text-sm font-medium transition-colors hover:bg-accent hover:text-accent-foreground disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fa-solid fa-chevron-left"></i>
                        </button>

                        <template x-for="i in pages" :key="i">
                            <button type="button"
                                    x-show="pages <= 7 || i === 1 || i === pages || Math.abs(i - page) <= 1"
                                    @click="page = i"
                                    class="inline-flex items-center justify-center rounded-lg border px-3 py-2 text-sm font-medium transition-colors min-w-[40px]"
                                    :class="i === page
                                        ? 'bg-primary text-primary-foreground border-primary'
                                        : 'bg-background text-foreground border-input hover:bg-accent hover:text-accent-foreground'"
                                    x-text="i">
                            </button>
                        </template>

                        <button type="button"
                                :disabled="page === pages"
                                @click="page = Math.min(pages, page + 1)"
                                class="inline-flex items-center justify-center rounded-lg border border-input bg-background px-3 py-2 text-sm font-medium transition-colors hover:bg-accent hover:text-accent-foreground disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fa-solid fa-chevron-right"></i>
                        </button>
                    </nav>
                </div>
            </div>
        </template>

        <template x-if="tab === 'marketplace'">
            <div x-data="{
                page: 1,
                perPage: 9,
                allModules: {{ $marketModules->toJson() }},
                get filteredModules() {
                    if (!this.search) return this.allModules;
                    const searchTerm = this.search.toLowerCase();
                    return this.allModules.filter(m =>
                        m.name.toLowerCase().includes(searchTerm) ||
                        (m.description && m.description.toLowerCase().includes(searchTerm)) ||
                        (m.short_description && m.short_description.toLowerCase().includes(searchTerm)) ||
                        (m.author && m.author.toLowerCase().includes(searchTerm))
                    );
                },
                get total() { return this.filteredModules.length; },
                get pages() { return Math.ceil(this.total / this.perPage); },
                get paginated() {
                    return this.filteredModules.slice((this.page - 1) * this.perPage, this.page * this.perPage);
                }
            }" class="space-y-4">

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <template x-for="module in paginated" :key="module.id">
                        <div class="overflow-hidden rounded-xl border border-border bg-card shadow-sm hover:shadow-md transition-all flex flex-col">
                            <div class="aspect-video w-full bg-gradient-to-br from-primary/5 to-primary/10 overflow-hidden">
                                <img :src="module.thumbnail || 'https://via.placeholder.com/600x400?text=' + encodeURIComponent(module.name)"
                                     :alt="module.name"
                                     class="w-full h-full object-cover"
                                     loading="lazy">
                            </div>

                            <div class="p-4 flex-1 flex flex-col">
                                <div class="flex items-start gap-3 mb-3">
                                    <div class="flex-shrink-0 w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center border border-primary/20">
                                        <i class="fa-solid fa-puzzle-piece text-primary"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-semibold text-base leading-tight truncate" x-text="module.name"></h4>
                                        <div class="flex flex-wrap items-center gap-2 text-xs text-muted-foreground mt-1">
                                            <span class="inline-flex items-center gap-1">
                                                <i class="fa-solid fa-tag text-[10px]"></i>
                                                <span x-text="`v${module.version ?? '1.0.0'}`"></span>
                                            </span>
                                            <span x-show="module.author" class="inline-flex items-center gap-1">
                                                <i class="fa-solid fa-user text-[10px]"></i>
                                                <span x-text="module.author"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <p class="text-sm text-muted-foreground mb-4 line-clamp-2 flex-1"
                                   x-html="module.short_description || module.description || 'Aucune description disponible'"></p>

                                <div class="flex items-center justify-between mb-4 pb-4 border-t border-border pt-4">
                                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-semibold bg-primary/10 text-primary border border-primary/20">
                                        <i class="fa-solid fa-puzzle-piece text-[10px]"></i> Module
                                    </span>
                                    <span class="font-bold text-base"
                                          :class="module.price == '0.00' ? 'text-green-600 dark:text-green-400' : 'text-primary'"
                                          x-text="module.price == '0.00' ? 'Gratuit' : `${parseFloat(module.price).toFixed(2)}€`"></span>
                                </div>

                                <div class="flex gap-2">
                                    <template x-if="{{ json_encode($installedSlugs) }}.includes(module.slug)">
                                        <div class="flex-1 text-center py-2 px-3 rounded-lg bg-muted text-muted-foreground text-sm font-medium border border-border">
                                            <i class="fa-solid fa-check mr-1"></i> Installé
                                        </div>
                                    </template>

                                    <template x-if="!{{ json_encode($installedSlugs) }}.includes(module.slug) && (module.price == '0.00' || {{ json_encode($licensedIds) }}.includes(module.id))">
                                        <form :action="'{{ route('modules.install', '') }}/' + module.id" method="POST" class="flex-1">
                                            @csrf
                                            <button type="submit"
                                                    class="w-full inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2 text-sm font-medium bg-primary text-primary-foreground hover:bg-primary/90 transition-colors">
                                                <i class="fa-solid fa-download"></i> Installer
                                            </button>
                                        </form>
                                    </template>

                                    <template x-if="!{{ json_encode($licensedIds) }}.includes(module.id) && module.price != '0.00'">
                                        <a :href="`https://stratumcms.com/shop/${module.slug}/details`"
                                           target="_blank"
                                           class="flex-1 inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2 text-sm font-medium bg-primary text-primary-foreground hover:bg-primary/90 transition-colors">
                                            <i class="fa-solid fa-shopping-cart"></i> Acheter
                                        </a>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <div x-show="filteredModules.length === 0 && search !== ''" class="rounded-xl border border-border bg-card p-12 text-center shadow-sm">
                    <div class="flex flex-col items-center">
                        <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-muted/50">
                            <i class="fa-solid fa-magnifying-glass text-3xl text-muted-foreground/50"></i>
                        </div>
                        <p class="text-sm text-muted-foreground mb-1 font-medium">Aucun module trouvé</p>
                        <p class="text-xs text-muted-foreground">Aucun module ne correspond à "<span x-text="search" class="font-semibold"></span>"</p>
                    </div>
                </div>

                <div x-show="pages > 1 && filteredModules.length > 0" class="flex justify-center pt-4">
                    <nav class="inline-flex items-center gap-2">
                        <button type="button"
                                :disabled="page === 1"
                                @click="page = Math.max(1, page - 1)"
                                class="inline-flex items-center justify-center rounded-lg border border-input bg-background px-3 py-2 text-sm font-medium transition-colors hover:bg-accent hover:text-accent-foreground disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fa-solid fa-chevron-left"></i>
                        </button>

                        <template x-for="i in pages" :key="i">
                            <button type="button"
                                    x-show="pages <= 7 || i === 1 || i === pages || Math.abs(i - page) <= 1"
                                    @click="page = i"
                                    class="inline-flex items-center justify-center rounded-lg border px-3 py-2 text-sm font-medium transition-colors min-w-[40px]"
                                    :class="i === page
                                        ? 'bg-primary text-primary-foreground border-primary'
                                        : 'bg-background text-foreground border-input hover:bg-accent hover:text-accent-foreground'"
                                    x-text="i">
                            </button>
                        </template>

                        <button type="button"
                                :disabled="page === pages"
                                @click="page = Math.min(pages, page + 1)"
                                class="inline-flex items-center justify-center rounded-lg border border-input bg-background px-3 py-2 text-sm font-medium transition-colors hover:bg-accent hover:text-accent-foreground disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fa-solid fa-chevron-right"></i>
                        </button>
                    </nav>
                </div>

                <div x-show="allModules.length === 0" class="rounded-xl border border-border bg-card p-12 text-center shadow-sm">
                    <div class="flex flex-col items-center">
                        <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-muted/50">
                            <i class="fa-solid fa-store-slash text-3xl text-muted-foreground/50"></i>
                        </div>
                        <p class="text-sm text-muted-foreground mb-1 font-medium">Marketplace indisponible</p>
                        <p class="text-xs text-muted-foreground">Impossible de récupérer les modules du marketplace</p>
                    </div>
                </div>
            </div>
        </template>
    </div>
@endsection
