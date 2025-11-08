@extends('admin.layouts.admin')

@section('title', 'Thèmes')

@section('content')
    <div x-data="{ tab: 'installed', search: '' }" class="space-y-6">

        <div class="mb-4">
            <div class="flex flex-col sm:flex-row gap-4 justify-between items-start sm:items-center">
                <div>
                    <h1 class="text-2xl font-semibold sm:text-3xl">Gestion des Thèmes</h1>
                    <p class="text-sm text-muted-foreground mt-1">Gérez les thèmes installés et découvrez-en de nouveaux</p>
                </div>

                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full sm:w-auto justify-end sm:justify-start">
                    <div class="relative w-full sm:w-64">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-muted-foreground w-4 h-4 pointer-events-none"></i>
                        <input
                            type="text"
                            x-model="search"
                            placeholder="Rechercher un thème..."
                            class="w-full pl-10 pr-4 py-2 bg-background border border-border rounded-lg text-sm
                           focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                    <form action="{{ route('themes.scan') }}" method="POST">
                        @csrf
                        <button
                            type="submit"
                            title="Actualiser les thèmes"
                            aria-label="Actualiser les thèmes"
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
            <div
                x-data="{ show: true }"
                x-show="show"
                x-init="setTimeout(() => show = false, 5000)"
                x-transition
                class="rounded-md bg-green-100 text-green-800 px-4 py-3 border border-green-300 shadow-sm"
            >
                <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div
                x-data="{ show: true }"
                x-show="show"
                x-init="setTimeout(() => show = false, 5000)"
                x-transition
                class="rounded-md bg-red-100 text-red-800 px-4 py-3 border border-red-300 shadow-sm"
            >
                <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
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
                        {{ $themes->count() }}
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
                        {{ $marketThemes->count() }}
                    </span>
                </button>
            </div>
        </div>

        {{-- SECTION Thème actif --}}
        @php
            $active = $themes->firstWhere('active', true);
            if ($active){
                $hasConfig = File::exists(resource_path("themes/{$active->slug}/config/rules.php"));
            }
        @endphp

        <template x-if="tab === 'installed'">
            <div class="space-y-6">
                @if($active)
                    <div class="rounded-lg border bg-card text-card-foreground shadow-sm hover-lift hover-glow-purple transition-all">
                        <div class="flex flex-col space-y-1.5 p-6">
                            <div class="flex items-center space-x-4 text-xl font-semibold leading-none tracking-tight">
                                <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center shadow-sm">
                                    <i class="fa-solid fa-window-maximize text-primary text-lg"></i>
                                </div>
                                <span class="text-foreground">Thème actuel</span>
                                <span class="inline-flex items-center rounded-full bg-green-500 text-white px-3 py-0.5 text-xs font-semibold shadow-sm">
                                    <i class="fa-solid fa-check mr-1 text-xs"></i> Actif
                                </span>
                            </div>
                        </div>

                        <div class="p-6 pt-0">
                            <div class="flex items-center space-x-6">
                                <div class="w-24 h-20 rounded-lg overflow-hidden bg-muted">
                                    <img src="{{ $active->preview ?? "https://images.unsplash.com/photo-1486312338219-ce68d2c6f44d?w=400&h=200&fit=crop" }}"
                                         alt="{{ $active->name }}" class="object-cover w-full h-full">
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-xl font-semibold">{{ $active->name }}</h3>
                                    <p class="text-muted-foreground text-sm">v{{ $active->version }} • {{ $active->author }}</p>
                                    <p class="text-sm text-muted-foreground mt-1">{{ Str::limit($active->description, 200) }}</p>
                                </div>
                                <div class="flex space-x-2">
                                    @if($hasConfig)
                                        <a href="{{ route('themes.customize', $active->slug) }}"
                                           class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 rounded-md px-3">
                                            <i class="fas fa-cog w-4 h-4 mr-1"></i> Personnaliser
                                        </a>
                                    @endif

                                    <form action="{{ route('themes.deactivate', $active->slug) }}" method="POST">
                                        @csrf
                                        <button class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 rounded-md px-3">
                                            <i class="fa-solid fa-eye-slash w-4 h-4 mr-1"></i> Désactiver
                                        </button>
                                    </form>

                                    <a href="/?preview={{ $active->slug }}" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 rounded-md px-3">
                                        <i class="fas fa-eye w-4 h-4 mr-1"></i> Prévisualiser
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Liste des thèmes installés (hors actif) --}}
                <div x-data="{
                    page: 1,
                    perPage: 9,
                    themes: {{ $themes->where('active', false)->values()->toJson() }},
                    get filtered() {
                        if (!search) return this.themes;
                        const term = search.toLowerCase();
                        return this.themes.filter(t =>
                            (t.name && t.name.toLowerCase().includes(term)) ||
                            (t.description && t.description.toLowerCase().includes(term)) ||
                            (t.author && t.author.toLowerCase().includes(term))
                        );
                    },
                    get total() { return this.filtered.length; },
                    get pages() { return Math.ceil(this.total / this.perPage); },
                    get paginated() { return this.filtered.slice((this.page - 1) * this.perPage, this.page * this.perPage); }
                }" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-4">

                    <template x-for="theme in paginated" :key="theme.slug">
                        <div class="rounded-lg border bg-card text-card-foreground shadow-sm hover-glow-purple transition flex flex-col justify-between">
                            <div class="p-6 pb-0">
                                <div class="flex items-center space-x-3 mb-4">
                                    <div class="w-10 h-10 rounded-lg bg-primary flex items-center justify-center text-white">
                                        <i class="fas fa-palette w-5 h-5"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-lg font-semibold leading-none tracking-tight" x-text="theme.name"></h4>
                                        <p class="text-xs text-muted-foreground" x-text="`v${theme.version}`"></p>
                                    </div>
                                </div>

                                <div class="mb-4 h-32 w-full bg-muted rounded-lg overflow-hidden">
                                    <img :src="theme.preview || 'https://images.unsplash.com/photo-1488590528505-98d2b5aba04b?w=400&h=200&fit=crop'"
                                         class="object-cover w-full h-full" :alt="theme.name">
                                </div>

                                <p class="text-sm text-muted-foreground mb-4" x-text="theme.description ? (theme.description.length > 100 ? theme.description.substring(0,100) + '...' : theme.description) : 'Aucune description disponible'"></p>
                            </div>

                            <div class="px-6 pb-4 pt-2 flex items-center justify-between border-t border-border bg-muted/10">
                                <form :action="`{{ route('themes.activate', '') }}/${theme.slug}`" method="POST">
                                    @csrf
                                    <button class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 rounded-md px-3 items-center gap-2 text-sm font-medium hover-glow-purple">
                                        <i class="fas fa-check-circle text-primary w-4 h-4"></i> Activer
                                    </button>
                                </form>

                                <div class="flex items-center gap-2">
                                    <a :href="`/?preview=${theme.slug}`" title="Prévisualiser"
                                       class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 rounded-md px-3 text-muted-foreground hover:text-primary transition hover-glow-purple">
                                        <i class="fa-solid fa-eye w-4 h-4"></i>
                                    </a>

                                    <form action="#" method="POST">
                                        @csrf
                                        <button type="submit" title="Supprimer"
                                                class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 rounded-md px-3 text-destructive hover:text-red-600 transition hover-glow-red">
                                            <i class="fa-solid fa-trash w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </template>

                    <div x-show="filtered.length === 0 && search !== ''" class="col-span-full">
                        <div class="rounded-xl border border-border bg-card p-12 text-center shadow-sm">
                            <div class="flex flex-col items-center">
                                <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-muted/50">
                                    <i class="fa-solid fa-magnifying-glass text-3xl text-muted-foreground/50"></i>
                                </div>
                                <p class="text-sm text-muted-foreground mb-1 font-medium">Aucun thème trouvé</p>
                                <p class="text-xs text-muted-foreground">Aucun thème ne correspond à "<span x-text="search" class="font-semibold"></span>"</p>
                            </div>
                        </div>
                    </div>

                    <div x-show="pages > 1 && filtered.length > 0" class="col-span-full flex justify-center pt-4">
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

                    @if($themes->where('active', false)->isEmpty() && !$active)
                        <div class="col-span-full">
                            <div class="rounded-xl border border-border bg-card p-12 text-center shadow-sm">
                                <div class="flex flex-col items-center">
                                    <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-muted/50">
                                        <i class="fa-solid fa-box-open text-3xl text-muted-foreground/50"></i>
                                    </div>
                                    <p class="text-sm text-muted-foreground mb-1 font-medium">Aucun thème installé</p>
                                    <p class="text-xs text-muted-foreground mb-4">Commencez par installer votre premier thème</p>
                                    <button x-on:click="tab = 'marketplace'"
                                            class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-medium text-primary-foreground transition-colors hover:bg-primary/90">
                                        <i class="fa-solid fa-store"></i>
                                        <span>Découvrir le marketplace</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </template>

        {{-- MARKETPLACE --}}
        <template x-if="tab === 'marketplace'">
            <div x-data="{
                page: 1,
                perPage: 6,
                all: {{ $marketThemes->toJson() }},
                get filtered() {
                    if (!search) return this.all;
                    const term = search.toLowerCase();
                    return this.all.filter(t =>
                        (t.name && t.name.toLowerCase().includes(term)) ||
                        (t.short_description && t.short_description.toLowerCase().includes(term)) ||
                        (t.description && t.description.toLowerCase().includes(term)) ||
                        (t.author && t.author.toLowerCase().includes(term))
                    );
                },
                get total() { return this.filtered.length; },
                get pages() { return Math.ceil(this.total / this.perPage); },
                get paginated() { return this.filtered.slice((this.page - 1) * this.perPage, this.page * this.perPage); }
            }" class="space-y-6">

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <template x-for="theme in paginated" :key="theme.id">
                        <div class="rounded-lg border bg-card text-card-foreground shadow-sm hover-lift hover-glow-purple transition-all">
                            <div class="flex flex-col space-y-1.5 p-6">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center">
                                        <i class="fas fa-store w-5 h-5 text-white"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-2xl font-semibold leading-none tracking-tight" x-text="theme.name"></h4>
                                        <p class="text-sm text-muted-foreground" x-text="`v${theme.version ?? '?'}`"></p>
                                    </div>
                                </div>
                            </div>

                            <div class="p-6 pt-0">
                                <div class="mb-4">
                                    <div class="w-full h-32 rounded-lg overflow-hidden bg-muted">
                                        <img :src="theme.thumbnail || 'https://via.placeholder.com/400x200?text=Theme'" class="w-full h-full object-cover" :alt="theme.name">
                                    </div>
                                </div>
                                <p class="text-sm text-muted-foreground mb-3" x-html="theme.short_description || theme.description || 'Aucune description disponible'"></p>

                                <div class="flex items-center justify-between">
                                    <div class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors"
                                         :class="theme.type === 'theme'
                                    ? 'bg-blue-100 text-blue-800'
                                    : theme.type === 'module'
                                    ? 'bg-green-100 text-green-800'
                                    : 'bg-gray-100 text-gray-700'"
                                         x-text="theme.type ? (theme.type.charAt(0).toUpperCase() + theme.type.slice(1)) : 'Thème'">
                                    </div>
                                    <span class="font-semibold text-primary" x-text="theme.price == '0.00' ? 'Gratuit' : `${parseFloat(theme.price).toFixed(2)}€`"></span>
                                </div>
                            </div>

                            <div class="flex items-center p-6 pt-0">
                                <template x-if="{{ json_encode($themes->pluck('slug')) }}.includes(theme.slug)">
                                    <span class="text-sm text-muted-foreground">Déjà installé</span>
                                </template>

                                <template x-if="!{{ json_encode($themes->pluck('slug')) }}.includes(theme.slug) && (theme.price == '0.00' || {{ json_encode($licensedIds) }}.includes(theme.id))">
                                    <form :action="'{{ route('themes.install', '') }}/' + theme.id" method="POST">
                                        @csrf
                                        <button class="inline-flex items-center gap-2 text-sm font-medium px-3 py-2 rounded-md border bg-background hover:bg-accent hover:text-accent-foreground transition">
                                            <i class="fas fa-download text-primary w-4 h-4"></i> Installer
                                        </button>
                                    </form>
                                </template>

                                <template x-if="!{{ json_encode($licensedIds) }}.includes(theme.id) && theme.price != '0.00'">
                                    <div class="ml-auto">
                                        <a :href="`https://stratumcms.com/shop/${theme.id}/details`" target="_blank"
                                           class="inline-flex items-center gap-2 text-sm font-medium px-4 py-2 rounded-md border bg-background hover:bg-accent hover:text-accent-foreground transition hover-glow-purple">
                                            <i class="fas fa-arrow-right w-4 h-4"></i> Acheter
                                        </a>
                                    </div>
                                </template>
                            </div>

                        </div>
                    </template>
                </div>

                <div x-show="filtered.length === 0 && search !== ''" class="rounded-xl border border-border bg-card p-12 text-center shadow-sm">
                    <div class="flex flex-col items-center">
                        <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-muted/50">
                            <i class="fa-solid fa-magnifying-glass text-3xl text-muted-foreground/50"></i>
                        </div>
                        <p class="text-sm text-muted-foreground mb-1 font-medium">Aucun thème trouvé</p>
                        <p class="text-xs text-muted-foreground">Aucun thème ne correspond à "<span x-text="search" class="font-semibold"></span>"</p>
                    </div>
                </div>

                <div x-show="pages > 1 && filtered.length > 0" class="flex justify-center mt-4">
                    <nav class="inline-flex items-center gap-2">
                        <button type="button"
                                class="px-3 py-1 text-sm rounded-md border bg-background hover:bg-muted transition"
                                :class="page === 1 && 'opacity-50 cursor-not-allowed'"
                                @click="page = Math.max(1, page - 1)">
                            ← Préc.
                        </button>

                        <template x-for="i in pages" :key="i">
                            <button type="button"
                                    class="px-3 py-1 text-sm rounded-md border"
                                    :class="i === page ? 'bg-primary text-white' : 'bg-background hover:bg-muted text-muted-foreground'"
                                    @click="page = i" x-text="i">
                            </button>
                        </template>

                        <button type="button"
                                class="px-3 py-1 text-sm rounded-md border bg-background hover:bg-muted transition"
                                :class="page === pages && 'opacity-50 cursor-not-allowed'"
                                @click="page = Math.min(pages, page + 1)">
                            Suiv. →
                        </button>
                    </nav>
                </div>

                <div x-show="all.length === 0" class="rounded-xl border border-border bg-card p-12 text-center shadow-sm">
                    <div class="flex flex-col items-center">
                        <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-muted/50">
                            <i class="fa-solid fa-store-slash text-3xl text-muted-foreground/50"></i>
                        </div>
                        <p class="text-sm text-muted-foreground mb-1 font-medium">Marketplace indisponible</p>
                        <p class="text-xs text-muted-foreground">Impossible de récupérer les thèmes du marketplace ou aucun thème n'est publié.</p>
                    </div>
                </div>

            </div>
        </template>

    </div>
@endsection
