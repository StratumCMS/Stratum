@extends('admin.layouts.admin')

@section('title', 'Modules')

@section('content')
    <div x-data="{ tab: 'installed' }" class="space-y-6">
        {{-- Alertes --}}
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                 x-transition
                 class="rounded-md bg-green-100 text-green-800 px-4 py-3 border border-green-300 shadow-sm">
                <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                 x-transition
                 class="rounded-md bg-red-100 text-red-800 px-4 py-3 border border-red-300 shadow-sm">
                <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
            </div>
        @endif

        {{-- Onglets + Recherche --}}
        <div class="flex flex-col sm:flex-row gap-4 justify-between items-start sm:items-center">
            <div class="flex space-x-4">
                <button x-on:click="tab = 'installed'"
                        :class="tab === 'installed' ? 'bg-primary text-white hover:bg-primary/90' : 'border border-input bg-background hover:bg-accent hover:text-accent-foreground'"
                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 h-9 rounded-md px-3 hover-glow-purple">
                    Modules installés
                </button>
                <button x-on:click="tab = 'marketplace'"
                        :class="tab === 'marketplace' ? 'bg-primary text-primary-foreground hover:bg-primary/90' : 'border border-input bg-background hover:bg-accent hover:text-accent-foreground'"
                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 h-9 rounded-md px-3 hover-glow-purple">
                    Marketplace
                </button>
            </div>
        </div>

        {{-- Modules installés --}}
        <template x-if="tab === 'installed'">
            <div class="mt-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($modules as $module)
                    <div class="rounded-lg border bg-card text-card-foreground shadow-sm hover-glow-purple transition flex flex-col justify-between">
                        <div class="p-6 pb-0">
                            <div class="flex items-center space-x-3 mb-4">
                                <div class="w-10 h-10 rounded-lg bg-primary flex items-center justify-center text-white">
                                    <i class="fas fa-puzzle-piece text-lg"></i>
                                </div>

                                <div class="flex-1">
                                    <h4 class="text-lg font-semibold leading-none tracking-tight">{{ $module->name }}</h4>
                                    <p class="text-xs text-muted-foreground">v{{ $module->version }}</p>
                                </div>
                            </div>

                            <p class="text-sm text-muted-foreground mb-4">{{ Str::limit($module->description, 100) }}</p>
                        </div>

                        <div class="px-6 pb-4 pt-2 flex items-center justify-between border-t border-border bg-muted/10">
                            <form action="{{ route('modules.' . ($module->active ? 'deactivate' : 'activate'), $module->slug) }}" method="POST">
                                @csrf
                                <button class="inline-flex items-center gap-2 text-sm font-medium px-3 py-2 rounded-md border bg-background hover:bg-accent hover:text-accent-foreground transition hover-glow-purple">
                                    @if($module->active)
                                        <i class="fas fa-eye-slash"></i> Désactiver
                                    @else
                                        <i class="fas fa-check-circle"></i> Activer
                                    @endif
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="mt-16 col-span-full">
                        <div class="rounded-lg border bg-muted/30 text-muted-foreground text-center py-12 shadow-sm">
                            <i class="fas fa-box-open text-3xl text-muted-foreground mb-3"></i>
                            <h3 class="text-lg font-semibold">Aucun module installé</h3>
                            <p class="text-sm mt-1">Aucun module n'a encore été installé sur ce site.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </template>

        {{-- Marketplace --}}
        <template x-if="tab === 'marketplace'">
            <div x-data="{
                page: 1,
                perPage: 6,
                get total() { return {{ $marketModules->count() }}; },
                get pages() { return Math.ceil(this.total / this.perPage); },
                get paginated() {
                    return {{ $marketModules->toJson() }}.slice((this.page - 1) * this.perPage, this.page * this.perPage);
                }
            }" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <template x-for="module in paginated" :key="module.id">
                        <div class="rounded-lg border bg-card text-card-foreground shadow-sm hover-lift hover-glow-purple transition-all">
                            <div class="flex flex-col space-y-1.5 p-6">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center">
                                        <i class="fas fa-store w-5 h-5 text-white"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-2xl font-semibold leading-none tracking-tight" x-text="module.name"></h4>
                                        <p class="text-sm text-muted-foreground" x-text="`v${module.version ?? '?'}`"></p>
                                    </div>
                                </div>
                            </div>

                            <div class="p-6 pt-0">
                                <div class="mb-4">
                                    <div class="w-full h-32 rounded-lg overflow-hidden bg-muted">
                                        <img :src="module.thumbnail || 'https://via.placeholder.com/400x200?text=Module'" class="w-full h-full object-cover" :alt="module.name">
                                    </div>
                                </div>
                                <p class="text-sm text-muted-foreground mb-3" x-html="module.short_description || module.description"></p>

                                <div class="flex items-center justify-between">
                                    <div class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors bg-green-100 text-green-800">
                                        Module
                                    </div>
                                    <span class="font-semibold text-primary" x-text="module.price == '0.00' ? 'Gratuit' : `${parseFloat(module.price).toFixed(2)}€`"></span>
                                </div>
                            </div>

                            <div class="flex items-center p-6 pt-0">
                                <template x-if="{{ json_encode($installedSlugs) }}.includes(module.slug)">
                                    <span class="text-sm text-muted-foreground">Déjà installé</span>
                                </template>

                                <template x-if="!{{ json_encode($installedSlugs) }}.includes(module.slug) && (module.price == '0.00' || {{ json_encode($licensedIds) }}.includes(module.id))">
                                    <form :action="'{{ route('modules.install', '') }}/' + module.id" method="POST">
                                        @csrf
                                        <button class="inline-flex items-center gap-2 text-sm font-medium px-3 py-2 rounded-md border bg-background hover:bg-accent hover:text-accent-foreground transition">
                                            <i class="fas fa-download text-primary w-4 h-4"></i> Installer
                                        </button>
                                    </form>
                                </template>

                                <template x-if="!{{ json_encode($licensedIds) }}.includes(module.id) && module.price != '0.00'">
                                    <div class="ml-auto">
                                        <a :href="`https://stratumcms.com/shop/${module.slug}/details`" target="_blank"
                                           class="inline-flex items-center gap-2 text-sm font-medium px-4 py-2 rounded-md border bg-background hover:bg-accent hover:text-accent-foreground transition hover-glow-purple">
                                            <i class="fas fa-arrow-right w-4 h-4"></i> Acheter
                                        </a>
                                    </div>
                                </template>
                            </div>

                        </div>
                    </template>
                </div>

                <div class="flex justify-center mt-4" x-show="pages > 1">
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
            </div>
        </template>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/alpinejs" defer></script>
@endpush
