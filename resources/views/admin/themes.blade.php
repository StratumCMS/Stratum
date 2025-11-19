@extends('admin.layouts.admin')

@section('title', 'Thèmes')

@section('content')
    <div x-data="themesManager()" x-init="init()" class="max-w-7xl mx-auto space-y-6">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center space-x-4">
                <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
                    <i class="fas fa-palette text-primary text-sm"></i>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-semibold text-foreground">Gestion des thèmes</h1>
                    <p class="text-sm text-muted-foreground hidden sm:block">Personnalisez l'apparence de votre site</p>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                <div class="relative flex-1 sm:flex-none sm:w-64">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-muted-foreground w-4 h-4"></i>
                    <input type="text"
                           x-model="search"
                           placeholder="Rechercher un thème..."
                           class="flex h-10 w-full rounded-lg border border-input bg-background pl-10 pr-4 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 transition-colors">
                </div>

                <form action="{{ route('themes.scan') }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-lg border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 w-10 sm:w-auto sm:px-3 text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-ring"
                            title="Scanner les nouveaux thèmes">
                        <i class="fas fa-rotate w-4 h-4"></i>
                        <span class="hidden sm:inline">Scanner</span>
                    </button>
                </form>
            </div>
        </div>

        <div class="border-b border-border">
            <nav class="-mb-px flex space-x-2 sm:space-x-8 overflow-x-auto">
                <button
                    type="button"
                    x-on:click="setActiveTab('installed')"
                    :class="activeTab === 'installed' ? 'border-primary text-primary' : 'border-transparent text-muted-foreground hover:text-foreground hover:border-foreground/20'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors flex items-center space-x-2"
                >
                    <i class="fas fa-box w-4 h-4"></i>
                    <span>Thèmes installés</span>
                    <span class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 text-xs font-semibold rounded-full bg-primary/10 text-primary"
                          x-text="installedThemes.length"></span>
                </button>

                <button
                    type="button"
                    x-on:click="setActiveTab('marketplace')"
                    :class="activeTab === 'marketplace' ? 'border-primary text-primary' : 'border-transparent text-muted-foreground hover:text-foreground hover:border-foreground/20'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors flex items-center space-x-2"
                >
                    <i class="fas fa-store w-4 h-4"></i>
                    <span>Marketplace</span>
                    <span class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 text-xs font-semibold rounded-full bg-primary/10 text-primary"
                          x-text="marketplaceThemes.length"></span>
                </button>
            </nav>
        </div>

        <div x-show="activeTab === 'installed'" class="space-y-6" x-cloak>
            <template x-if="activeTheme">
                <div class="rounded-xl border bg-card shadow-sm border-l-4 border-l-green-500">
                    <div class="p-4 sm:p-6 border-b border-border">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-lg bg-green-500/10 flex items-center justify-center">
                                <i class="fas fa-star text-green-500 text-sm"></i>
                            </div>
                            <div>
                                <h2 class="text-lg sm:text-xl font-semibold text-foreground">Thème actif</h2>
                                <p class="text-sm text-muted-foreground">Thème actuellement utilisé sur votre site</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 sm:p-6">
                        <div class="flex flex-col lg:flex-row lg:items-start gap-6">
                            <div class="lg:w-1/3">
                                <div class="aspect-video rounded-lg overflow-hidden bg-muted border border-border">
                                    <img :src="activeTheme.preview || 'https://placehold.co/400x200?text=Theme'"
                                         :alt="activeTheme.name"
                                         class="w-full h-full object-cover"
                                         loading="lazy">
                                </div>
                            </div>

                            <div class="lg:flex-1">
                                <div class="flex items-start justify-between mb-4">
                                    <div>
                                        <h3 class="text-xl font-semibold text-foreground" x-text="activeTheme.name"></h3>
                                        <div class="flex items-center space-x-4 mt-2 text-sm text-muted-foreground">
                                            <span class="font-mono" x-text="'v' + activeTheme.version"></span>
                                            <span x-text="activeTheme.author"></span>
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center space-x-1 rounded-full bg-green-500/10 text-green-600 px-3 py-1 text-sm font-medium border border-green-500/20">
                                        <i class="fas fa-check w-3 h-3"></i>
                                        <span>Actif</span>
                                    </span>
                                </div>

                                <p class="text-muted-foreground mb-6" x-text="activeTheme.description || 'Aucune description disponible'"></p>

                                <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                                    <template x-if="activeTheme.hasConfig">
                                        <a :href="`{{ route('themes.customize', '') }}/${activeTheme.slug}`"
                                           class="inline-flex items-center justify-center gap-2 rounded-lg border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2 text-sm font-medium transition-colors">
                                            <i class="fas fa-cog w-4 h-4"></i>
                                            <span>Personnaliser</span>
                                        </a>
                                    </template>

                                    <a :href="`/?preview=${activeTheme.slug}`" target="_blank"
                                       class="inline-flex items-center justify-center gap-2 rounded-lg border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2 text-sm font-medium transition-colors">
                                        <i class="fas fa-eye w-4 h-4"></i>
                                        <span>Prévisualiser</span>
                                    </a>

                                    <form :action="`{{ route('themes.deactivate', '') }}/${activeTheme.slug}`" method="POST">
                                        @csrf
                                        <button type="submit"
                                                class="inline-flex items-center justify-center gap-2 rounded-lg border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2 text-sm font-medium transition-colors text-amber-600 hover:text-amber-700">
                                            <i class="fas fa-eye-slash w-4 h-4"></i>
                                            <span>Désactiver</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-foreground">Thèmes disponibles</h3>
                    <span class="text-sm text-muted-foreground" x-text="`${filteredInstalledThemes.length} thème(s)`"></span>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                    <template x-for="theme in paginatedInstalledThemes" :key="theme.slug">
                        <div class="rounded-xl border bg-card shadow-sm hover:shadow-md transition-all duration-300 flex flex-col group">
                            <div class="aspect-video w-full bg-gradient-to-br from-primary/5 to-primary/10 overflow-hidden rounded-t-xl">
                                <img :src="theme.preview || 'https://placehold.co/400x200?text=Theme'"
                                     :alt="theme.name"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                     loading="lazy">
                            </div>

                            <div class="p-4 sm:p-6 flex-1 flex flex-col">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-semibold text-foreground truncate" x-text="theme.name"></h4>
                                        <div class="flex items-center space-x-2 mt-1 text-xs text-muted-foreground">
                                            <span class="font-mono" x-text="'v' + theme.version"></span>
                                            <template x-if="theme.author">
                                                <span>par <span x-text="theme.author"></span></span>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                <p class="text-sm text-muted-foreground mb-4 line-clamp-2 flex-1"
                                   x-text="theme.description || 'Aucune description disponible'"></p>

                                <div class="flex items-center justify-between pt-4 border-t border-border">
                                    <form :action="`{{ route('themes.activate', '') }}/${theme.slug}`" method="POST">
                                        @csrf
                                        <button type="submit"
                                                class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary text-primary-foreground hover:bg-primary/90 h-9 px-3 py-2 text-sm font-medium transition-colors">
                                            <i class="fas fa-play w-4 h-4"></i>
                                            <span>Activer</span>
                                        </button>
                                    </form>

                                    <div class="flex items-center space-x-1">
                                        <a :href="`/?preview=${theme.slug}`" target="_blank"
                                           class="inline-flex items-center justify-center rounded-lg border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 w-9 text-sm font-medium transition-colors"
                                           title="Prévisualiser">
                                            <i class="fas fa-eye w-4 h-4"></i>
                                        </a>

                                        <button type="button"
                                                x-on:click="openDeleteModal(theme)"
                                                class="inline-flex items-center justify-center rounded-lg border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 w-9 text-sm font-medium transition-colors text-destructive hover:text-destructive/80"
                                                title="Désactiver">
                                            <i class="fas fa-trash w-4 h-4"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <div x-show="filteredInstalledThemes.length === 0 && search"
                     class="rounded-xl border bg-card p-8 text-center" x-cloak>
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 rounded-full bg-muted/50 flex items-center justify-center mb-4">
                            <i class="fas fa-search text-2xl text-muted-foreground"></i>
                        </div>
                        <h3 class="text-lg font-medium text-foreground mb-2">Aucun thème trouvé</h3>
                        <p class="text-muted-foreground text-sm">
                            Aucun thème installé ne correspond à "<span x-text="search" class="font-semibold text-foreground"></span>"
                        </p>
                    </div>
                </div>

                <div x-show="inactiveThemes.length === 0 && !search && !activeTheme"
                     class="rounded-xl border bg-card p-12 text-center" x-cloak>
                    <div class="flex flex-col items-center">
                        <div class="w-20 h-20 rounded-full bg-muted/50 flex items-center justify-center mb-6">
                            <i class="fas fa-palette text-3xl text-muted-foreground"></i>
                        </div>
                        <h3 class="text-xl font-medium text-foreground mb-2">Aucun thème installé</h3>
                        <p class="text-muted-foreground mb-6 max-w-md mx-auto">
                            Commencez par installer votre premier thème depuis le marketplace pour personnaliser l'apparence de votre site.
                        </p>
                        <button x-on:click="setActiveTab('marketplace')"
                                class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary text-primary-foreground hover:bg-primary/90 h-11 px-6 py-2 text-sm font-medium transition-colors">
                            <i class="fas fa-store"></i>
                            <span>Explorer le marketplace</span>
                        </button>
                    </div>
                </div>

                <div x-show="installedPages > 1 && filteredInstalledThemes.length > 0"
                     class="flex items-center justify-between pt-6 border-t border-border" x-cloak>
                    <div class="text-sm text-muted-foreground">
                        Affichage de <span x-text="Math.min(installedPerPage, filteredInstalledThemes.length)"></span>
                        sur <span x-text="filteredInstalledThemes.length"></span> thèmes
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
        </div>

        <div x-show="activeTab === 'marketplace'" class="space-y-6" x-cloak>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 p-4 rounded-xl border bg-card">
                <div class="flex items-center space-x-4">
                    <span class="text-sm font-medium text-foreground">Filtrer :</span>
                    <div class="flex items-center space-x-2">
                        <button x-on:click="marketplaceFilter = 'all'"
                                :class="marketplaceFilter === 'all' ? 'bg-primary text-primary-foreground' : 'bg-background text-foreground border border-input hover:bg-accent'"
                                class="inline-flex items-center justify-center rounded-lg px-3 py-1.5 text-xs font-medium transition-colors">
                            Tous
                        </button>
                        <button x-on:click="marketplaceFilter = 'free'"
                                :class="marketplaceFilter === 'free' ? 'bg-green-500 text-white' : 'bg-background text-foreground border border-input hover:bg-accent'"
                                class="inline-flex items-center justify-center rounded-lg px-3 py-1.5 text-xs font-medium transition-colors">
                            Gratuits
                        </button>
                        <button x-on:click="marketplaceFilter = 'premium'"
                                :class="marketplaceFilter === 'premium' ? 'bg-purple-500 text-white' : 'bg-background text-foreground border border-input hover:bg-accent'"
                                class="inline-flex items-center justify-center rounded-lg px-3 py-1.5 text-xs font-medium transition-colors">
                            Premium
                        </button>
                    </div>
                </div>

                <div class="text-sm text-muted-foreground">
                    <span x-text="filteredMarketplaceThemes.length"></span> thèmes disponibles
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                <template x-for="theme in paginatedMarketplaceThemes" :key="theme.id">
                    <div class="rounded-xl border bg-card shadow-sm hover:shadow-md transition-all duration-300 flex flex-col group">
                        <div class="aspect-video w-full bg-gradient-to-br from-primary/5 to-primary/10 overflow-hidden rounded-t-xl">
                            <img :src="theme.thumbnail || 'https://placehold.co/400x200?text=Theme'"
                                 :alt="theme.name"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                 loading="lazy">
                        </div>

                        <div class="p-4 sm:p-6 flex-1 flex flex-col">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-semibold text-foreground mb-1 truncate" x-text="theme.name"></h3>
                                    <div class="flex items-center space-x-2 text-xs text-muted-foreground">
                                        <span class="font-mono" x-text="'v' + (theme.version || '1.0.0')"></span>
                                        <template x-if="theme.author">
                                            <span>par <span x-text="theme.author"></span></span>
                                        </template>
                                    </div>
                                </div>

                                <div class="text-right flex-shrink-0 ml-2">
                                    <div class="text-lg font-bold"
                                         :class="(theme.price == '0.00' || parseFloat(theme.price) === 0) ? 'text-green-600' : 'text-primary'"
                                         x-text="(theme.price == '0.00' || parseFloat(theme.price) === 0) ? 'Gratuit' : `€${parseFloat(theme.price).toFixed(2)}`"></div>
                                </div>
                            </div>

                            <p class="text-sm text-muted-foreground mb-4 line-clamp-3 flex-1"
                               x-text="theme.short_description || theme.description || 'Aucune description disponible'"></p>

                            <div class="flex items-center justify-between mb-4 pb-4 border-t border-border pt-4">
                                <span class="inline-flex items-center space-x-1 rounded-full bg-primary/10 text-primary px-2.5 py-1 text-xs font-medium border border-primary/20">
                                    <i class="fas fa-palette w-3 h-3"></i>
                                    <span>Thème</span>
                                </span>

                                <template x-if="isInstalled(theme)">
                                    <span class="inline-flex items-center space-x-1 rounded-full bg-green-500/10 text-green-600 px-2.5 py-1 text-xs font-medium border border-green-500/20">
                                        <i class="fas fa-check w-3 h-3"></i>
                                        <span>Installé</span>
                                    </span>
                                </template>
                            </div>

                            <div class="space-y-2">
                                <template x-if="isInstalled(theme)">
                                    <div class="text-center py-2 px-3 rounded-lg bg-muted text-muted-foreground text-sm font-medium border border-border">
                                        <i class="fas fa-check mr-2"></i> Déjà installé
                                    </div>
                                </template>

                                <template x-if="!isInstalled(theme) && (theme.price == '0.00' || isLicensed(theme))">
                                    <form :action="'{{ route('themes.install', '') }}/' + theme.id" method="POST">
                                        @csrf
                                        <button type="submit"
                                                class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2 text-sm font-medium transition-colors">
                                            <i class="fas fa-download"></i>
                                            <span>Installer le thème</span>
                                        </button>
                                    </form>
                                </template>

                                <template x-if="!isLicensed(theme) && theme.price != '0.00'">
                                    <div class="space-y-2">
                                        <a :href="`https://stratumcms.com/shop/${theme.slug}/details`"
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

            <div x-show="filteredMarketplaceThemes.length === 0 && search"
                 class="rounded-xl border bg-card p-8 text-center" x-cloak>
                <div class="flex flex-col items-center">
                    <div class="w-16 h-16 rounded-full bg-muted/50 flex items-center justify-center mb-4">
                        <i class="fas fa-search text-2xl text-muted-foreground"></i>
                    </div>
                    <h3 class="text-lg font-medium text-foreground mb-2">Aucun thème trouvé</h3>
                    <p class="text-muted-foreground text-sm">
                        Aucun thème du marketplace ne correspond à "<span x-text="search" class="font-semibold text-foreground"></span>"
                    </p>
                </div>
            </div>

            <div x-show="marketplaceThemes.length === 0 && !search"
                 class="rounded-xl border bg-card p-12 text-center" x-cloak>
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

            <div x-show="marketplacePages > 1 && filteredMarketplaceThemes.length > 0"
                 class="flex items-center justify-between pt-6 border-t border-border" x-cloak>
                <div class="text-sm text-muted-foreground">
                    Affichage de <span x-text="Math.min(marketplacePerPage, filteredMarketplaceThemes.length)"></span>
                    sur <span x-text="filteredMarketplaceThemes.length"></span> thèmes
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

    <div>
        <div x-show="showSuccessModal"
             x-cloak
             x-on:keydown.escape.window="closeSuccessModal()"
             x-on:click.away="closeSuccessModal()"
             x-transition.opacity
             style="display:none"
             class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/40"></div>
            <div class="w-full sm:max-w-lg bg-white dark:bg-slate-900 rounded-t-lg sm:rounded-lg shadow-lg overflow-hidden">
                <div class="p-4 sm:p-6">
                    <div class="flex items-start space-x-3">
                        <div class="w-10 h-10 rounded-lg bg-green-500/10 flex items-center justify-center">
                            <i class="fas fa-check-circle text-green-600 text-lg"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg font-semibold text-foreground">Succès</h3>
                            <p class="text-sm text-muted-foreground mt-1" x-text="successMessage"></p>
                        </div>
                    </div>
                    <div class="mt-4 text-right">
                        <button x-on:click="closeSuccessModal()" class="inline-flex items-center justify-center rounded-lg px-4 py-2 bg-primary text-primary-foreground hover:bg-primary/90">Fermer</button>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="showErrorModal"
             x-cloak
             x-on:keydown.escape.window="closeErrorModal()"
             x-on:click.away="closeErrorModal()"
             x-transition.opacity
             style="display:none"
             class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/40"></div>
            <div class="w-full sm:max-w-lg bg-white dark:bg-slate-900 rounded-t-lg sm:rounded-lg shadow-lg overflow-hidden">
                <div class="p-4 sm:p-6">
                    <div class="flex items-start space-x-3">
                        <div class="w-10 h-10 rounded-lg bg-red-500/10 flex items-center justify-center">
                            <i class="fas fa-exclamation-circle text-red-600 text-lg"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg font-semibold text-foreground">Erreur</h3>
                            <p class="text-sm text-muted-foreground mt-1" x-text="errorMessage"></p>
                        </div>
                    </div>
                    <div class="mt-4 text-right">
                        <button x-on:click="closeErrorModal()" class="inline-flex items-center justify-center rounded-lg px-4 py-2 bg-primary text-primary-foreground hover:bg-primary/90">Fermer</button>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="showDeleteModal"
             x-cloak
             x-on:keydown.escape.window="closeDeleteModal()"
             x-on:click.away="closeDeleteModal()"
             x-transition.opacity
             style="display:none"
             class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/40"></div>

            <div class="w-full sm:max-w-md bg-white dark:bg-slate-900 rounded-t-lg sm:rounded-lg shadow-lg overflow-hidden">
                <form :action="deleteFormAction" method="POST" class="p-4 sm:p-6">
                    @csrf
                    <div class="flex items-start space-x-3">
                        <div class="w-10 h-10 rounded-lg bg-destructive/10 flex items-center justify-center">
                            <i class="fas fa-trash text-destructive text-lg"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg font-semibold text-foreground" x-text="deleteTitle"></h3>
                            <p class="text-sm text-muted-foreground mt-1" x-html="deleteMessage"></p>

                            <template x-if="deleteTarget && deleteTarget.preview">
                                <div class="mt-3">
                                    <div class="aspect-video rounded-md overflow-hidden border border-border">
                                        <img :src="deleteTarget.preview" :alt="deleteTarget.name" class="w-full h-full object-cover">
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="mt-4 flex justify-end gap-2">
                        <button type="button" x-on:click="closeDeleteModal()" class="inline-flex items-center justify-center rounded-lg px-4 py-2 border border-input bg-background hover:bg-accent">Annuler</button>
                        <button type="submit" class="inline-flex items-center justify-center rounded-lg px-4 py-2 bg-destructive text-white hover:opacity-95">Désactiver</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script id="themes-data" type="application/json">
        {!! json_encode([
            'installed' => $themes->toArray(),
            'marketplace' => isset($marketThemes) ? (is_object($marketThemes) && method_exists($marketThemes, 'toArray') ? $marketThemes->toArray() : (array) $marketThemes) : [],
            'installedSlugs' => $themes->pluck('slug')->toArray(),
            'licensedIds' => $licensedIds ?? [],
            'sessionSuccess' => session('success') ?? null,
            'sessionError' => session('error') ?? null,
        ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) !!}
    </script>

    <script>
        function themesManager() {
            const raw = document.getElementById('themes-data')?.textContent || '{}';
            let initial = {};
            try {
                initial = JSON.parse(raw);
            } catch (e) {
                console.error('themes-data parse error', e);
                initial = {
                    installed: [],
                    marketplace: [],
                    installedSlugs: [],
                    licensedIds: [],
                    sessionSuccess: null,
                    sessionError: null
                };
            }

            return {
                activeTab: 'installed',
                search: '',
                marketplaceFilter: 'all',

                installedThemes: initial.installed || [],
                marketplaceThemes: initial.marketplace || [],
                installedSlugs: initial.installedSlugs || [],
                licensedIds: initial.licensedIds || [],

                installedPage: 1,
                installedPerPage: 6,
                marketplacePage: 1,
                marketplacePerPage: 6,

                showSuccessModal: false,
                successMessage: '',
                showErrorModal: false,
                errorMessage: '',
                showDeleteModal: false,
                deleteTarget: null,
                deleteFormAction: '',
                deleteTitle: 'Désactiver le thème',
                deleteMessage: '',

                init() {
                    const saved = localStorage.getItem('themes_active_tab');
                    if (saved === 'installed' || saved === 'marketplace') this.activeTab = saved;

                    if (initial.sessionSuccess && String(initial.sessionSuccess).trim().length > 0) {
                        this.successMessage = initial.sessionSuccess;
                        this.showSuccessModal = true;
                    }
                    if (initial.sessionError && String(initial.sessionError).trim().length > 0) {
                        this.errorMessage = initial.sessionError;
                        this.showErrorModal = true;
                    }
                },

                setActiveTab(tab) {
                    this.activeTab = tab;
                    this.search = '';
                    this.installedPage = 1;
                    this.marketplacePage = 1;
                    localStorage.setItem('themes_active_tab', tab);
                },

                get activeTheme() {
                    return this.installedThemes.find(t => t.active) || null;
                },
                get inactiveThemes() {
                    return this.installedThemes.filter(t => !t.active);
                },

                get filteredInstalledThemes() {
                    let filtered = this.inactiveThemes.slice();
                    if (this.search) {
                        const s = this.search.toLowerCase();
                        filtered = filtered.filter(t =>
                            (t.name && t.name.toLowerCase().includes(s)) ||
                            (t.description && t.description.toLowerCase().includes(s)) ||
                            (t.author && t.author.toLowerCase().includes(s)) ||
                            (t.slug && t.slug.toLowerCase().includes(s))
                        );
                    }
                    return filtered;
                },

                get installedPages() {
                    return Math.max(1, Math.ceil(this.filteredInstalledThemes.length / this.installedPerPage));
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

                get paginatedInstalledThemes() {
                    const start = (this.installedPage - 1) * this.installedPerPage;
                    return this.filteredInstalledThemes.slice(start, start + this.installedPerPage);
                },

                get filteredMarketplaceThemes() {
                    let filtered = this.marketplaceThemes.slice();
                    if (this.search) {
                        const s = this.search.toLowerCase();
                        filtered = filtered.filter(t =>
                            (t.name && t.name.toLowerCase().includes(s)) ||
                            (t.description && t.description.toLowerCase().includes(s)) ||
                            (t.short_description && t.short_description.toLowerCase().includes(s)) ||
                            (t.author && t.author.toLowerCase().includes(s)) ||
                            (t.slug && t.slug.toLowerCase().includes(s))
                        );
                    }
                    if (this.marketplaceFilter === 'free') {
                        filtered = filtered.filter(t => t.price === '0.00' || parseFloat(t.price) === 0);
                    } else if (this.marketplaceFilter === 'premium') {
                        filtered = filtered.filter(t => !(t.price === '0.00' || parseFloat(t.price) === 0));
                    }
                    return filtered;
                },

                get marketplacePages() {
                    return Math.max(1, Math.ceil(this.filteredMarketplaceThemes.length / this.marketplacePerPage));
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

                get paginatedMarketplaceThemes() {
                    const start = (this.marketplacePage - 1) * this.marketplacePerPage;
                    return this.filteredMarketplaceThemes.slice(start, start + this.marketplacePerPage);
                },

                isInstalled(theme) {
                    return this.installedSlugs.includes(theme.slug);
                },

                isLicensed(theme) {
                    return this.licensedIds.includes(theme.id);
                },

                refreshMarketplace() {
                    window.location.reload();
                },

                openDeleteModal(theme) {
                    this.deleteTarget = theme;
                    this.deleteFormAction = `{{ url('admin/themes/deactivate') }}/${encodeURIComponent(theme.slug)}`;
                    this.deleteTitle = 'Désactiver le thème';
                    this.deleteMessage = `Voulez-vous désactiver le thème <strong>${this.escapeHtml(theme.name)}</strong> ? Vous pourrez le réactiver plus tard.`;
                    this.showDeleteModal = true;
                    setTimeout(() => {
                        const modal = document.querySelector('[x-show="showDeleteModal"]');
                        if (modal && modal.scrollIntoView) modal.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }, 80);
                },

                closeDeleteModal() {
                    this.showDeleteModal = false;
                    this.deleteTarget = null;
                    this.deleteFormAction = '';
                    this.deleteTitle = 'Désactiver le thème';
                    this.deleteMessage = '';
                },

                closeSuccessModal() {
                    this.showSuccessModal = false;
                    this.successMessage = '';
                },

                closeErrorModal() {
                    this.showErrorModal = false;
                    this.errorMessage = '';
                },

                escapeHtml(str) {
                    return String(str || '')
                        .replace(/&/g, '&amp;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;')
                        .replace(/"/g, '&quot;')
                        .replace(/'/g, '&#039;');
                }
            };
        }
    </script>
@endpush
