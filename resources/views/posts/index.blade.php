@extends('theme::layout')

@section('title', 'Articles')

@section('content')
    <section class="py-20 bg-gray-900 text-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold mb-2">
                    Toutes les <span class="text-gradient bg-gradient-to-r from-purple-400 to-blue-300">actualités</span>
                </h2>
                <p class="text-slate-400 text-lg">Parcourez les dernières publications du serveur.</p>
            </div>

            @if ($posts->isEmpty())
                <p class="text-center text-gray-400">Aucune actualité trouvée.</p>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($posts as $post)
                        <div class="bg-gray-800 rounded-xl overflow-hidden shadow-lg transition hover:scale-105">
                            <img src="{{ $post->thumbnail() ?? 'https://placehold.co/600x400?text=Sans+image' }}" alt="{{ $post->title }}" class="w-full h-48 object-cover">

                            <div class="p-5">
                                <div class="flex items-center text-xs text-slate-400 mb-2">
                                    <span class="text-white font-semibold">{{ $post->author->name ?? 'Anonyme' }}</span>
                                    <span class="mx-2">•</span>
                                    <span>{{ format_date($post->published_at) }}</span>
                                </div>

                                <h3 class="text-lg font-bold text-white mb-2">{{ $post->title }}</h3>

                                <p class="text-slate-400 text-sm mb-4">
                                    {{ Str::limit(strip_tags($post->content), 100) }}
                                </p>

                                <a href="{{ route('posts.show', $post) }}" class="inline-block text-sm font-medium text-white bg-indigo-600 px-4 py-2 rounded hover:bg-indigo-500 transition">
                                    Lire la suite →
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-10">
                    <x-pagination :totalPages="$totalPages" :currentPage="$currentPage" />
                </div>
            @endif
        </div>
    </section>
@endsection
