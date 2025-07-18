<aside id="sidebar"
       class="w-64 bg-sidebar border-r border-sidebar-border h-screen fixed left-0 top-0 z-30">
    <div class="flex flex-col h-full">
        {{-- Header --}}
        <div class="h-16 flex items-center px-6 border-b border-sidebar-border">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center">
                    <span class="text-white font-bold text-sm">S</span>
                </div>
                <span class="text-xl font-bold text-sidebar-foreground">Stratum</span>
            </div>
        </div>

        {{-- Mobile close button --}}
        <button class="lg:hidden p-2 focus:outline-none"
                onclick="document.getElementById('sidebar').classList.add('-translate-x-full')">
            <i class="fas fa-times text-xl dark:text-white"></i>
        </button>

        {{-- Navigation --}}
        <nav class="flex-1 px-4 py-6 space-y-6 overflow-y-auto">

            {{-- Group: Navigation --}}
            <div>
                <h3 class="duration-200 flex h-8 shrink-0 items-center rounded-md px-2 text-xs font-medium text-sidebar-foreground/70 outline-none ring-sidebar-ring transition-[margin,opa] ease-linear focus-visible:ring-2 [&>svg]:size-4 [&>svg]:shrink-0 group-data-[collapsible=icon]:-mt-8 group-data-[collapsible=icon]:opacity-0">
                    Navigation
                </h3>
                <div class="space-y-2">
                    @foreach ($navigationItems as $item)
                        <a href="{{ route($item['route']) }}"
                           class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                           {{ request()->routeIs($item['route'].'*') ? 'bg-sidebar-accent text-sidebar-primary' : 'text-sidebar-foreground hover:bg-sidebar-accent/50 hover:text-sidebar-primary' }}">
                            <i class="fas {{ $item['icon'] }} mr-3 h-5 w-5"></i>
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Group: Modules --}}
            @if (!empty($moduleNavigationItems))
                <div class="border-t border-sidebar-border pt-4">
                    <h3 class="text-xs font-semibold text-sidebar-foreground/60 uppercase tracking-wider mb-2">
                        Modules
                    </h3>
                    <div class="space-y-2">
                        @foreach ($moduleNavigationItems as $item)
                            @if ($item['type'] === 'dropdown')
                                <div class="space-y-1">
                                    <button class="flex items-center w-full px-3 py-2.5 text-sm font-medium rounded-lg text-sidebar-foreground hover:bg-sidebar-accent/50 transition"
                                            onclick="this.nextElementSibling.classList.toggle('hidden')">
                                        <i class="fas {{ $item['icon'] ?? 'fa-puzzle-piece' }} mr-3"></i>
                                        <span>{{ $item['label'] }}</span>
                                        <i class="ml-auto fas fa-chevron-down text-xs"></i>
                                    </button>
                                    <div class="ml-4 hidden">
                                        @foreach ($item['items'] as $subItem)
                                            <a href="{{ route($subItem['route']) }}"
                                               class="flex items-center px-3 py-2 text-sm rounded-md text-sidebar-foreground hover:bg-sidebar-accent/50 transition">
                                                <i class="fas {{ $subItem['icon'] ?? 'fa-circle' }} mr-2 text-xs"></i>
                                                <span>{{ $subItem['label'] }}</span>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <a href="{{ route($item['route']) }}"
                                   class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
               {{ request()->routeIs($item['route'].'*') ? 'bg-sidebar-accent text-sidebar-primary' : 'text-sidebar-foreground hover:bg-sidebar-accent/50 hover:text-sidebar-primary' }}">
                                    <i class="fas {{ $item['icon'] ?? 'fa-puzzle-piece' }} mr-3 h-5 w-5"></i>
                                    <span>{{ $item['label'] }}</span>
                                </a>
                            @endif
                        @endforeach

                    </div>
                </div>
            @endif

        </nav>

        {{-- Dark mode --}}
        <div class="px-4 py-4 border-t border-sidebar-border mt-auto">
            <button onclick="toggleTheme()"
                    class="flex w-full items-center px-3 py-2 rounded-lg text-gray-700 dark:text-gray-200 hover:bg-primary/20 dark:hover:bg-primary/40 transition-colors"
                    id="theme-toggle">
                <span class="ml-3">...</span>
            </button>
        </div>
    </div>
</aside>

{{-- Mobile open button --}}
<button class="lg:hidden fixed top-4 left-4 z-40 p-2 bg-white dark:bg-[#1e293b] rounded-lg shadow"
        onclick="document.getElementById('sidebar').classList.toggle('-translate-x-full')">
    <i class="fas fa-bars text-xl dark:text-white"></i>
</button>
