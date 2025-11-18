<div id="search-modal" class="fixed inset-0 z-50 hidden">
    <div id="search-overlay" class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="toggleSearchModal()"></div>

    <div class="absolute top-20 left-1/2 transform -translate-x-1/2 w-full max-w-2xl mx-4">
        <div class="bg-popover border border-border rounded-2xl shadow-2xl overflow-hidden backdrop-blur-sm">
            <div class="p-4 border-b border-border bg-popover/95">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-muted-foreground w-4 h-4" aria-hidden="true"></i>
                    <input type="text"
                           id="search-input"
                           placeholder="Rechercher dans l'administration..."
                           class="w-full pl-10 pr-4 py-3 bg-transparent border-none focus:outline-none focus:ring-0 text-foreground placeholder-muted-foreground text-lg"
                           oninput="performSearch(this.value)"
                           onkeydown="handleSearchKeydown(event)">
                    <button onclick="toggleSearchModal()"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 p-1 rounded-lg hover:bg-accent transition-colors">
                        <i class="fas fa-times text-muted-foreground w-4 h-4" aria-hidden="true"></i>
                    </button>
                </div>
            </div>

            <div id="search-shortcuts" class="p-6">
                <h3 class="text-sm font-semibold text-muted-foreground mb-4">Raccourcis rapides</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <a href="{{ route('admin.articles') }}"
                       class="flex items-center p-4 rounded-xl bg-accent/30 hover:bg-accent/50 transition-colors group">
                        <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">
                            <i class="fas fa-file-text text-white text-sm" aria-hidden="true"></i>
                        </div>
                        <div>
                            <div class="font-medium text-foreground">Articles</div>
                            <div class="text-xs text-muted-foreground">Gérer les articles</div>
                        </div>
                    </a>

                    <a href="{{ route('admin.pages') }}"
                       class="flex items-center p-4 rounded-xl bg-accent/30 hover:bg-accent/50 transition-colors group">
                        <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">
                            <i class="fas fa-file text-white text-sm" aria-hidden="true"></i>
                        </div>
                        <div>
                            <div class="font-medium text-foreground">Pages</div>
                            <div class="text-xs text-muted-foreground">Gérer les pages</div>
                        </div>
                    </a>

                    <a href="{{ route('admin.media') }}"
                       class="flex items-center p-4 rounded-xl bg-accent/30 hover:bg-accent/50 transition-colors group">
                        <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">
                            <i class="fas fa-image text-white text-sm" aria-hidden="true"></i>
                        </div>
                        <div>
                            <div class="font-medium text-foreground">Médias</div>
                            <div class="text-xs text-muted-foreground">Gérer les fichiers</div>
                        </div>
                    </a>

                    <a href="{{ route('admin.users') }}"
                       class="flex items-center p-4 rounded-xl bg-accent/30 hover:bg-accent/50 transition-colors group">
                        <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">
                            <i class="fas fa-users text-white text-sm" aria-hidden="true"></i>
                        </div>
                        <div>
                            <div class="font-medium text-foreground">Utilisateurs</div>
                            <div class="text-xs text-muted-foreground">Gérer les utilisateurs</div>
                        </div>
                    </a>

                    <a href="{{ route('themes.index') }}"
                       class="flex items-center p-4 rounded-xl bg-accent/30 hover:bg-accent/50 transition-colors group">
                        <div class="w-10 h-10 bg-pink-500 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">
                            <i class="fas fa-palette text-white text-sm" aria-hidden="true"></i>
                        </div>
                        <div>
                            <div class="font-medium text-foreground">Thèmes</div>
                            <div class="text-xs text-muted-foreground">Gérer les thèmes</div>
                        </div>
                    </a>

                    <a href="{{ route('modules.index') }}"
                       class="flex items-center p-4 rounded-xl bg-accent/30 hover:bg-accent/50 transition-colors group">
                        <div class="w-10 h-10 bg-cyan-500 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">
                            <i class="fas fa-puzzle-piece text-white text-sm" aria-hidden="true"></i>
                        </div>
                        <div>
                            <div class="font-medium text-foreground">Modules</div>
                            <div class="text-xs text-muted-foreground">Gérer les modules</div>
                        </div>
                    </a>
                </div>
            </div>

            <div id="search-results" class="hidden max-h-96 overflow-y-auto"></div>

            <div class="p-4 border-t border-border bg-muted/20 text-center">
                <div class="text-xs text-muted-foreground">
                    Appuyez sur <kbd class="inline-flex items-center gap-1 rounded border bg-background px-1.5 font-mono text-[10px] font-medium">Esc</kbd> pour fermer
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function handleSearchKeydown(event) {
        if (event.key === 'Escape') {
            toggleSearchModal();
        } else if (event.key === 'Enter' && event.target.value.trim()) {
            window.location.href = '{{ route("admin.articles") }}?search=' + encodeURIComponent(event.target.value);
        }
    }

    document.addEventListener('keydown', function(event) {
        if ((event.metaKey || event.ctrlKey) && event.key === 'k') {
            event.preventDefault();
            toggleSearchModal();
        }
    });
</script>
