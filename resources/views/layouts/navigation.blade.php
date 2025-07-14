<nav x-data x-init="$store.darkMode.init()" class="backdrop-blur-md bg-background/80 border-b border-border/50 sticky top-0 z-50">
    <div class="container mx-auto px-4 py-4">
        <div class="flex items-center justify-between">

            <a href="{{ route('home') }}" class="flex items-center space-x-2">
                <img src="{{ site_logo() }}" class="h-8 w-8" alt="{{ site_name() }}">
                <span class="text-xl font-bold text-primary">{{ site_name() }}</span>
            </a>

            <div class="hidden md:flex items-center space-x-6">
                @foreach($navigationItems as $item)
                    @if($item->isDropdown() && $item->children->isNotEmpty())
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open"
                                    class="text-foreground/70 hover:text-primary transition-colors text-sm font-medium flex items-center gap-1">
                                @if($item->icon)<i class="{{ $item->icon }} mr-1"></i>@endif
                                {{ $item->name }}
                                <svg class="h-4 w-4 transform transition-transform duration-200"
                                     :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" stroke-width="2"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div x-show="open" x-transition @click.away="open = false"
                                 class="absolute mt-2 w-48 bg-background/80 border border-border/50 rounded shadow-lg z-50 py-1">
                                @foreach($item->children as $child)
                                    <a href="{{ $child->getLink() }}"
                                       class="block px-4 py-2 text-sm text-foreground/70 hover:bg-accent hover:text-primary transition"
                                       @if($child->new_tab) target="_blank" rel="noopener noreferrer" @endif>
                                        @if($child->icon)<i class="{{ $child->icon }} mr-1"></i>@endif
                                        {{ $child->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <a href="{{ $item->value }}"
                           class="flex items-center space-x-2 text-foreground/70 hover:text-primary transition-colors">
                            @if($item->icon)<i class="{{ $item->icon }} mr-1"></i>@endif
                            {{ $item->name }}
                        </a>
                    @endif
                @endforeach
            </div>

            <div class="flex items-center space-x-4">

                <button @click="$store.darkMode.toggle()"
                        class="rounded-full backdrop-blur-sm border border-border/40 hover:bg-accent p-2 transition"
                        aria-label="Toggle dark mode">
                    <i x-show="!$store.darkMode.enabled" class="fas fa-moon text-gray-700 dark:text-gray-200"></i>
                    <i x-show="$store.darkMode.enabled" class="fas fa-sun text-yellow-400"></i>
                </button>


                @auth
                    @php
                        $user = auth()->user();
                        $initials = collect(explode(' ', $user->name))->map(fn($n) => strtoupper(substr($n, 0, 1)))->implode('');
                        $role = $user->roles->first();
                    @endphp

                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open"
                                class="flex items-center space-x-3 rounded-full px-3 py-1.5 h-10 backdrop-blur-sm hover:bg-accent/60 transition text-sm border border-border/40 shadow-sm">
                            <div class="h-8 w-8 rounded-full overflow-hidden bg-muted flex items-center justify-center text-white font-medium text-xs uppercase">
                                @if ($user->avatar_url)
                                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="h-8 w-8 object-cover rounded-full">
                                @else
                                    {{ $initials }}
                                @endif
                            </div>
                            <div class="hidden md:flex flex-col items-start leading-tight text-left">
                                <span class="text-sm font-semibold text-foreground">{{ $user->name }}</span>
                                @if ($role)
                                    <span class="text-xs flex items-center gap-1 text-muted-foreground">
                                        <i class="fas fa-{{ $role->icon }}" style="color: {{ $role->color }}"></i>
                                        {{ $role->name }}
                                    </span>
                                @endif
                            </div>
                        </button>

                        <div x-show="open" x-transition @click.away="open = false"
                             class="absolute right-0 mt-2 w-64 bg-background/90 backdrop-blur-xl border border-border/50 rounded-xl shadow-xl z-50 py-3 text-sm space-y-1">
                            <div class="px-4 pb-2 border-b border-muted">
                                <div class="flex items-center space-x-3">
                                    <div class="h-10 w-10 rounded-full overflow-hidden bg-muted flex items-center justify-center text-white font-medium text-sm uppercase">
                                        @if ($user->avatar_url)
                                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="h-10 w-10 object-cover rounded-full">
                                        @else
                                            {{ $initials }}
                                        @endif
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-semibold text-foreground">{{ $user->name }}</span>
                                        <span class="text-xs text-muted-foreground">{{ $user->email }}</span>
                                        @if ($role)
                                            <span class="text-xs flex items-center gap-1 text-muted-foreground mt-1">
                                                <i class="fas fa-{{ $role->icon }}" style="color: {{ $role->color }}"></i>
                                                {{ $role->name }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>


                            <div class="py-1 space-y-1">
                                <a href="{{ route('profile') }}"
                                   class="flex items-center gap-2 px-4 py-2 hover:bg-accent/50 rounded-md transition">
                                    <i class="fas fa-user w-4 text-muted-foreground"></i>
                                    <span>Profil</span>
                                </a>

                                @can('access_dashboard')
                                <a href="{{ route('admin.dashboard') }}"
                                   class="flex items-center gap-2 px-4 py-2 hover:bg-accent/50 rounded-md transition">
                                    <i class="fa-solid fa-gauge w-4 text-muted-foreground"></i>
                                    <span>Administraion</span>
                                </a>
                                @endcan
                                <a href="{{ route('profile.edit') }}"
                                   class="flex items-center gap-2 px-4 py-2 hover:bg-accent/50 rounded-md transition">
                                    <i class="fas fa-cog w-4 text-muted-foreground"></i>
                                    <span>Paramètres</span>
                                </a>
                            </div>

                            <div class="pt-2 border-t border-muted">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                            class="w-full flex items-center gap-2 px-4 py-2 text-destructive hover:bg-accent/40 hover:text-destructive rounded-md transition">
                                        <i class="fas fa-sign-out-alt w-4"></i>
                                        <span>Se déconnecter</span>
                                    </button>
                                </form>
                            </div>

                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}">
                        <button class="border border-input bg-background hover:bg-accent hover:text-accent-foreground rounded-md h-10 px-4 py-2 text-sm font-medium backdrop-blur-sm">
                            Se connecter
                        </button>
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>
