<!DOCTYPE html>
@include('elements.base')
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="author" content="StratumCMS">

        <title>@yield('title', 'StratumCMS')</title>
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

        <link href="{{ asset('build/assets/app-Bp7TMlvl.css') }}" rel="stylesheet">

        @stack('scripts')
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')

            @if (isset($header))
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <main>
                @yield('content')
            </main>
        </div>
    </body>
    <script src="https://kit.fontawesome.com/91664c67de.js" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/alpinejs" defer></script>
</html>
