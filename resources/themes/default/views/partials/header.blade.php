<style>[x-cloak]{display:none !important}</style>

<a href="#main-content" class="skip-link sr-only focus:not-sr-only focus:absolute focus:top-2 focus:left-2 focus:z-[60] bg-primary text-primary-foreground px-3 py-2 rounded">
    Aller au contenu principal
</a>

<nav
    x-data="{ mobileOpen:false, searchOpen:false }"
    x-init="$store.darkMode.init()"
    class="sticky top-0 z-50 w-full backdrop-blur-xl bg-background/80 border-b border-border/50"
>
    <div class="container mx-auto px-4">
        <div class="flex h-16 items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center space-x-2">
                <img src="{{ site_logo() }}" class="h-8 w-8 rounded-sm" alt="{{ site_name() }}">
                <span class="text-xl font-bold text-primary">{{ site_name() }}</span>
            </a>

            <div class="hidden md:flex items-center space-x-8">
                @foreach($navigationItems as $item)
                    @if($item->isDropdown() && $item->children->isNotEmpty())
                        <div class="relative" x-data="{ open:false }" @keydown.escape.window="open=false">
                            <button @click="open=!open" :aria-expanded="open"
                                    class="text-foreground/70 hover:text-foreground transition-colors text-sm font-medium flex items-center gap-1 relative group">
                                @if($item->icon)<i class="{{ $item->icon }} mr-1"></i>@endif
                                {{ $item->name }}
                                <svg class="h-4 w-4 transform transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                <span class="absolute inset-x-0 -bottom-1 h-0.5 bg-primary transform scale-x-0 group-hover:scale-x-100 transition-transform duration-200 origin-left"></span>
                            </button>
                            <div x-show="open" x-transition @click.outside="open=false"
                                 class="absolute mt-2 w-52 bg-surface/95 supports-[backdrop-filter]:bg-surface/85 backdrop-blur-xl border border-border/60 rounded-xl shadow-2xl z-50 py-1"
                                 role="menu" aria-label="Menu de navigation">
                                @foreach($item->children as $child)
                                    <a href="{{ $child->getLink() }}"
                                       class="block px-4 py-2 text-sm text-foreground/80 hover:bg-surface-hover hover:text-foreground transition"
                                       @if($child->new_tab) target="_blank" rel="noopener noreferrer" @endif>
                                        @if($child->icon)<i class="{{ $child->icon }} mr-1"></i>@endif
                                        {{ $child->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <a href="{{ $item->getLink() }}"
                           class="text-foreground/70 hover:text-foreground transition-colors text-sm font-medium relative group">
                            @if($item->icon)<i class="{{ $item->icon }} mr-1"></i>@endif
                            {{ $item->name }}
                            <span class="absolute inset-x-0 -bottom-1 h-0.5 bg-primary transform scale-x-0 group-hover:scale-x-100 transition-transform duration-200 origin-left"></span>
                        </a>
                    @endif
                @endforeach
            </div>

            <div class="flex items-center space-x-2 md:space-x-4">
                <button
                    @click="searchOpen=!searchOpen"
                    class="md:hidden inline-flex h-9 w-9 items-center justify-center rounded-md border border-border/40 hover:bg-accent backdrop-blur-sm"
                    :aria-expanded="searchOpen" aria-label="Rechercher"
                >
                    <svg class="h-4 w-4 text-foreground/80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                    </svg>
                </button>

                <div class="hidden md:flex items-center">
                    <form action="" method="GET" class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-foreground/50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                        </svg>
                        <input
                            type="search" name="q" placeholder="Rechercher..."
                            class="w-64 h-10 rounded-md border border-border/50 bg-surface/70 px-10 text-sm placeholder:text-foreground/50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        />
                    </form>
                </div>

                <button
                    @click="$store.darkMode.toggle()"
                    class="rounded-full backdrop-blur-sm border border-border/40 hover:bg-accent p-2 transition"
                    aria-label="Basculer le thème"
                >
                    <i x-cloak x-show="!$store.darkMode.enabled" class="fas fa-moon text-gray-700 dark:text-gray-200"></i>
                    <i x-cloak x-show="$store.darkMode.enabled" class="fas fa-sun text-yellow-400"></i>
                </button>

                @auth
                    @php
                        $user = auth()->user();
                        $initials = collect(explode(' ', $user->name))->map(fn($n) => strtoupper(substr($n,0,1)))->implode('');
                        $role = $user->roles->first();
                    @endphp

                    <div x-data="{ open:false }" class="relative" @keydown.escape.window="open=false">
                        <button
                            @click="open = !open; $nextTick(() => $refs.firstMenuItem && $refs.firstMenuItem.focus())"
                            class="group flex items-center gap-3 rounded-full pl-1 pr-3 py-1.5 h-10 backdrop-blur-sm transition text-sm border border-border/40 shadow-sm
                                   hover:bg-accent/60 focus:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                            aria-haspopup="menu" :aria-expanded="open">
                            <span class="relative inline-flex">
                                <span class="h-8 w-8 rounded-full overflow-hidden bg-muted flex items-center justify-center text-white font-medium text-xs uppercase">
                                    @if ($user->avatar_url)
                                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="h-8 w-8 object-cover rounded-full">
                                    @else
                                        {{ $initials }}
                                    @endif
                                </span>
                            </span>
                            <span class="hidden md:flex flex-col items-start leading-tight text-left">
                                <span class="text-sm font-semibold text-foreground">{{ $user->name }}</span>
                                @if ($role)
                                    <span class="text-[11px] text-muted-foreground flex items-center gap-1">
                                        <i class="fas fa-{{ $role->icon }}"></i>{{ $role->name }}
                                    </span>
                                @endif
                            </span>
                            <svg class="h-4 w-4 text-muted-foreground transition-transform duration-200"
                                 :class="{ 'rotate-180': open }" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-show="open" x-transition.origin.top.right @click.outside="open=false"
                             class="absolute right-0 mt-2 w-[22rem]
                                    bg-surface/95 supports-[backdrop-filter]:bg-surface/85 backdrop-blur-2xl
                                    border border-border/70 rounded-2xl shadow-2xl ring-1 ring-black/10 z-50 overflow-hidden"
                             role="menu" aria-label="Menu utilisateur">
                            <div class="relative p-4 border-b border-muted/60">
                                <div class="flex items-center gap-3">
                                    <div class="h-12 w-12 rounded-xl overflow-hidden bg-muted flex items-center justify-center text-white font-medium text-sm uppercase ring-1 ring-border/50">
                                        @if ($user->avatar_url)
                                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="h-12 w-12 object-cover">
                                        @else
                                            {{ $initials }}
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-semibold text-foreground truncate">{{ $user->name }}</div>
                                        <div class="text-xs text-muted-foreground truncate">{{ $user->email }}</div>
                                        @if ($role)
                                            <span class="inline-flex items-center gap-1 mt-1 text-[11px] text-golden px-2 py-0.5 rounded
                                                         bg-gradient-to-r from-primary/10 to-amber-500/10 border border-primary/20">
                                                <i class="fas fa-{{ $role->icon }}"></i>{{ $role->name }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="py-1">
                                <a x-ref="firstMenuItem" href="{{ route('profile') }}"
                                   class="flex items-center gap-3 px-4 py-2.5 text-sm text-foreground/85 hover:text-foreground
                                          hover:bg-surface-hover transition outline-none focus:bg-surface-hover" role="menuitem" tabindex="0">
                                    <i class="fas fa-user w-4 text-muted-foreground"></i> Profil
                                </a>

                                @can('access_dashboard')
                                    <a href="{{ route('admin.dashboard') }}"
                                       class="flex items-center gap-3 px-4 py-2.5 text-sm text-foreground/85 hover:text-foreground
                                              hover:bg-surface-hover transition outline-none focus:bg-surface-hover" role="menuitem" tabindex="-1">
                                        <i class="fa-solid fa-gauge w-4 text-muted-foreground"></i> Administration
                                    </a>
                                @endcan

                                <a href="{{ route('profile.edit') }}"
                                   class="flex items-center gap-3 px-4 py-2.5 text-sm text-foreground/85 hover:text-foreground
                                          hover:bg-surface-hover transition outline-none focus:bg-surface-hover" role="menuitem" tabindex="-1">
                                    <i class="fas fa-cog w-4 text-muted-foreground"></i> Paramètres
                                </a>

                                @can('access_dashboard')
                                    <div class="px-4 py-3">
                                        <div class="grid grid-cols-2 gap-2">
                                            <a href="{{ route('posts.index', ['mine' => 1]) }}"
                                               class="text-center text-sm px-3 py-2 rounded-md bg-surface hover:bg-surface-hover border border-border/50 transition">
                                                Mes articles
                                            </a>
                                            <a href="{{ route('admin.articles.create') }}"
                                               class="text-center text-sm px-3 py-2 rounded-md bg-primary/10 border border-primary/30
                                                      hover:bg-primary/15 text-primary transition">
                                                Nouvel article
                                            </a>
                                        </div>
                                    </div>
                                @endcan

                                <div class="border-t border-muted/60"></div>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                            class="w-full flex items-center justify-center gap-2 px-4 py-2.5 text-sm text-destructive
                                                   hover:bg-destructive/10 transition" role="menuitem">
                                        <i class="fas fa-sign-out-alt w-4"></i> Se déconnecter
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="hidden md:block">
                        <button class="border border-input bg-background hover:bg-accent hover:text-accent-foreground rounded-md h-10 px-4 py-2 text-sm font-medium backdrop-blur-sm">
                            Se connecter
                        </button>
                    </a>
                @endauth

                <button
                    class="md:hidden inline-flex h-10 w-10 items-center justify-center rounded-md border border-border/40 hover:bg-accent backdrop-blur-sm"
                    @click="mobileOpen=!mobileOpen" :aria-expanded="mobileOpen" aria-label="Ouvrir le menu">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div x-show="searchOpen" x-transition class="md:hidden border-t border-border/50">
        <div class="container mx-auto px-4 py-3">
            <form action="" method="GET" class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-foreground/50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                </svg>
                <input
                    type="search" name="q" placeholder="Rechercher des articles..."
                    class="w-full h-10 rounded-md border border-border/50 bg-surface/70 px-10 text-sm placeholder:text-foreground/50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                    autofocus
                />
            </form>
        </div>
    </div>

    <div x-show="mobileOpen" x-transition class="md:hidden border-t border-border/50">
        <div class="container mx-auto px-4 py-3 space-y-1">
            @foreach($navigationItems as $item)
                @if($item->isDropdown() && $item->children->isNotEmpty())
                    <div x-data="{ open:false }" class="rounded-md">
                        <button @click="open=!open" :aria-expanded="open"
                                class="w-full flex items-center justify-between px-3 py-2 text-foreground/70 hover:text-foreground hover:bg-accent/60 rounded-md transition">
                            <span class="flex items-center gap-2">
                                @if($item->icon)<i class="{{ $item->icon }} mr-1"></i>@endif
                                {{ $item->name }}
                            </span>
                            <svg class="h-4 w-4 transform transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" x-transition class="mt-1 pl-3 space-y-1">
                            @foreach($item->children as $child)
                                <a href="{{ $child->getLink() }}"
                                   class="block px-3 py-2 text-sm text-foreground/70 hover:text-primary hover:bg-accent/60 rounded-md transition"
                                   @click="mobileOpen=false"
                                   @if($child->new_tab) target="_blank" rel="noopener noreferrer" @endif>
                                    @if($child->icon)<i class="{{ $child->icon }} mr-1"></i>@endif
                                    {{ $child->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @else
                    <a href="{{ $item->getLink() }}"
                       class="block px-3 py-2 text-foreground/70 hover:text-foreground hover:bg-accent/60 rounded-md transition"
                       @click="mobileOpen=false">
                        @if($item->icon)<i class="{{ $item->icon }} mr-1"></i>@endif
                        {{ $item->name }}
                    </a>
                @endif
            @endforeach

            <hr class="border-border/50 my-3" />
            @auth
                <a href="{{ route('profile') }}" class="block px-3 py-2 text-foreground/70 hover:text-foreground hover:bg-accent/60 rounded-md transition" @click="mobileOpen=false">Profil</a>
            @else
                <a href="{{ route('login') }}" class="block px-3 py-2 text-foreground/70 hover:text-foreground hover:bg-accent/60 rounded-md transition" @click="mobileOpen=false">Se connecter</a>
            @endauth
        </div>
    </div>
</nav>
