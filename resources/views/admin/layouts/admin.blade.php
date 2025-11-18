<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script>
        (function () {
            try {
                const theme = localStorage.getItem('stratum-theme') || 'dark';
                document.documentElement.classList.add(theme);
            } catch (e) {}
        })();
    </script>

    <title>{{ site_name() ?? "StratumCMS" }} - @yield('title', 'Admin Dashboard')</title>
    @vite(['resources/js/app.js', 'resources/css/admin.css'])
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.css') }}">
    <script src="{{ asset('vendor/fontawesome/js/fontawesome.js') }}"></script>
    <script src="{{ asset('vendor/fontawesome/js/solid.js') }}"></script>
    <script src="{{ asset('vendor/fontawesome/js/sharp-solid.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @stack('head')

</head>
<body class="min-h-screen flex w-full bg-background text-foreground">

@include('admin.partials.sidebar')

<div class="flex-1 min-h-screen md:pl-64 flex flex-col">
    @include('admin.partials.topbar')
    <main class="p-6 animate-fade-in">
        @yield('content')
    </main>
</div>

@stack('scripts')

</body>
</html>
