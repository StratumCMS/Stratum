@extends('admin.layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        <!-- User Count -->
        <div class="bg-card p-6 rounded-card shadow-glass">
            <h3 class="text-xl font-bold">Users</h3>
            <p class="text-lg">{{ $userCount }}</p>
        </div>

        <!-- Theme Count -->
        <div class="bg-card p-6 rounded-card shadow-glass">
            <h3 class="text-xl font-bold">Themes</h3>
            <p class="text-lg">{{ $themeCount }}</p>
        </div>

        <!-- Module Count -->
        <div class="bg-card p-6 rounded-card shadow-glass">
            <h3 class="text-xl font-bold">Modules</h3>
            <p class="text-lg">{{ $moduleCount }}</p>
        </div>
    </div>
@endsection
