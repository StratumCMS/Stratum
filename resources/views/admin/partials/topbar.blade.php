@php use App\Models\ActivityLog; @endphp
<header class="h-16 bg-card/80 backdrop-blur-lg border-b border-border flex items-center justify-between px-4 sm:px-6 sticky top-0 z-30 supports-backdrop-blur:bg-card/60">

    <div class="flex items-center space-x-4 min-w-0 flex-1">
        <button class="lg:hidden p-2 rounded-lg hover:bg-accent transition-colors focus:outline-none focus:ring-2 focus:ring-primary/20 mr-2"
                onclick="toggleSidebar()"
                aria-label="Ouvrir le menu">
            <i class="fas fa-bars text-lg text-foreground" aria-hidden="true"></i>
        </button>

        <h1 class="text-xl font-semibold text-foreground truncate">
            @yield('title', 'Tableau de bord')
        </h1>
    </div>

    <div class="flex items-center space-x-2 sm:space-x-3">

        <button class="sm:hidden p-2 rounded-lg hover:bg-accent transition-colors focus:outline-none focus:ring-2 focus:ring-primary/20"
                onclick="toggleSearchModal()"
                aria-label="Ouvrir la recherche">
            <i class="fas fa-search w-5 h-5 text-muted-foreground" aria-hidden="true"></i>
        </button>

        <div class="hidden sm:block relative">
            <button onclick="toggleSearchModal()"
                    class="flex items-center space-x-3 px-4 py-2 bg-muted/50 hover:bg-muted/70 backdrop-blur-sm rounded-xl border border-border focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all duration-200 group w-64">
                <i class="fas fa-search text-muted-foreground group-hover:text-foreground w-4 h-4 transition-colors" aria-hidden="true"></i>
                <span class="text-muted-foreground group-hover:text-foreground transition-colors flex-1 text-left">Rechercher...</span>
                <kbd class="hidden lg:inline-flex items-center gap-1 rounded border bg-background px-1.5 font-mono text-[10px] font-medium text-muted-foreground opacity-100">
                    ⌘K
                </kbd>
            </button>
        </div>

        @php
            $activities = $activities ?? ActivityLog::latest()->limit(5)->get();
            $unreadCount = $activities->count();
        @endphp

        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open"
                    class="relative p-2 rounded-lg hover:bg-accent transition-colors focus:outline-none focus:ring-2 focus:ring-primary/20 group"
                    :aria-expanded="open"
                    aria-label="Notifications"
                    aria-controls="notifications-panel">
                <i class="fas fa-bell w-5 h-5 text-muted-foreground group-hover:text-foreground transition-colors" aria-hidden="true"></i>
                @if ($unreadCount > 0)
                    <span class="absolute -top-1 -right-1 w-3 h-3 bg-destructive rounded-full text-[10px] text-white flex items-center justify-center animate-pulse ring-2 ring-background">
                        {{ $unreadCount }}
                    </span>
                @endif
            </button>

            <div x-show="open"
                 @click.away="open = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 id="notifications-panel"
                 class="absolute right-0 mt-2 w-80 sm:w-96 bg-popover border border-border rounded-xl shadow-2xl overflow-hidden z-50 backdrop-blur-sm"
                 role="dialog"
                 aria-label="Panneau des notifications">

                <div class="p-4 border-b border-border bg-popover/95">
                    <div class="flex items-center justify-between">
                        <span class="font-semibold text-foreground">Activités récentes</span>
                        @if ($unreadCount > 0)
                            <span class="text-xs bg-primary text-primary-foreground px-2 py-1 rounded-full">
                                {{ $unreadCount }} nouvelle(s)
                            </span>
                        @endif
                    </div>
                </div>

                <div class="max-h-80 overflow-y-auto divide-y divide-border">
                    @forelse ($activities as $activity)
                        <div class="p-4 hover:bg-accent/30 transition-colors cursor-pointer group border-l-4 border-primary/50"
                             onclick="window.location='{{ route('admin.dashboard') }}'">
                            <div class="flex items-start space-x-3">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-foreground leading-relaxed">
                                        {{ $activity->description }}
                                    </p>
                                    <div class="flex items-center mt-2 space-x-4 text-xs text-muted-foreground">
                                        @if($activity->causer)
                                            <span class="flex items-center">
                                                <i class="fas fa-user mr-1" aria-hidden="true"></i>
                                                {{ $activity->causer->name ?? 'Inconnu' }}
                                            </span>
                                        @endif
                                        <span class="flex items-center">
                                            <i class="fas fa-clock mr-1" aria-hidden="true"></i>
                                            {{ $activity->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-muted-foreground">
                            <i class="fas fa-bell-slash text-2xl mb-3 opacity-50" aria-hidden="true"></i>
                            <p>Aucune activité récente</p>
                        </div>
                    @endforelse
                </div>

                <div class="p-3 border-t border-border bg-muted/20">
                    <a href="{{ route('admin.dashboard') }}"
                       class="block text-center text-sm text-primary hover:text-primary/80 font-medium py-2 transition-colors rounded-lg hover:bg-accent/50">
                        Voir toutes les activités
                    </a>
                </div>
            </div>
        </div>

        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open"
                    class="flex items-center space-x-3 p-2 rounded-xl hover:bg-accent transition-colors focus:outline-none focus:ring-2 focus:ring-primary/20 group min-w-0"
                    :aria-expanded="open"
                    aria-label="Menu utilisateur"
                    aria-controls="user-menu">

                <div class="w-8 h-8 rounded-full bg-primary overflow-hidden ring-2 ring-background flex-shrink-0">
                    <img src="{{ auth()->user()->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name ?? 'Admin') . '&background=6366f1&color=fff' }}"
                         alt="Avatar de {{ auth()->user()->name }}"
                         class="w-8 h-8 object-cover rounded-full"
                         loading="lazy">
                </div>

                <div class="hidden lg:block text-left min-w-0 flex-1">
                    <div class="text-sm font-medium truncate">{{ auth()->user()->name ?? 'Admin' }}</div>
                    <div class="text-xs text-muted-foreground truncate">{{ auth()->user()->email }}</div>
                </div>

                <i class="fas fa-chevron-down text-muted-foreground text-xs flex-shrink-0 transition-transform duration-200"
                   :class="{ 'rotate-180': open }"
                   aria-hidden="true"></i>
            </button>

            <div x-show="open"
                 @click.away="open = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 id="user-menu"
                 class="absolute right-0 mt-2 w-64 bg-popover border border-border rounded-xl shadow-2xl z-50 overflow-hidden backdrop-blur-sm"
                 role="menu"
                 aria-label="Menu utilisateur">

                <div class="p-4 border-b border-border bg-popover/95">
                    <p class="text-sm font-medium truncate">{{ auth()->user()->name ?? 'Admin' }}</p>
                    <p class="text-xs text-muted-foreground truncate mt-1">{{ auth()->user()->email }}</p>

                    @php
                        $role = auth()->user()->roles->first();
                    @endphp

                    @if($role)
                        <div class="mt-3 flex items-center text-sm px-2 py-1 bg-accent/30 rounded-lg">
                            <i class="fas fa-{{ $role->icon ?? 'user' }} mr-2 text-xs" style="color: {{ $role->color ?? '#6b7280' }}" aria-hidden="true"></i>
                            <span class="text-muted-foreground truncate">{{ $role->name }}</span>
                        </div>
                    @endif
                </div>

                <div class="py-2">
                    <a href="{{ route('admin.profile') }}"
                       class="flex items-center px-4 py-3 text-sm text-foreground hover:bg-accent/50 transition-colors group focus:outline-none focus:bg-accent/50">
                        <i class="fas fa-cog mr-3 w-4 group-hover:rotate-90 transition-transform" aria-hidden="true"></i>
                        Paramètres du compte
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button type="submit"
                                class="flex items-center w-full text-left px-4 py-3 text-sm text-destructive hover:bg-accent/50 transition-colors focus:outline-none focus:bg-accent/50">
                            <i class="fas fa-sign-out-alt mr-3 w-4" aria-hidden="true"></i>
                            Se déconnecter
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
