<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Bienvenue')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ theme_asset('js/script.js') }}" defer></script>
</head>
<body>
@include('theme::partials.header')

<main>
    @yield('content')
</main>

@include('theme::partials.footer')
<script src="https://unpkg.com/alpinejs" defer></script>
</body>
</html>
