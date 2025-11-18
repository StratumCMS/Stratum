<aside id="sidebar"
       class="fixed inset-y-0 left-0 z-50 w-64 bg-sidebar border-r border-sidebar-border transform transition-transform duration-300 ease-in-out lg:translate-x-0 -translate-x-full flex flex-col">

    <div class="h-16 flex items-center justify-between px-4 border-b border-sidebar-border shrink-0 bg-sidebar/95 backdrop-blur-sm">
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center shrink-0"
                 aria-hidden="true">
                <span class="text-white font-bold text-sm">S</span>
            </div>
            <span class="text-xl font-bold text-sidebar-foreground">Stratum</span>
        </div>

        <button class="lg:hidden p-2 rounded-lg hover:bg-sidebar-accent/50 transition-colors focus:outline-none focus:ring-2 focus:ring-primary/20"
                onclick="toggleSidebar()"
                aria-label="Fermer le menu de navigation">
            <i class="fas fa-times text-lg text-sidebar-foreground" aria-hidden="true"></i>
        </button>
    </div>

    <div class="flex-1 overflow-y-auto">
        <nav class="p-4 space-y-6" aria-label="Navigation principale">

            <div>
                <h3 class="px-2 text-xs font-semibold text-sidebar-foreground/60 uppercase tracking-wider mb-3">
                    Navigation
                </h3>
                <div class="space-y-1">
                    @foreach ($navigationItems as $item)
                        <a href="{{ route($item['route']) }}"
                           class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 group
                                  {{ request()->routeIs($item['route'].'*') ? 'bg-sidebar-accent text-sidebar-primary' : 'text-sidebar-foreground hover:bg-sidebar-accent/50 hover:text-sidebar-primary' }}"
                           aria-current="{{ request()->routeIs($item['route'].'*') ? 'page' : 'false' }}">
                            <i class="fas {{ $item['icon'] }} mr-3 h-4 w-4 flex-shrink-0 group-hover:scale-110 transition-transform" aria-hidden="true"></i>
                            <span class="truncate">{{ $item['label'] }}</span>
                            @if(request()->routeIs($item['route'].'*'))
                                <div class="ml-auto w-2 h-2 bg-sidebar-primary rounded-full"></div>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>

            @if (!empty($moduleNavigationItems))
                <div class="border-t border-sidebar-border pt-6">
                    <h3 class="px-2 text-xs font-semibold text-sidebar-foreground/60 uppercase tracking-wider mb-3">
                        Modules
                    </h3>
                    <div class="space-y-1">
                        @foreach ($moduleNavigationItems as $item)
                            @if ($item['type'] === 'dropdown')
                                <div class="space-y-1" x-data="{ open: {{ request()->routeIs(collect($item['items'])->pluck('route')->map(function($route) { return $route.'*'; })->toArray()) ? 'true' : 'false' }} }">
                                    <button class="flex items-center w-full px-3 py-3 text-sm font-medium rounded-lg text-sidebar-foreground hover:bg-sidebar-accent/50 transition-colors focus:outline-none focus:ring-2 focus:ring-primary/20 group"
                                            @click="open = !open"
                                            :aria-expanded="open"
                                            aria-controls="dropdown-{{ Str::slug($item['label']) }}">
                                        <i class="{{ $item['icon'] ?? 'fas fa-puzzle-piece' }} mr-3 h-4 w-4 flex-shrink-0 group-hover:scale-110 transition-transform" aria-hidden="true"></i>
                                        <span class="truncate flex-1 text-left">{{ $item['label'] }}</span>
                                        <i class="fas fa-chevron-down text-xs transition-transform duration-200 ml-2 flex-shrink-0"
                                           :class="{ 'rotate-180': open }" aria-hidden="true"></i>
                                    </button>
                                    <div id="dropdown-{{ Str::slug($item['label']) }}"
                                         class="ml-4 space-y-1 transition-all duration-200 overflow-hidden border-l border-sidebar-border/50"
                                         x-show="open"
                                         x-collapse.duration.300ms>
                                        @foreach ($item['items'] as $subItem)
                                            <a href="{{ route($subItem['route']) }}"
                                               class="flex items-center px-3 py-2 text-sm rounded-lg text-sidebar-foreground hover:bg-sidebar-accent/50 transition-colors group ml-2
                                                      {{ request()->routeIs($subItem['route'].'*') ? 'bg-sidebar-accent/30 text-sidebar-primary border-l-2 border-sidebar-primary' : '' }}">
                                                <i class="{{ $subItem['icon'] ?? 'fas fa-circle' }} mr-2 text-xs flex-shrink-0 group-hover:scale-110 transition-transform" aria-hidden="true"></i>
                                                <span class="truncate">{{ $subItem['label'] }}</span>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <a href="{{ route($item['route']) }}"
                                   class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 group
                                          {{ request()->routeIs($item['route'].'*') ? 'bg-sidebar-accent text-sidebar-primary' : 'text-sidebar-foreground hover:bg-sidebar-accent/50 hover:text-sidebar-primary' }}"
                                   aria-current="{{ request()->routeIs($item['route'].'*') ? 'page' : 'false' }}">
                                    <i class="fas {{ $item['icon'] ?? 'fa-puzzle-piece' }} mr-3 h-4 w-4 flex-shrink-0 group-hover:scale-110 transition-transform" aria-hidden="true"></i>
                                    <span class="truncate">{{ $item['label'] }}</span>
                                    @if(request()->routeIs($item['route'].'*'))
                                        <div class="ml-auto w-2 h-2 bg-sidebar-primary rounded-full"></div>
                                    @endif
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

        </nav>
    </div>

    <div class="p-4 border-t border-sidebar-border mt-auto shrink-0 bg-sidebar/95 backdrop-blur-sm">
        <a href="{{ url('/') }}"
           class="flex items-center px-3 py-3 rounded-lg text-sidebar-foreground hover:bg-primary/20 transition-colors group focus:outline-none focus:ring-2 focus:ring-primary/20">
            <i class="fas fa-house mr-3 h-4 w-4 flex-shrink-0 group-hover:scale-110 transition-transform" aria-hidden="true"></i>
            <span class="font-medium">Retour au site</span>
        </a>
    </div>
</aside>

<button class="lg:hidden fixed top-4 left-4 z-40 p-3 bg-primary text-white rounded-xl shadow-2xl focus:outline-none focus:ring-2 focus:ring-primary/20 transition-transform active:scale-95"
        onclick="toggleSidebar()"
        aria-label="Ouvrir le menu de navigation">
    <i class="fas fa-bars text-lg" aria-hidden="true"></i>
</button>
