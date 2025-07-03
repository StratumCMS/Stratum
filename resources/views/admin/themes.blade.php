@extends('admin.layouts.admin')

@section('title', 'Themes')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold">Themes</h2>
            <form action="{{ route('themes.scan') }}" method="POST">
                @csrf
                <button class="bg-primary hover:bg-primary/90 text-white px-4 py-2 rounded-lg shadow">Scan/Add</button>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($themes as $theme)
                <div class="bg-card/50 backdrop-blur-sm border border-white/20 p-6 rounded-card shadow-glass flex flex-col">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-bold">{{ $theme->name }}</h3>
                            <p class="text-sm text-gray-300">v{{ $theme->version }} • {{ $theme->author }}</p>
                        </div>
                        @if($theme->active)
                            <span class="bg-success text-black px-2 py-1 text-xs rounded">Active</span>
                        @endif
                    </div>
                    <p class="flex-1 text-gray-300 mb-4">{{ Str::limit($theme->description, 100) }}</p>
                    <div class="flex gap-2">
                        @if(!$theme->active)
                            <form action="{{ route('themes.activate', $theme->slug) }}" method="POST">
                                @csrf
                                <button class="flex-1 bg-accent/80 hover:bg-accent text-white px-3 py-2 rounded">Activate</button>
                            </form>
                        @endif
                        <form action="{{ route('themes.deactivate', $theme->slug) }}" method="POST">
                            @csrf
                            <button class="flex-1 bg-destructive/70 hover:bg-destructive text-white px-3 py-2 rounded">
                                Deactivate
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
