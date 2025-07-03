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

    <title>@yield('title', 'Admin Dashboard')</title>
    <link href="{{ asset('build/assets/admin.css') }}" rel="stylesheet">
    <script src="https://kit.fontawesome.com/91664c67de.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('assets/js/admin.js') }}"></script>
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
