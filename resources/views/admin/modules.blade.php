@extends('admin.layouts.admin')

@section('title', 'Modules')

@section('content')
    <div x-data="modulesManager()" x-init="init()" class="max-w-7xl mx-auto space-y-6">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center space-x-4">
                <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
                    <i class="fas fa-puzzle-piece text-primary text-sm"></i>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-semibold text-foreground">Gestion des modules</h1>
                    <p class="text-sm text-muted-foreground hidden sm:block">Étendez les fonctionnalités de votre CMS</p>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                <div class="relative flex-1 sm:flex-none sm:w-64">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-muted-foreground w-4 h-4"></i>
                    <input type="text"
                           x-model="search"
                           placeholder="Rechercher un module..."
                           class="flex h-10 w-full rounded-lg border border-input bg-background pl-10 pr-4 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 transition-colors">
                </div>

                <form action="{{ route('modules.scan') }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-lg border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 w-10 sm:w-auto sm:px-3 text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-ring"
                            title="Scanner les nouveaux modules">
                        <i class="fas fa-rotate w-4 h-4"></i>
                        <span class="hidden sm:inline">Scanner</span>
                    </button>
                </form>
            </div>
        </div>

        @if (session('success'))
            <div class="p-4 rounded-xl bg-green-500/10 border border-green-500/20 text-green-600 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-600 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>{{ session('error') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        <div class="border-b border-border">
            <nav class="-mb-px flex space-x-2 sm:space-x-8 overflow-x-auto">
                <button
                    type="button"
                    x-on:click="setActiveTab('installed')"
                    :class="{
                        'border-primary text-primary': activeTab === 'installed',
                        'border-transparent text-muted-foreground hover:text-foreground hover:border-foreground/20': activeTab !== 'installed'
                    }"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors flex items-center space-x-2"
                >
                    <i class="fas fa-box w-4 h-4"></i>
                    <span>Modules installés</span>
                    <span class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 text-xs font-semibold rounded-full bg-primary/10 text-primary"
                          x-text="installedModules.length"></span>
                </button>

                <button
                    type="button"
                    x-on:click="setActiveTab('marketplace')"
                    :class="{
                        'border-primary text-primary': activeTab === 'marketplace',
                        'border-transparent text-muted-foreground hover:text-foreground hover:border-foreground/20': activeTab !== 'marketplace'
                    }"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors flex items-center space-x-2"
                >
                    <i class="fas fa-store w-4 h-4"></i>
                    <span>Marketplace</span>
                    <span class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 text-xs font-semibold rounded-full bg-primary/10 text-primary"
                          x-text="marketplaceModules.length"></span>
                </button>
            </nav>
        </div>

        <div x-show="activeTab === 'installed'" class="space-y-6" x-cloak>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="rounded-xl border bg-card p-4 text-center">
                    <div class="text-2xl font-bold text-primary" x-text="installedModules.length"></div>
                    <div class="text-sm text-muted-foreground">Total</div>
                </div>
                <div class="rounded-xl border bg-card p-4 text-center">
                    <div class="text-2xl font-bold text-green-600" x-text="installedModules.filter(m => m.active).length"></div>
                    <div class="text-sm text-muted-foreground">Actifs</div>
                </div>
                <div class="rounded-xl border bg-card p-4 text-center">
                    <div class="text-2xl font-bold text-amber-600" x-text="installedModules.filter(m => !m.active).length"></div>
                    <div class="text-sm text-muted-foreground">Inactifs</div>
                </div>
                <div class="rounded-xl border bg-card p-4 text-center">
                    <div class="text-2xl font-bold text-blue-600" x-text="installedModules.filter(m => hasUpdate(m)).length"></div>
                    <div class="text-sm text-muted-foreground">Mises à jour</div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                <template x-for="module in paginatedInstalledModules" :key="module.id">
                    <div class="rounded-xl border bg-card shadow-sm hover:shadow-md transition-all duration-300 flex flex-col group">
                        <div class="p-4 sm:p-6 border-b border-border">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center space-x-3 flex-1 min-w-0">
                                    <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                                        <i class="fas fa-puzzle-piece text-primary text-lg"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-semibold text-foreground truncate" x-text="module.name"></h3>
                                        <div class="flex items-center space-x-2 mt-1">
                                            <span class="text-xs text-muted-foreground font-mono" x-text="'v' + module.version"></span>
                                            <template x-if="module.author">
                                                <span class="text-xs text-muted-foreground">par <span x-text="module.author"></span></span>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-col items-end space-y-1 flex-shrink-0 ml-2">
                                    <span x-show="module.active"
                                          class="inline-flex items-center space-x-1 rounded-full bg-green-500/10 text-green-600 px-2 py-1 text-xs font-medium border border-green-500/20">
                                        <i class="fas fa-circle w-2 h-2"></i>
                                        <span class="hidden sm:inline">Actif</span>
                                    </span>
                                    <span x-show="!module.active"
                                          class="inline-flex items-center space-x-1 rounded-full bg-gray-500/10 text-gray-600 px-2 py-1 text-xs font-medium border border-gray-500/20">
                                        <i class="fas fa-circle w-2 h-2"></i>
                                        <span class="hidden sm:inline">Inactif</span>
                                    </span>
                                    <span x-show="hasUpdate(module)"
                                          class="inline-flex items-center space-x-1 rounded-full bg-blue-500/10 text-blue-600 px-2 py-1 text-xs font-medium border border-blue-500/20">
                                        <i class="fas fa-arrow-up w-2 h-2"></i>
                                        <span class="hidden sm:inline">MAJ</span>
                                    </span>
                                </div>
                            </div>

                            <p class="text-sm text-muted-foreground line-clamp-2"
                               x-text="module.description || 'Aucune description disponible'"></p>
                        </div>

                        <div class="p-4 sm:p-6 space-y-3 mt-auto">
                            <div class="flex space-x-2">
                                <form :action="`{{ url('admin/modules') }}/${module.active ? 'deactivate' : 'activate'}/${module.slug}`"
                                      method="POST"
                                      class="flex-1">
                                    @csrf
                                    <button type="submit"
                                            class="w-full inline-flex items-center justify-center gap-2 rounded-lg px-3 py-2 text-sm font-medium transition-colors"
                                            :class="module.active
                                                ? 'bg-red-50 text-red-700 border border-red-200 hover:bg-red-100'
                                                : 'bg-green-50 text-green-700 border border-green-200 hover:bg-green-100'">
                                        <i :class="module.active ? 'fas fa-power-off' : 'fas fa-play'"></i>
                                        <span x-text="module.active ? 'Désactiver' : 'Activer'"></span>
                                    </button>
                                </form>

                                <template x-if="hasUpdate(module)">
                                    <form :action="`{{ url('admin/modules/update') }}/${module.slug}`" method="POST">
                                        @csrf
                                        <button type="submit"
                                                class="inline-flex items-center justify-center gap-2 rounded-lg px-3 py-2 text-sm font-medium bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100 transition-colors whitespace-nowrap">
                                            <i class="fas fa-download"></i>
                                            <span class="hidden sm:inline">Mettre à jour</span>
                                        </button>
                                    </form>
                                </template>
                            </div>

                            <div class="flex items-center justify-between text-xs text-muted-foreground pt-2 border-t border-border">
                                <span class="flex items-center space-x-1">
                                    <i class="fas fa-cube"></i>
                                    <span>Module</span>
                                </span>
                                <template x-if="module.updated_at">
                                    <span x-text="formatDate(module.updated_at)"></span>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <div x-show="filteredInstalledModules.length === 0 && search"
                 class="rounded-xl border bg-card p-8 text-center">
                <div class="flex flex-col items-center">
                    <div class="w-16 h-16 rounded-full bg-muted/50 flex items-center justify-center mb-4">
                        <i class="fas fa-search text-2xl text-muted-foreground"></i>
                    </div>
                    <h3 class="text-lg font-medium text-foreground mb-2">Aucun module trouvé</h3>
                    <p class="text-muted-foreground text-sm">
                        Aucun module installé ne correspond à "<span x-text="search" class="font-semibold text-foreground"></span>"
                    </p>
                </div>
            </div>

            <div x-show="installedModules.length === 0 && !search"
                 class="rounded-xl border bg-card p-12 text-center">
                <div class="flex flex-col items-center">
                    <div class="w-20 h-20 rounded-full bg-muted/50 flex items-center justify-center mb-6">
                        <i class="fas fa-puzzle-piece text-3xl text-muted-foreground"></i>
                    </div>
                    <h3 class="text-xl font-medium text-foreground mb-2">Aucun module installé</h3>
                    <p class="text-muted-foreground mb-6 max-w-md mx-auto">
                        Commencez par installer votre premier module depuis le marketplace pour étendre les fonctionnalités de votre CMS.
                    </p>
                    <button x-on:click="setActiveTab('marketplace')"
                            class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary text-primary-foreground hover:bg-primary/90 h-11 px-6 py-2 text-sm font-medium transition-colors">
                        <i class="fas fa-store"></i>
                        <span>Explorer le marketplace</span>
                    </button>
                </div>
            </div>

            <div x-show="installedPages > 1 && filteredInstalledModules.length > 0"
                 class="flex items-center justify-between pt-6 border-t border-border">
                <div class="text-sm text-muted-foreground">
                    Affichage de <span x-text="Math.min(installedPerPage, filteredInstalledModules.length)"></span>
                    sur <span x-text="filteredInstalledModules.length"></span> modules
                </div>
                <div class="flex items-center space-x-2">
                    <button x-on:click="installedPage = Math.max(1, installedPage - 1)"
                            :disabled="installedPage === 1"
                            class="inline-flex items-center justify-center rounded-lg border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 w-9 text-sm font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-chevron-left w-4 h-4"></i>
                    </button>

                    <div class="flex items-center space-x-1">
                        <template x-for="page in installedPageRange" :key="page">
                            <button x-show="page !== '...'"
                                    x-on:click="installedPage = page"
                                    :class="page === installedPage
                                        ? 'bg-primary text-primary-foreground border-primary'
                                        : 'bg-background text-foreground border-input hover:bg-accent hover:text-accent-foreground'"
                                    class="inline-flex items-center justify-center rounded-lg border h-9 w-9 text-sm font-medium transition-colors">
                                <span x-text="page"></span>
                            </button>
                            <span x-show="page === '...'" class="px-2 text-muted-foreground">...</span>
                        </template>
                    </div>

                    <button x-on:click="installedPage = Math.min(installedPages, installedPage + 1)"
                            :disabled="installedPage === installedPages"
                            class="inline-flex items-center justify-center rounded-lg border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 w-9 text-sm font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-chevron-right w-4 h-4"></i>
                    </button>
                </div>
            </div>
        </div>

        <div x-show="activeTab === 'marketplace'" class="space-y-6" x-cloak>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 p-4 rounded-xl border bg-card">
                <div class="flex items-center space-x-4">
                    <span class="text-sm font-medium text-foreground">Filtrer :</span>
                    <div class="flex items-center space-x-2">
                        <button x-on:click="marketplaceFilter = 'all'"
                                :class="marketplaceFilter === 'all'
                                    ? 'bg-primary text-primary-foreground'
                                    : 'bg-background text-foreground border border-input hover:bg-accent'"
                                class="inline-flex items-center justify-center rounded-lg px-3 py-1.5 text-xs font-medium transition-colors">
                            Tous
                        </button>
                        <button x-on:click="marketplaceFilter = 'free'"
                                :class="marketplaceFilter === 'free'
                                    ? 'bg-green-500 text-white'
                                    : 'bg-background text-foreground border border-input hover:bg-accent'"
                                class="inline-flex items-center justify-center rounded-lg px-3 py-1.5 text-xs font-medium transition-colors">
                            Gratuits
                        </button>
                        <button x-on:click="marketplaceFilter = 'premium'"
                                :class="marketplaceFilter === 'premium'
                                    ? 'bg-purple-500 text-white'
                                    : 'bg-background text-foreground border border-input hover:bg-accent'"
                                class="inline-flex items-center justify-center rounded-lg px-3 py-1.5 text-xs font-medium transition-colors">
                            Premium
                        </button>
                    </div>
                </div>

                <div class="text-sm text-muted-foreground">
                    <span x-text="filteredMarketplaceModules.length"></span> modules disponibles
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                <template x-for="module in paginatedMarketplaceModules" :key="module.id">
                    <div class="rounded-xl border bg-card shadow-sm hover:shadow-md transition-all duration-300 flex flex-col group">
                        <div class="aspect-video w-full bg-gradient-to-br from-primary/5 to-primary/10 overflow-hidden rounded-t-xl">
                            <img :src="module.thumbnail || '/assets/images/module-placeholder.jpg'"
                                 :alt="module.name"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                 loading="lazy"
                                 x-on:error="module.thumbnail = '/assets/images/module-placeholder.jpg'">
                        </div>

                        <div class="p-4 sm:p-6 flex-1 flex flex-col">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-semibold text-foreground mb-1 truncate" x-text="module.name"></h3>
                                    <div class="flex items-center space-x-2 text-xs text-muted-foreground">
                                        <span class="font-mono" x-text="'v' + (module.version || '1.0.0')"></span>
                                        <template x-if="module.author">
                                            <span>par <span x-text="module.author"></span></span>
                                        </template>
                                    </div>
                                </div>

                                <div class="text-right flex-shrink-0 ml-2">
                                    <div class="text-lg font-bold"
                                         :class="module.price == '0.00' ? 'text-green-600' : 'text-primary'"
                                         x-text="module.price == '0.00' ? 'Gratuit' : `€${parseFloat(module.price).toFixed(2)}`"></div>
                                </div>
                            </div>

                            <p class="text-sm text-muted-foreground mb-4 line-clamp-3 flex-1"
                               x-text="module.short_description || module.description || 'Aucune description disponible'"></p>

                            <div class="flex items-center justify-between mb-4 pb-4 border-t border-border pt-4">
                                <span class="inline-flex items-center space-x-1 rounded-full bg-primary/10 text-primary px-2.5 py-1 text-xs font-medium border border-primary/20">
                                    <i class="fas fa-puzzle-piece w-3 h-3"></i>
                                    <span>Module</span>
                                </span>

                                <template x-if="isInstalled(module)">
                                    <span class="inline-flex items-center space-x-1 rounded-full bg-green-500/10 text-green-600 px-2.5 py-1 text-xs font-medium border border-green-500/20">
                                        <i class="fas fa-check w-3 h-3"></i>
                                        <span>Installé</span>
                                    </span>
                                </template>
                            </div>

                            <div class="space-y-2">
                                <template x-if="isInstalled(module)">
                                    <div class="text-center py-2 px-3 rounded-lg bg-muted text-muted-foreground text-sm font-medium border border-border">
                                        <i class="fas fa-check mr-2"></i> Déjà installé
                                    </div>
                                </template>

                                <template x-if="!isInstalled(module) && (module.price == '0.00' || isLicensed(module))">
                                    <form :action="'{{ route('modules.install', '') }}/' + module.id" method="POST">
                                        @csrf
                                        <button type="submit"
                                                class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2 text-sm font-medium transition-colors">
                                            <i class="fas fa-download"></i>
                                            <span>Installer le module</span>
                                        </button>
                                    </form>
                                </template>

                                <template x-if="!isLicensed(module) && module.price != '0.00'">
                                    <div class="space-y-2">
                                        <a :href="`https://stratumcms.com/shop/${module.slug}/details`"
                                           target="_blank"
                                           class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2 text-sm font-medium transition-colors">
                                            <i class="fas fa-shopping-cart"></i>
                                            <span>Acheter maintenant</span>
                                        </a>
                                        <p class="text-xs text-muted-foreground text-center">
                                            Licence requise pour l'installation
                                        </p>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <div x-show="filteredMarketplaceModules.length === 0 && search"
                 class="rounded-xl border bg-card p-8 text-center">
                <div class="flex flex-col items-center">
                    <div class="w-16 h-16 rounded-full bg-muted/50 flex items-center justify-center mb-4">
                        <i class="fas fa-search text-2xl text-muted-foreground"></i>
                    </div>
                    <h3 class="text-lg font-medium text-foreground mb-2">Aucun module trouvé</h3>
                    <p class="text-muted-foreground text-sm">
                        Aucun module du marketplace ne correspond à "<span x-text="search" class="font-semibold text-foreground"></span>"
                    </p>
                </div>
            </div>

            <div x-show="marketplaceModules.length === 0 && !search"
                 class="rounded-xl border bg-card p-12 text-center">
                <div class="flex flex-col items-center">
                    <div class="w-20 h-20 rounded-full bg-muted/50 flex items-center justify-center mb-6">
                        <i class="fas fa-store-slash text-3xl text-muted-foreground"></i>
                    </div>
                    <h3 class="text-xl font-medium text-foreground mb-2">Marketplace indisponible</h3>
                    <p class="text-muted-foreground mb-6 max-w-md mx-auto">
                        Impossible de se connecter au marketplace. Vérifiez votre connexion internet ou réessayez ultérieurement.
                    </p>
                    <button x-on:click="refreshMarketplace()"
                            class="inline-flex items-center justify-center gap-2 rounded-lg border border-input bg-background hover:bg-accent hover:text-accent-foreground h-11 px-6 py-2 text-sm font-medium transition-colors">
                        <i class="fas fa-rotate"></i>
                        <span>Réessayer</span>
                    </button>
                </div>
            </div>

            <div x-show="marketplacePages > 1 && filteredMarketplaceModules.length > 0"
                 class="flex items-center justify-between pt-6 border-t border-border">
                <div class="text-sm text-muted-foreground">
                    Affichage de <span x-text="Math.min(marketplacePerPage, filteredMarketplaceModules.length)"></span>
                    sur <span x-text="filteredMarketplaceModules.length"></span> modules
                </div>
                <div class="flex items-center space-x-2">
                    <button x-on:click="marketplacePage = Math.max(1, marketplacePage - 1)"
                            :disabled="marketplacePage === 1"
                            class="inline-flex items-center justify-center rounded-lg border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 w-9 text-sm font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-chevron-left w-4 h-4"></i>
                    </button>

                    <div class="flex items-center space-x-1">
                        <template x-for="page in marketplacePageRange" :key="page">
                            <button x-show="page !== '...'"
                                    x-on:click="marketplacePage = page"
                                    :class="page === marketplacePage
                                        ? 'bg-primary text-primary-foreground border-primary'
                                        : 'bg-background text-foreground border-input hover:bg-accent hover:text-accent-foreground'"
                                    class="inline-flex items-center justify-center rounded-lg border h-9 w-9 text-sm font-medium transition-colors">
                                <span x-text="page"></span>
                            </button>
                            <span x-show="page === '...'" class="px-2 text-muted-foreground">...</span>
                        </template>
                    </div>

                    <button x-on:click="marketplacePage = Math.min(marketplacePages, marketplacePage + 1)"
                            :disabled="marketplacePage === marketplacePages"
                            class="inline-flex items-center justify-center rounded-lg border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 w-9 text-sm font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-chevron-right w-4 h-4"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script id="modules-data" type="application/json">
        {!! json_encode([
            'installed' => $modules->toArray(),
            'marketplace' => (isset($marketModules) ? (is_object($marketModules) && method_exists($marketModules, 'toArray') ? $marketModules->toArray() : (array) $marketModules) : []),
            'installedSlugs' => $installedSlugs ?? [],
            'licensedIds' => $licensedIds ?? [],
        ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) !!}
    </script>

    <script>
        function modulesManager() {
            const initialData = (() => {
                try {
                    return JSON.parse(document.getElementById('modules-data').textContent || '{}');
                } catch (e) {
                    console.error('Impossible de parser les données initiales des modules', e);
                    return {
                        installed: [],
                        marketplace: [],
                        installedSlugs: [],
                        licensedIds: []
                    };
                }
            })();

            return {
                activeTab: 'installed',
                search: '',
                marketplaceFilter: 'all',

                installedModules: initialData.installed || [],
                marketplaceModules: initialData.marketplace || [],
                installedSlugs: initialData.installedSlugs || [],
                licensedIds: initialData.licensedIds || [],

                installedPage: 1,
                installedPerPage: 6,

                marketplacePage: 1,
                marketplacePerPage: 6,

                init() {
                    const savedTab = localStorage.getItem('modules_active_tab');
                    if (savedTab && (savedTab === 'installed' || savedTab === 'marketplace')) {
                        this.activeTab = savedTab;
                    }
                },

                setActiveTab(tab) {
                    this.activeTab = tab;
                    this.search = '';
                    this.installedPage = 1;
                    this.marketplacePage = 1;
                    localStorage.setItem('modules_active_tab', tab);
                },

                get filteredInstalledModules() {
                    let filtered = this.installedModules;

                    if (this.search) {
                        const searchTerm = this.search.toLowerCase();
                        filtered = filtered.filter(m =>
                            (m.name && m.name.toLowerCase().includes(searchTerm)) ||
                            (m.description && m.description.toLowerCase().includes(searchTerm)) ||
                            (m.author && m.author.toLowerCase().includes(searchTerm)) ||
                            (m.slug && m.slug.toLowerCase().includes(searchTerm))
                        );
                    }

                    return filtered;
                },

                get installedPages() {
                    return Math.ceil(this.filteredInstalledModules.length / this.installedPerPage) || 1;
                },

                get installedPageRange() {
                    const pages = [];
                    const total = this.installedPages;
                    const current = this.installedPage;

                    if (total <= 7) {
                        for (let i = 1; i <= total; i++) pages.push(i);
                    } else {
                        if (current <= 4) {
                            for (let i = 1; i <= 5; i++) pages.push(i);
                            pages.push('...');
                            pages.push(total);
                        } else if (current >= total - 3) {
                            pages.push(1);
                            pages.push('...');
                            for (let i = total - 4; i <= total; i++) pages.push(i);
                        } else {
                            pages.push(1);
                            pages.push('...');
                            for (let i = current - 1; i <= current + 1; i++) pages.push(i);
                            pages.push('...');
                            pages.push(total);
                        }
                    }

                    return pages;
                },

                get paginatedInstalledModules() {
                    const start = (this.installedPage - 1) * this.installedPerPage;
                    return this.filteredInstalledModules.slice(start, start + this.installedPerPage);
                },

                get filteredMarketplaceModules() {
                    let filtered = this.marketplaceModules;

                    if (this.search) {
                        const searchTerm = this.search.toLowerCase();
                        filtered = filtered.filter(m =>
                            (m.name && m.name.toLowerCase().includes(searchTerm)) ||
                            (m.description && m.description.toLowerCase().includes(searchTerm)) ||
                            (m.short_description && m.short_description.toLowerCase().includes(searchTerm)) ||
                            (m.author && m.author.toLowerCase().includes(searchTerm)) ||
                            (m.slug && m.slug.toLowerCase().includes(searchTerm))
                        );
                    }

                    if (this.marketplaceFilter === 'free') {
                        filtered = filtered.filter(m => m.price === '0.00' || parseFloat(m.price) === 0);
                    } else if (this.marketplaceFilter === 'premium') {
                        filtered = filtered.filter(m => !(m.price === '0.00' || parseFloat(m.price) === 0));
                    }

                    return filtered;
                },

                get marketplacePages() {
                    return Math.ceil(this.filteredMarketplaceModules.length / this.marketplacePerPage) || 1;
                },

                get marketplacePageRange() {
                    const pages = [];
                    const total = this.marketplacePages;
                    const current = this.marketplacePage;

                    if (total <= 7) {
                        for (let i = 1; i <= total; i++) pages.push(i);
                    } else {
                        if (current <= 4) {
                            for (let i = 1; i <= 5; i++) pages.push(i);
                            pages.push('...');
                            pages.push(total);
                        } else if (current >= total - 3) {
                            pages.push(1);
                            pages.push('...');
                            for (let i = total - 4; i <= total; i++) pages.push(i);
                        } else {
                            pages.push(1);
                            pages.push('...');
                            for (let i = current - 1; i <= current + 1; i++) pages.push(i);
                            pages.push('...');
                            pages.push(total);
                        }
                    }

                    return pages;
                },

                get paginatedMarketplaceModules() {
                    const start = (this.marketplacePage - 1) * this.marketplacePerPage;
                    return this.filteredMarketplaceModules.slice(start, start + this.marketplacePerPage);
                },

                hasUpdate(module) {
                    if (!module || !module.slug) return false;
                    const marketModule = this.marketplaceModules.find(m => m.slug === module.slug);
                    if (!marketModule || !marketModule.version || !module.version) return false;

                    const parseVer = v => ('' + v).split('.').map(Number);
                    const current = parseVer(module.version);
                    const latest = parseVer(marketModule.version);

                    for (let i = 0; i < Math.max(current.length, latest.length); i++) {
                        if ((latest[i] || 0) > (current[i] || 0)) return true;
                        if ((latest[i] || 0) < (current[i] || 0)) return false;
                    }
                    return false;
                },

                isInstalled(module) {
                    return this.installedSlugs.includes(module.slug);
                },

                isLicensed(module) {
                    return this.licensedIds.includes(module.id);
                },

                formatDate(dateString) {
                    if (!dateString) return '';
                    const date = new Date(dateString);
                    return date.toLocaleDateString('fr-FR', {
                        day: 'numeric',
                        month: 'short',
                        year: 'numeric'
                    });
                },

                refreshMarketplace() {
                    window.location.reload();
                }
            }
        }
    </script>
@endpush
