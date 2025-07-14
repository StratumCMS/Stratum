<!DOCTYPE html>
@include('elements.base')
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="author" content="StratumCMS">

        <title>@yield('title') | {{ site_name() }}</title>
        <meta property="og:title" content="@yield('title')">
        <meta property="og:type" content="@yield('type', 'website')">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:image" content="{{ favicon() }}">
        <meta property="og:description" content="@yield('description', setting('description', ''))">
        <meta property="og:site_name" content="{{ site_name() }}">

        <link rel="shortcut icon" href="{{ favicon() }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <link href="{{ asset('build/assets/default.css') }}" rel="stylesheet">

        @stack('scripts')
    </head>
    <body class="min-h-screen transition-colors duration-300 dark:bg-slate-900 bg-gray-50">
            @include('layouts.navigation')
            <main class="flex-1 container mx-auto px-4 py-8">
                @yield('content')
            </main>
    @include('elements.footer')
    </body>
    <script src="https://kit.fontawesome.com/91664c67de.js" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/alpinejs" defer></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('darkMode', {
                enabled: localStorage.getItem('darkMode') === 'true',
                toggle() {
                    this.enabled = !this.enabled;
                    localStorage.setItem('darkMode', this.enabled);
                    document.documentElement.classList.toggle('dark', this.enabled);
                },
                init() {
                    document.documentElement.classList.toggle('dark', this.enabled);
                }
            });
        });

        (function () {
            try {
                const theme = localStorage.getItem('stratum-theme') || 'dark';
                document.documentElement.classList.add(theme);
            } catch (e) {}
        })();
    </script>
</html>
