<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | {{ site_name() }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="@yield('description', setting('description', ''))">

    <link href="{{ theme_asset('css/output.css') }}" rel="stylesheet">
    <link href="{{ theme_asset('css/custom.css') }}" rel="stylesheet">

    <meta property="og:title" content="@yield('title')">
    <meta property="og:type" content="@yield('type', 'website')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ favicon() }}">
    <meta property="og:description" content="@yield('description', setting('description', ''))">
    <meta property="og:site_name" content="{{ site_name() }}">
    @stack('meta')

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="shortcut icon" href="{{ favicon() }}">

    @stack('scripts')

    @stack('styles')
</head>
<body>
<header>
    @include('theme::partials.header')
</header>

@yield('content')

@include('theme::partials.footer')
<script src="https://unpkg.com/alpinejs" defer></script>
<script src="https://kit.fontawesome.com/91664c67de.js" crossorigin="anonymous"></script>
</body>
</html>
