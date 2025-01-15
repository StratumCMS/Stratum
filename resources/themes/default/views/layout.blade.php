<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Bienvenue')</title>
    <link rel="stylesheet" href="{{ asset(config('theme.assets') . '/css/main.css') }}">
    <script src="{{ asset(config('theme.assets') . '/js/main.js') }}" defer></script>
</head>
<body>
@include('theme::partials.header')

<main>
    @yield('content')
</main>

@include('theme::partials.footer')
</body>
</html>
