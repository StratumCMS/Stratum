@extends('theme::layout')

@section('title', 'Articles')

@section('content')
    <section class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="font-title text-4xl md:text-5xl font-bold text-white mb-4">
                    TOUTES LES <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-blue-300">ACTUALITÉS</span>
                </h2>
                <p class="text-xl text-slate-400 max-w-3xl mx-auto">
                    Parcourez toutes les publications et restez à jour avec le serveur.
                </p>
            </div>

            @php
                $perPage = 3;

                $currentPage = request()->query('page', 1);

                $paginatedPosts = $posts->slice(($currentPage - 1) * $perPage, $perPage);

                $totalPages = ceil($posts->count() / $perPage);
            @endphp
            @if ($posts->isEmpty())
                <div class="text-center text-gray-400">Aucune actualité trouvée.</div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
                    @foreach ($paginatedPosts as $post)
                        <div class="block-bg rounded-xl overflow-hidden news-card transition-all duration-300">
                            <div class="h-48 bg-gradient-to-r from-primary to-secondary flex items-center justify-center">
                                <img src="{{ $post->thumbnail() ?? 'https://placehold.co/600x400?text=Sans+image' }}"
                                     alt="{{ $post->title }}" class="w-full h-48 object-cover">
                            </div>
                            <div class="p-6">
                                <div class="flex items-center text-sm text-slate-400 mb-2">
                                    <span class="bg-blue-500 bg-opacity-20 text-white px-2 py-1 rounded-full text-xs">
                                        {{ $post->author->name }}
                                    </span>
                                    <span class="mx-2">•</span>
                                    <span>{{ format_date($post->published_at) }}</span>
                                </div>
                                <h3 class="news-title text-xl font-bold text-white mb-3 transition-colors duration-300">
                                    {{ $post->title }}
                                </h3>
                                <p class="text-slate-400 mb-4">
                                    {{ Str::limit(strip_tags($post->content), 120) }}
                                </p>
                                <a href="{{ route('posts.show', $post->id) }}"
                                   class="w-full sm:w-auto text-center px-4 py-2 text-white font-semibold rounded-md neon-btn bg-gradient-to-r from-indigo-500 to-purple-500 hover:brightness-110 transition">
                                    Lire la suite
                                    <i class="fas fa-arrow-right ml-2"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="text-center">
                    <x-pagination :totalPages="$totalPages" :currentPage="$currentPage" />
                </div>
            @endif
        </div>
    </section>
@endsection
