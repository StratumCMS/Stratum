@props([
    'role' => null,
    'isEdit' => false,
    'xModel' => 'modalOpen'
])

@php
    $roleName = old('name', $role->name ?? '');
    $roleDescription = old('description', $role->description ?? '');
    $roleColor = old('color', $role->color ?? '#3b82f6');
    $roleIcon = old('icon', $role->icon ?? 'user');
    $selectedPermissions = old('permissions', $role ? $role->permissions->pluck('id')->toArray() : []);
@endphp

<div x-data="{
    selectedIcon: '{{ $roleIcon }}',
    selectedColor: '{{ $roleColor }}',
    iconPickerOpen: false,
    iconSearch: '',
    roleName: '{{ $roleName }}',
    iconsLoaded: false,
    allIcons: [],
    isLoading: false,

    get selectedIconClass() {
        return this.formatIconClass(this.selectedIcon, 'text-primary flex-shrink-0');
    },

    get selectedIconClassSmall() {
        return this.formatIconClass(this.selectedIcon, 'text-sm');
    },

    get selectedIconClassLarge() {
        return this.formatIconClass(this.selectedIcon, 'text-lg');
    },

    formatIconClass(icon, extraClasses = '') {
        if (!icon) return `fas fa-user ${extraClasses}`.trim();

        if (icon.match(/^fa-(solid|regular|light|thin|duotone|sharp|brands)/)) {
            return `${icon} ${extraClasses}`.trim();
        }

        if (icon.startsWith('fa-')) {
            return `fas ${icon} ${extraClasses}`.trim();
        }

        return `fas fa-${icon} ${extraClasses}`.trim();
    },

    popularIcons: [
        'user', 'users', 'user-shield', 'user-tie', 'user-cog', 'user-check',
        'shield', 'shield-alt', 'shield-check', 'crown', 'star', 'award',
        'cog', 'cogs', 'wrench', 'tools', 'hammer', 'screwdriver',
        'edit', 'pen', 'pencil-alt', 'marker', 'lock', 'lock-open', 'key',
        'eye', 'eye-slash', 'heart', 'chart-line', 'chart-bar', 'chart-pie',
        'home', 'building', 'store', 'briefcase', 'suitcase',
        'envelope', 'message', 'comments', 'comment-alt', 'bell', 'calendar', 'clock',
        'search', 'filter', 'plus', 'minus', 'check', 'times', 'ban',
        'arrow-right', 'arrow-left', 'arrow-up', 'arrow-down', 'download', 'upload',
        'trash', 'trash-alt', 'save', 'share', 'print', 'copy', 'paste',
        'image', 'camera', 'video', 'music', 'film', 'photo-video',
        'file', 'file-alt', 'folder', 'folder-open', 'database', 'server', 'code', 'bug',
        'rocket', 'fire', 'bolt', 'lightbulb', 'flag', 'bookmark', 'tag', 'hashtag',
        'globe', 'map', 'map-marker', 'compass', 'wifi', 'bluetooth',
        'mobile', 'laptop', 'tablet', 'desktop', 'mouse', 'keyboard',
        'car', 'bicycle', 'plane', 'ship', 'train', 'bus',
        'money-bill', 'credit-card', 'wallet', 'shopping-cart', 'gift', 'trophy',
        'medkit', 'stethoscope', 'heartbeat', 'ambulance', 'pills',
        'graduation-cap', 'book', 'book-open', 'pencil-ruler', 'calculator'
    ],

    get displayedIcons() {
        if (!this.iconSearch.trim()) {
            return this.popularIcons;
        }

        const search = this.iconSearch.toLowerCase().trim();
        if (search.length < 2) {
            return [];
        }

        if (!this.iconsLoaded) {
            this.loadAllIcons();
            return this.popularIcons.filter(icon =>
                icon.toLowerCase().includes(search)
            ).slice(0, 30);
        }

        return this.allIcons
            .filter(icon => icon.toLowerCase().includes(search))
            .slice(0, 60);
    },

    async loadAllIcons() {
        if (this.iconsLoaded || this.isLoading) return;

        this.isLoading = true;
        const CACHE_KEY = 'fa_icons_v4_pro';
        const CACHE_EXPIRY = 7 * 24 * 60 * 60 * 1000;

        try {
            const cached = localStorage.getItem(CACHE_KEY);
            if (cached) {
                const { timestamp, icons } = JSON.parse(cached);
                if (Date.now() - timestamp < CACHE_EXPIRY && Array.isArray(icons)) {
                    this.allIcons = icons;
                    this.iconsLoaded = true;
                    this.isLoading = false;
                    return;
                }
            }

            console.log('⏳ Chargement des icônes FontAwesome...');

            const possibleUrls = [
                '/vendor/fontawesome/css/all.css',
            ];

            let cssContent = '';
            for (const url of possibleUrls) {
                try {
                    const response = await fetch(url);
                    if (response.ok) {
                        cssContent = await response.text();
                        console.log(`✓ Fichier CSS chargé depuis: ${url}`);
                        break;
                    }
                } catch (e) {
                    console.log(`Échec pour ${url}`);
                }
            }

            if (!cssContent) {
                throw new Error('Impossible de charger les icônes');
            }

            const iconSet = new Set();

            const patterns = [
                /\.fa-solid\.fa-([a-z0-9-]+)/g,
                /\.fa-regular\.fa-([a-z0-9-]+)/g,
                /\.fa-light\.fa-([a-z0-9-]+)/g,
                /\.fa-thin\.fa-([a-z0-9-]+)/g,
                /\.fa-duotone\.fa-([a-z0-9-]+)/g,
                /\.fa-sharp\.fa-solid\.fa-([a-z0-9-]+)/g,
                /\.fas\.fa-([a-z0-9-]+)/g,
                /\.fa-([a-z0-9-]+)(?::before|,|\s)/g
            ];

            const excluded = new Set([
                'fw', 'lg', 'xs', 'sm', 'xl', '2x', '3x', '4x', '5x', '6x', '7x', '8x', '9x', '10x',
                'rotate-90', 'rotate-180', 'rotate-270', 'flip-horizontal', 'flip-vertical',
                'spin', 'pulse', 'border', 'pull-left', 'pull-right', 'stack', 'inverse',
                'solid', 'regular', 'light', 'thin', 'duotone', 'sharp', 'brands'
            ]);

            patterns.forEach(pattern => {
                let match;
                while ((match = pattern.exec(cssContent)) !== null) {
                    const name = match[1];
                    if (!excluded.has(name) &&
                        name.length >= 2 &&
                        !name.match(/^\d/) &&
                        !name.includes('--')) {
                        iconSet.add(name);
                    }
                }
            });

            this.allIcons = Array.from(iconSet).sort();
            this.iconsLoaded = true;

            localStorage.setItem(CACHE_KEY, JSON.stringify({
                timestamp: Date.now(),
                icons: this.allIcons
            }));

            console.log(`✓ ${this.allIcons.length} icônes chargées avec succès`);
        } catch (error) {
            console.error('❌ Erreur chargement icônes:', error);
            this.allIcons = [...this.popularIcons];
            this.iconsLoaded = true;
        } finally {
            this.isLoading = false;
        }
    },

    selectIcon(icon) {
        this.selectedIcon = icon;
        this.iconPickerOpen = false;
        this.iconSearch = '';
    },

    init() {
        this.$watch('iconPickerOpen', (value) => {
            if (value && !this.iconsLoaded) {
                this.loadAllIcons();
            }
        });
    }
}"
     x-show="{{ $xModel }} @if($isEdit) === {{ $role->id }} @endif"
     x-transition:enter="ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">

    <div x-show="{{ $xModel }} @if($isEdit) === {{ $role->id }} @endif"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click.outside="{{ $xModel }} = false; iconPickerOpen = false"
         @keydown.escape.window="{{ $xModel }} = false; iconPickerOpen = false"
         class="bg-card border border-border rounded-xl shadow-2xl w-full max-w-2xl max-h-[90dvh] overflow-hidden flex flex-col">

        <div class="p-4 sm:p-6 border-b border-border flex items-center justify-between bg-white/50 dark:bg-gray-900/50">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center">
                    <i class="fas {{ $isEdit ? 'fa-edit' : 'fa-plus' }} text-primary text-sm"></i>
                </div>
                <h2 class="text-lg sm:text-xl font-semibold text-foreground">
                    {{ $isEdit ? "Modifier le rôle" : "Créer un nouveau rôle" }}
                </h2>
            </div>
            <button @click="{{ $xModel }} = false"
                    class="inline-flex items-center justify-center rounded-lg p-2 text-muted-foreground hover:text-foreground hover:bg-accent transition-colors"
                    aria-label="Fermer">
                <i class="fas fa-times w-5 h-5"></i>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-4 sm:p-6">
            <form method="POST"
                  id="roleForm{{ $isEdit ? '_' . $role->id : '_create' }}"
                  action="{{ $isEdit ? route('admin.roles.update', $role) : route('admin.roles.store') }}"
                  class="space-y-6">
                @csrf
                @if($isEdit)
                    @method('PUT')
                @endif

                <div class="space-y-2">
                    <label for="roleName{{ $isEdit ? '_' . $role->id : '_create' }}" class="text-sm font-medium text-foreground flex items-center justify-between">
                        <span>Nom du rôle</span>
                        <span class="text-xs text-muted-foreground font-normal">Requis</span>
                    </label>
                    <input type="text"
                           id="roleName{{ $isEdit ? '_' . $role->id : '_create' }}"
                           name="name"
                           x-model="roleName"
                           class="flex h-10 w-full rounded-lg border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 transition-colors"
                           placeholder="Ex: Administrateur, Éditeur..."
                           required>
                </div>

                <div class="space-y-2">
                    <label for="roleDescription{{ $isEdit ? '_' . $role->id : '_create' }}" class="text-sm font-medium text-foreground">Description</label>
                    <textarea id="roleDescription{{ $isEdit ? '_' . $role->id : '_create' }}"
                              name="description"
                              rows="3"
                              class="flex min-h-[80px] w-full rounded-lg border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 resize-none transition-colors"
                              placeholder="Description du rôle et de ses permissions...">{{ $roleDescription }}</textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-foreground">Couleur</label>
                        <div class="flex items-center space-x-3">
                            <input type="color"
                                   name="color"
                                   x-model="selectedColor"
                                   class="w-12 h-12 rounded-lg border border-input cursor-pointer p-1 bg-white"
                                   aria-label="Choisir une couleur">
                            <div class="flex-1">
                                <input type="text"
                                       x-model="selectedColor"
                                       class="flex h-10 w-full rounded-lg border border-input bg-background px-3 py-2 text-sm font-mono focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                                       readonly
                                       aria-label="Code couleur">
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2 relative">
                        <label class="text-sm font-medium text-foreground">Icône</label>
                        <input type="hidden" name="icon" x-model="selectedIcon">

                        <button type="button"
                                @click="iconPickerOpen = !iconPickerOpen; if(!iconsLoaded) loadAllIcons()"
                                class="flex h-10 w-full items-center justify-between rounded-lg border border-input bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 transition-colors hover:bg-accent/50"
                                aria-haspopup="true"
                                :aria-expanded="iconPickerOpen">
                            <div class="flex items-center space-x-2 min-w-0 flex-1">
                                <i :class="selectedIconClass"></i>
                                <span class="truncate" x-text="selectedIcon || 'Choisir une icône'"></span>
                            </div>
                            <i class="fas fa-chevron-down text-xs text-muted-foreground flex-shrink-0 ml-2 transition-transform"
                               :class="{ 'rotate-180': iconPickerOpen }"></i>
                        </button>

                        <div x-show="iconPickerOpen"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             @click.outside="iconPickerOpen = false"
                             class="absolute z-[100] mt-2 w-full bg-card border border-border rounded-lg shadow-xl overflow-hidden max-h-96 flex flex-col">

                            <div class="p-3 border-b border-border bg-muted/30">
                                <div class="relative">
                                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground text-sm"></i>
                                    <input type="text"
                                           x-model="iconSearch"
                                           @input.debounce.300ms=""
                                           @click.stop
                                           placeholder="Rechercher une icône (min. 2 caractères)..."
                                           class="w-full pl-10 pr-3 py-2.5 text-sm border border-input rounded-lg bg-background focus:outline-none focus:ring-2 focus:ring-ring"
                                           aria-label="Rechercher une icône">
                                </div>
                                <div class="flex justify-between items-center mt-2">
                                    <p class="text-xs text-muted-foreground" x-show="!isLoading">
                                        <template x-if="!iconSearch.trim()">
                                            <span>💡 Icônes populaires</span>
                                        </template>
                                        <template x-if="iconSearch.trim()">
                                            <span x-text="`${displayedIcons.length} résultat(s)`"></span>
                                        </template>
                                    </p>
                                    <div x-show="isLoading" class="flex items-center space-x-2 text-xs text-muted-foreground">
                                        <i class="fas fa-spinner fa-spin"></i>
                                        <span>Chargement...</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex-1 overflow-y-auto p-3">
                                <template x-if="displayedIcons.length > 0">
                                    <div class="grid grid-cols-4 sm:grid-cols-6 gap-2">
                                        <template x-for="icon in displayedIcons" :key="icon">
                                            <button type="button"
                                                    @click="selectIcon(icon)"
                                                    :title="icon"
                                                    class="flex flex-col items-center justify-center p-2 rounded-lg hover:bg-accent transition-all duration-200 group relative"
                                                    :class="{
                                                        'bg-primary/20 ring-2 ring-primary': selectedIcon === icon || selectedIcon === 'fa-solid fa-' + icon || selectedIcon === 'fa-regular fa-' + icon,
                                                        'bg-transparent': selectedIcon !== icon && selectedIcon !== 'fa-solid fa-' + icon && selectedIcon !== 'fa-regular fa-' + icon
                                                    }"
                                                    :aria-label="`Sélectionner l'icône ${icon}`">
                                                <i :class="`fas fa-${icon} text-lg mb-1 group-hover:scale-110 transition-transform`"
                                                   :class="{ 'text-primary': selectedIcon === icon || selectedIcon === 'fa-solid fa-' + icon || selectedIcon === 'fa-regular fa-' + icon }"></i>
                                                <span class="text-[10px] text-muted-foreground truncate w-full text-center leading-tight" x-text="icon"></span>
                                            </button>
                                        </template>
                                    </div>
                                </template>

                                <template x-if="displayedIcons.length === 0 && iconSearch && iconSearch.length >= 2 && !isLoading">
                                    <div class="text-center py-8 text-sm text-muted-foreground">
                                        <i class="fas fa-search mb-2 text-2xl opacity-50"></i>
                                        <p>Aucune icône trouvée pour "<span x-text="iconSearch" class="font-medium"></span>"</p>
                                        <p class="text-xs mt-1">Essayez avec d'autres termes</p>
                                    </div>
                                </template>

                                <template x-if="!iconSearch.trim() && !isLoading">
                                    <div class="text-center py-4">
                                        <p class="text-xs text-muted-foreground mb-3">Tapez dans la barre de recherche pour voir plus d'icônes</p>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-2" x-show="selectedIcon && selectedIcon.length > 0">
                    <label class="text-sm font-medium text-foreground">Style d'icône</label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                        <button type="button"
                                @click="selectedIcon = selectedIcon.replace(/^(fa-solid|fa-regular|fa-light|fa-thin|fa-duotone|fa-sharp\s+fa-solid|fas|far|fal|fat|fad)\s+/, '').replace(/^fa-/, ''); selectedIcon = 'fa-solid fa-' + selectedIcon"
                                class="flex items-center justify-center gap-2 p-2 rounded-lg border transition-colors text-xs"
                                :class="selectedIcon.startsWith('fa-solid') || (!selectedIcon.includes('fa-') && selectedIcon.length > 0) ? 'border-primary bg-primary/10 text-primary font-medium' : 'border-input hover:bg-accent'">
                            <i class="fa-solid fa-circle"></i>
                            Solid
                        </button>
                        <button type="button"
                                @click="selectedIcon = selectedIcon.replace(/^(fa-solid|fa-regular|fa-light|fa-thin|fa-duotone|fa-sharp\s+fa-solid|fas|far|fal|fat|fad)\s+/, '').replace(/^fa-/, ''); selectedIcon = 'fa-regular fa-' + selectedIcon"
                                class="flex items-center justify-center gap-2 p-2 rounded-lg border transition-colors text-xs"
                                :class="selectedIcon.startsWith('fa-regular') ? 'border-primary bg-primary/10 text-primary font-medium' : 'border-input hover:bg-accent'">
                            <i class="fa-regular fa-circle"></i>
                            Regular
                        </button>
                        <button type="button"
                                @click="selectedIcon = selectedIcon.replace(/^(fa-solid|fa-regular|fa-light|fa-thin|fa-duotone|fa-sharp\s+fa-solid|fas|far|fal|fat|fad)\s+/, '').replace(/^fa-/, ''); selectedIcon = 'fa-sharp fa-solid fa-' + selectedIcon"
                                class="flex items-center justify-center gap-2 p-2 rounded-lg border transition-colors text-xs"
                                :class="selectedIcon.startsWith('fa-sharp') ? 'border-primary bg-primary/10 text-primary font-medium' : 'border-input hover:bg-accent'">
                            <i class="fa-sharp fa-solid fa-grid"></i>
                            Sharp
                        </button>
                    </div>
                </div>

                <div class="space-y-4">
                    <label class="text-sm font-medium text-foreground block">Aperçu en temps réel</label>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <div class="flex flex-col items-center justify-center p-4 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900 rounded-xl border border-border shadow-sm">
                            <div class="text-xs text-muted-foreground mb-3 font-medium">BADGE DU RÔLE</div>
                            <div class="inline-flex items-center gap-3 px-4 py-3 rounded-2xl text-white font-semibold shadow-lg transition-all duration-300 hover:scale-105"
                                 :style="`background: ${selectedColor}; box-shadow: 0 8px 25px -8px ${selectedColor}80;`">
                                <div class="flex items-center justify-center w-8 h-8 rounded-full bg-white/20 backdrop-blur-sm">
                                    <i :class="formatIconClass(selectedIcon, 'text-white text-sm')"></i>
                                </div>
                                <span class="text-sm tracking-wide" x-text="roleName || 'Nom du rôle'"></span>
                            </div>
                        </div>

                        <div class="flex flex-col items-center justify-center p-4 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900 rounded-xl border border-border shadow-sm">
                            <div class="text-xs text-muted-foreground mb-3 font-medium">VUE DÉTAILLÉE</div>
                            <div class="w-full max-w-xs bg-background/80 backdrop-blur-sm rounded-2xl border border-border shadow-lg p-4 transition-all duration-300 hover:shadow-xl">
                                <div class="flex items-start space-x-4">
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-white shadow-lg transition-all duration-300 hover:scale-110"
                                             :style="`background: ${selectedColor}; box-shadow: 0 6px 20px -6px ${selectedColor}80;`">
                                            <i :class="formatIconClass(selectedIcon, 'text-white text-lg')"></i>
                                        </div>
                                    </div>

                                    <div class="flex-1 min-w-0 space-y-2">
                                        <div>
                                            <div class="text-xs font-medium text-muted-foreground uppercase tracking-wide">Rôle</div>
                                            <div class="text-sm font-semibold text-foreground truncate"
                                                 x-text="roleName || 'Nom du rôle'"
                                                 :class="{ 'text-muted-foreground italic': !roleName }"></div>
                                        </div>

                                        <div class="grid grid-cols-2 gap-3 text-xs">
                                            <div>
                                                <div class="font-medium text-muted-foreground">Icône</div>
                                                <div class="font-mono text-foreground truncate text-[10px]" x-text="selectedIcon"></div>
                                            </div>
                                            <div>
                                                <div class="font-medium text-muted-foreground">Couleur</div>
                                                <div class="flex items-center space-x-2">
                                                    <div class="w-3 h-3 rounded-full border border-white shadow-inner"
                                                         :style="`background: ${selectedColor}`"></div>
                                                    <span class="font-mono text-foreground truncate text-[10px]" x-text="selectedColor"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-foreground flex items-center justify-between">
                        <span>Permissions</span>
                        <span class="text-xs text-muted-foreground font-normal"
                              x-data
                              x-text="`${$el.closest('.space-y-2').querySelectorAll('input[type=checkbox]:checked').length} sélectionnée(s)`"></span>
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-60 overflow-y-auto p-3 border border-border rounded-lg bg-background/50">
                        @foreach(\App\Models\Permission::all() as $permission)
                            <label class="flex items-center space-x-3 p-2 rounded-lg hover:bg-accent/50 transition-colors cursor-pointer">
                                <input type="checkbox"
                                       name="permissions[]"
                                       value="{{ $permission->id }}"
                                       @checked(in_array($permission->id, $selectedPermissions))
                                       class="h-4 w-4 text-primary border-border rounded focus:ring-2 focus:ring-ring focus:ring-offset-0 transition">
                                <span class="text-sm text-foreground flex-1">{{ $permission->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </form>
        </div>

        <div class="p-4 sm:p-6 border-t border-border bg-muted/20">
            <div class="flex flex-col sm:flex-row gap-3">
                <button type="button"
                        @click="{{ $xModel }} = false"
                        class="inline-flex items-center justify-center gap-2 rounded-lg border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2 text-sm font-medium transition-colors flex-1 sm:flex-none sm:min-w-[120px]">
                    <i class="fas fa-times w-4 h-4"></i>
                    Annuler
                </button>
                <button type="submit"
                        form="roleForm{{ $isEdit ? '_' . $role->id : '_create' }}"
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2 text-sm font-medium transition-colors flex-1">
                    <i class="fas {{ $isEdit ? 'fa-save' : 'fa-plus' }} w-4 h-4"></i>
                    {{ $isEdit ? 'Enregistrer' : 'Créer' }}
                </button>
            </div>
        </div>
    </div>
</div>
