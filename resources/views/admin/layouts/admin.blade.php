<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#6366f1">

    <script>
        (function() {
            try {
                const theme = localStorage.getItem('stratum-theme') || 'dark';
                document.documentElement.classList.add(theme);
            } catch (e) {}

            document.addEventListener('DOMContentLoaded', function() {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('sidebar-overlay');

                function toggleBodyScroll(disable) {
                    document.body.style.overflow = disable ? 'hidden' : '';
                }

                const observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        if (mutation.attributeName === 'class') {
                            const isHidden = sidebar.classList.contains('-translate-x-full');
                            toggleBodyScroll(!isHidden);
                            overlay.classList.toggle('hidden', isHidden);
                        }
                    });
                });

                observer.observe(sidebar, { attributes: true });
            });
        })();
    </script>

    <title>{{ site_name() ?? "StratumCMS" }} - @yield('title', 'Admin Dashboard')</title>

    @vite(['resources/js/app.js', 'resources/css/admin.css'])
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.css') }}">
    <script src="{{ asset('vendor/fontawesome/js/fontawesome.js') }}"></script>
    <script src="{{ asset('vendor/fontawesome/js/solid.js') }}"></script>
    <script src="{{ asset('vendor/fontawesome/js/sharp-solid.js') }}"></script>

    @stack('head')
</head>
<body class="min-h-screen flex w-full bg-background text-foreground antialiased">

@include('admin.partials.sidebar')

<div id="sidebar-overlay" class="fixed inset-0 bg-black/60 z-40 lg:hidden hidden transition-opacity duration-300"
     onclick="toggleSidebar()"></div>

<div class="flex-1 min-h-screen lg:ml-64 flex flex-col transition-all duration-300">
    @include('admin.partials.topbar')

    <main class="flex-1 p-4 sm:p-6 lg:p-8 animate-fade-in overflow-x-hidden">
        <div class="max-w-7xl mx-auto w-full">
            @yield('content')
        </div>
    </main>
</div>

@include('admin.partials.search-modal')

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');

        sidebar.classList.toggle('-translate-x-full');

        void sidebar.offsetWidth;
    }

    function handleResize() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');

        if (window.innerWidth >= 1024) {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.add('hidden');
        }
    }

    window.addEventListener('resize', handleResize);

    document.addEventListener('DOMContentLoaded', function() {
        handleResize();

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const sidebar = document.getElementById('sidebar');
                if (window.innerWidth < 1024 && !sidebar.classList.contains('-translate-x-full')) {
                    toggleSidebar();
                }

                const searchModal = document.getElementById('search-modal');
                if (searchModal && !searchModal.classList.contains('hidden')) {
                    toggleSearchModal();
                }
            }
        });
    });

    function toggleSearchModal() {
        const modal = document.getElementById('search-modal');
        const overlay = document.getElementById('search-overlay');
        const input = document.getElementById('search-input');

        modal.classList.toggle('hidden');
        overlay.classList.toggle('hidden');

        if (!modal.classList.contains('hidden')) {
            input.focus();
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = '';
        }
    }

    function performSearch(query) {
        const resultsContainer = document.getElementById('search-results');
        const shortcutsContainer = document.getElementById('search-shortcuts');

        if (query.length < 2) {
            resultsContainer.innerHTML = '';
            shortcutsContainer.classList.remove('hidden');
            return;
        }

        shortcutsContainer.classList.add('hidden');

        const results = [
            { type: 'article', title: 'Article: ' + query, url: '{{ route("admin.articles") }}', icon: 'fa-file-text' },
            { type: 'page', title: 'Page: ' + query, url: '{{ route("admin.pages") }}', icon: 'fa-file' },
            { type: 'user', title: 'Utilisateur: ' + query, url: '{{ route("admin.users") }}', icon: 'fa-user' },
        ];

        resultsContainer.innerHTML = results.map(result => `
            <a href="${result.url}" class="flex items-center p-4 hover:bg-accent/50 rounded-lg transition-colors group">
                <i class="fas ${result.icon} mr-3 text-muted-foreground group-hover:text-primary"></i>
                <span class="text-foreground">${result.title}</span>
            </a>
        `).join('');
    }
</script>

@stack('scripts')
</body>
</html>
