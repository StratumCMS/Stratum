@extends('admin.layouts.admin')

@section('title', 'Thèmes')

@section('content')
    <div x-data="{ tab: 'installed' }" class="space-y-6">

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


        <div class="flex flex-col sm:flex-row gap-4 justify-between items-start sm:items-center">
            <div class="flex space-x-4">
                <button x-on:click="tab = 'installed'"
                        :class="tab === 'installed' ? 'bg-primary text-primary-foreground hover:bg-primary/90' : 'border border-input bg-background hover:bg-accent hover:text-accent-foreground'"
                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 h-9 rounded-md px-3 hover-glow-purple">
                    Thèmes installés
                </button>
                <button x-on:click="tab = 'marketplace'"
                        :class="tab === 'marketplace' ? 'bg-primary text-primary-foreground hover:bg-primary/90' : 'border border-input bg-background hover:bg-accent hover:text-accent-foreground'"
                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 h-9 rounded-md px-3 hover-glow-purple">
                    Marketplace
                </button>
            </div>

            <div class="relative w-full sm:w-64">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-muted-foreground w-4 h-4"></i>
                <input type="text" placeholder="Rechercher un thème..."
                       class="pl-10 pr-4 py-2 bg-background border border-border rounded-lg focus:ring-2 focus:ring-primary/20 focus:outline-none focus:border-primary w-full">
            </div>
        </div>

        <template x-if="tab === 'installed'">
            <div class="rounded-lg border bg-card text-card-foreground shadow-sm hover-lift hover-glow-purple transition-all">
                @php
                    $active = $themes->firstWhere('active', true);
                    $hasConfig = File::exists(resource_path("themes/{$active->slug}/config/rules.php"));
                @endphp

                @if($active)
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
                                <img src="{{ $theme->preview ?? "https://images.unsplash.com/photo-1486312338219-ce68d2c6f44d?w=400&h=200&fit=crop" }}"
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
                                       class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 rounded-md px-3">
                                        <i class="fas fa-cog w-4 h-4 mr-1"></i> Personnaliser
                                    </a>
                                @endif
                                <a href="/?preview={{$active->slug}}" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 rounded-md px-3"><i class="fas fa-eye w-4 h-4 mr-1"></i>Prévisualiser</a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </template>

        <template x-if="tab === 'installed'">
            <div class="mt-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($themes->where('active', false) as $theme)
                    <div class="rounded-lg border bg-card text-card-foreground shadow-sm hover-glow-purple transition flex flex-col justify-between">
                        <div class="p-6 pb-0">
                            <div class="flex items-center space-x-3 mb-4">
                                <div class="w-10 h-10 rounded-lg bg-primary flex items-center justify-center text-white">
                                    <i class="fas fa-palette w-5 h-5"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-lg font-semibold leading-none tracking-tight">{{ $theme->name }}</h4>
                                    <p class="text-xs text-muted-foreground">v{{ $theme->version }}</p>
                                </div>
                            </div>

                            <div class="mb-4 h-32 w-full bg-muted rounded-lg overflow-hidden">
                                <img src="{{ $theme->preview ?? "https://images.unsplash.com/photo-1488590528505-98d2b5aba04b?w=400&h=200&fit=crop" }}"
                                     class="object-cover w-full h-full" alt="{{ $theme->name }}">
                            </div>

                            <p class="text-sm text-muted-foreground mb-4">{{ Str::limit($theme->description, 100) }}</p>
                        </div>

                        <div class="px-6 pb-4 pt-2 flex items-center justify-between border-t border-border bg-muted/10">
                            <form action="{{ route('themes.activate', $theme->slug) }}" method="POST">
                                @csrf
                                <button class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 rounded-md px-3 items-center gap-2 text-sm font-medium hover-glow-purple">
                                    <i class="fas fa-check-circle text-primary w-4 h-4"></i> Activer
                                </button>
                            </form>

                            <div class="flex items-center gap-2">
                                <a href="/?preview={{$theme->slug}}" title="Prévisualiser"
                                   class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 rounded-md px-3 text-muted-foreground hover:text-primary transition hover-glow-purple">
                                    <i class="fa-solid fa-eye w-4 h-4"></i>
                                </a>
                                <form action="#" method="POST">
                                    @csrf
                                    <button type="submit" title="Supprimer"
                                            class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 rounded-md px-3 text-destructive hover:text-red-600 transition hover-glow-red">
                                        <i class="fa-solid fa-trash w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="mt-16 col-span-full">
                        <div class="rounded-lg border bg-muted/30 text-muted-foreground text-center py-12 shadow-sm">
                            <i class="fas fa-box-open text-3xl text-muted-foreground mb-3"></i>
                            <h3 class="text-lg font-semibold">Aucun autre thème installé</h3>
                            <p class="text-sm mt-1">Tous les thèmes installés sont déjà utilisés ou aucun autre thème n'est disponible.</p>
                        </div>
                    </div>
                @endforelse

            </div>
        </template>

        <template x-if="tab === 'marketplace'">
            <div class="rounded-lg border bg-card text-card-foreground shadow-sm hover-glow-purple transition p-10 text-center">
                <i class="fas fa-palette text-4xl text-primary mb-3"></i>
                <h3 class="text-xl font-semibold mb-2">Découvrir plus de thèmes</h3>
                <p class="text-muted-foreground mb-4">Explorez notre marketplace pour trouver le thème parfait.</p>
                <a href="#" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 rounded-md px-3 text-muted-foreground hover:text-primary transition hover-glow-purple">
                    <i class="fas fa-external-link-alt mr-2"></i> Explorer la marketplace
                </a>
            </div>
        </template>

    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/alpinejs" defer></script>
@endpush
