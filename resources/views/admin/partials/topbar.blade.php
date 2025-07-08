@php use App\Models\ActivityLog; @endphp
<header class="h-16 bg-card/50 backdrop-blur-sm border-b border-border flex items-center justify-between px-6 sticky top-0 z-40">
    <div class="flex items-center space-x-4">
        <h1 class="text-2xl font-semibold text-foreground text-glow-purple">@yield('title')</h1>
    </div>

    <div class="flex items-center space-x-4" x-data="{ notifOpen: false, userOpen: false }">
        {{-- Barre de recherche --}}
        <div class="relative hidden md:block">
            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-muted-foreground w-4 h-4"></i>
            <input type="text" placeholder="Rechercher..."
                   class="pl-10 pr-4 py-2 bg-muted/50 backdrop-blur-sm rounded-lg border border-border focus:ring-2 focus:ring-primary/20 focus:outline-none focus:border-primary w-64 transition-all">
        </div>

        @php
            $activities = $activities ?? ActivityLog::latest()->limit(5)->get();
        @endphp
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 hover:bg-accent hover:text-accent-foreground h-9 px-3 relative hover-glow-purple">
                <i class="fas fa-bell w-5 h-5 text-muted-foreground"></i>
                @if ($activities->count() > 0)
                    <span class="absolute -top-1 -right-1 w-3 h-3 bg-destructive rounded-full text-[10px] text-white flex items-center justify-center animate-pulse">
                        {{ $activities->count() }}
                    </span>
                @endif
            </button>

            <div x-show="open" @click.away="open = false" x-transition
                 class="absolute right-0 mt-2 w-80 bg-popover border border-border rounded-lg shadow-lg overflow-hidden z-50">
                <div class="p-4 border-b border-border flex justify-between items-center">
                    <span class="font-semibold text-foreground">Activités récentes</span>
                </div>
                <ul class="max-h-64 overflow-y-auto divide-y divide-border text-sm">
                    @forelse ($activities as $activity)
                        @php
                            $typeColors = [
                                'page' => 'bg-blue-500',
                                'article' => 'bg-green-500',
                                'module' => 'bg-purple-500',
                                'theme' => 'bg-orange-500',
                                'media' => 'bg-cyan-600',
                                'user' => 'bg-pink-500',
                                'settings' => 'bg-red-500',
                            ];
                        @endphp
                        <li class="p-4 hover:bg-muted/50 transition-colors">
                            <div class="flex flex-col space-y-1">
                                <p class="font-medium text-foreground">
                                 {{ $activity->description }}
                                </p>
                                @if($activity->causer)
                                    <p class="text-muted-foreground text-sm">
                                        Par <strong>{{ $activity->causer->name ?? 'Inconnu' }}</strong>
                                    </p>
                                @endif
                                <p class="text-xs text-muted-foreground">
                                    {{ $activity->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </li>
                    @empty
                        <li class="p-4 text-muted-foreground text-center">Aucune activité récente</li>
                    @endforelse
                </ul>
                <div class="p-3 border-t border-border">

                </div>
            </div>
        </div>

        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0  space-x-3 hover:bg-accent hover:text-accent-foreground h-9 px-3 hover-glow-purple focus:outline-none">
                <div class="w-8 h-8 rounded-full bg-primary overflow-hidden">
                    <img src="{{ auth()->user()->avatar_url ?? 'https://placehold.co/32' }}" alt="Avatar" class="w-8 h-8 object-cover rounded-full">
                </div>
                <div class="hidden md:block text-left">
                    <div class="text-sm font-medium">{{ auth()->user()->name ?? 'Admin' }}</div>
                    <div class="text-xs text-muted-foreground">{{ auth()->user()->email }}</div>
                </div>
                <i class="fas fa-chevron-down text-muted-foreground text-xs"></i>
            </button>

            <div x-show="open" @click.away="open = false" x-transition
                 class="absolute right-0 mt-2 w-56 bg-popover border border-border rounded-md shadow-lg z-50 overflow-hidden">
                <div class="px-4 py-3 border-b border-border">
                    <p class="text-sm font-medium">{{ auth()->user()->name ?? 'Admin' }}</p>
                    <p class="text-xs text-muted-foreground">{{ auth()->user()->email }}</p>
                    @php
                        $role = auth()->user()->roles->first();
                    @endphp

                    @if($role)
                        <div class="mt-2 flex items-center text-sm">
                            <i class="fas fa-{{ $role->icon }} mr-2 text-xs" style="color: {{ $role->color }}"></i>
                            <span class="text-muted-foreground">{{ $role->name }}</span>
                        </div>
                    @endif
                </div>
                <ul class="py-1">
                    <li>
                        <a href="{{ route('admin.profile') }}"
                           class="block px-4 py-2 text-sm text-muted-foreground hover:bg-muted/50 hover:text-foreground">
                            <i class="fas fa-cog mr-2 w-4"></i> Paramètres
                        </a>
                    </li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="w-full text-left px-4 py-2 text-sm text-destructive hover:bg-muted/50">
                                <i class="fas fa-sign-out-alt mr-2 w-4"></i> Se déconnecter
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>

@push('scripts')
    <script src="https://unpkg.com/alpinejs" defer></script>

@endpush
