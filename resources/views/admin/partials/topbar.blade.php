<header class="h-16 bg-card/50 backdrop-blur-sm border-b border-border flex items-center justify-between px-6 sticky top-0 z-40">
    <div class="flex items-center space-x-4">
        <h1 class="text-2xl font-semibold text-foreground text-glow-purple">@yield('title')</h1>
    </div>
        <div class="flex items-center space-x-4">
            <div class="flex items-center space-x-4">
                <div class="relative hidden md:block">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-muted-foreground w-4 h-4"></i>
                    <input type="text" placeholder="Search..." class="pl-10 pr-4 py-2 bg-muted/50 backdrop-blur-sm rounded-lg border border-border focus:ring-2 focus:ring-primary/20 focus:outline-none focus:border-primary w-64 transition-all">
                </div>

                <button class="relative hover-glow-purple">
                    <i class="fas fa-bell w-5 h-5"></i>
                    <span class="absolute -top-1 -right-1 w-3 h-3 bg-destructive rounded-full text-xs text-white flex items-center justify-center animate-pulse">3</span>
                </button>

                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center glow-purple">
                        <img src="{{ auth()->user()->avatar_url ?? 'https://placehold.co/32' }}"
                             alt="Avatar" class="w-8 h-8 rounded-full">
                    </div>
                    <div class="hidden md:block">
                        <div class="text-sm font-medium">{{ auth()->user()->roles->first()?->name ?? 'No role' }}
                        </div>
                        <div class="text-xs text-muted-foreground">{{ auth()->user()->email }}</div>
                    </div>
                </div>
            </div>
        </div>
</header>
