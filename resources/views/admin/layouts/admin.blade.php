<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard')</title>
    <link href="{{ asset('build/assets/admin.css') }}" rel="stylesheet">
</head>
<body class="bg-background text-text font-sans min-h-screen">
<div class="flex">
    <!-- Sidebar -->
    <aside class="w-64 bg-secondary backdrop-blur-lg shadow-glass min-h-screen px-4 py-6">
        <div class="text-2xl font-bold mb-8">Admin Panel</div>
        <nav>
            <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 hover:bg-primary rounded-md">Dashboard</a>
            <a href="{{ route('themes.index') }}" class="block px-4 py-2 hover:bg-primary rounded-md">Themes</a>
            <a href="{{ route('modules.index') }}" class="block px-4 py-2 hover:bg-primary rounded-md">Modules</a>
            <a href="{{ route('admin.settings') }}" class="block px-4 py-2 hover:bg-primary rounded-md">Settings</a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-6">
        <header class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold">@yield('title')</h1>
        </header>
        <div>
            @yield('content')
        </div>
    </main>
</div>
</body>
</html>
