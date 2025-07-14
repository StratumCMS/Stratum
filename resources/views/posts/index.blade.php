@extends('layouts.app')

@section('title', 'Articles')

@section('content')
    <section class="py-16 bg-gray-50/50 dark:bg-slate-900/50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">Blog Stratum</h1>
                <p class="text-xl text-gray-600 dark:text-gray-400">
                    Découvrez nos derniers articles sur le développement web, le design et les tendances tech.
                </p>
            </div>

            <form method="GET" action="{{ route('posts.index') }}" class="mb-8">
                <div class="flex flex-col md:flex-row gap-4 mb-6">
                    <div class="flex-1 relative">
                        <i class="fas fa-search absolute left-3 top-3 h-5 w-5 text-gray-400"></i>
                        <input type="text" name="search" placeholder="Rechercher des articles"
                               value="{{ request('search') }}"
                               class="flex h-10 w-full rounded-md border px-3 py-2 text-base ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm pl-10 backdrop-blur-sm bg-white/50 dark:bg-slate-700/50 border-gray-300/50 dark:border-slate-600/50">
                    </div>
                </div>

                <div class="flex flex-wrap gap-2">
                    @foreach($types as $type)
                        <button type="submit" name="type" value="{{ $type }}"
                                class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 border h-9 px-3 backdrop-blur-sm {{ request('type') === $type ? 'bg-primary text-white' : 'bg-white/50 dark:bg-slate-700/50 text-gray-800 dark:text-white border-gray-300/50 dark:border-slate-600/50 hover:bg-primary hover:text-white' }}">
                            {{ $type }}
                        </button>
                    @endforeach

                    @if(request('type'))
                        <a href="{{ route('posts.index', array_filter(request()->except('type'))) }}"
                           class="text-sm text-blue-500 hover:underline ml-2">Réinitialiser</a>
                    @endif
                </div>
            </form>

            @if($featured->count())
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
                        Articles en vedette
                    </h2>
                    <div class="rounded-lg border text-card-foreground backdrop-blur-xl bg-white/80 dark:bg-slate-800/80 border-gray-200/50 dark:border-slate-700/50 shadow-2xl overflow-hidden">
                        <div class="md:flex">
                            @foreach($featured as $article)
                                <div class="md:w-1/2">
                                    <img src="{{ $article->thumbnail() }}" class="w-full h-64 md:h-full object-cover">
                                </div>
                                <div class="md:w-1/2 p-8">
                                    <div class="flex items-center space-x-4 mb-4">
                                        <div class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 border-transparent bg-secondary text-secondary-foreground hover:bg-secondary/80 backdrop-blur-sm">
                                            {{ $article->type }}
                                        </div>
                                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                            <i class="fas fa-calendar h-4 w-4 mr-1"></i>
                                            {{ format_date($article->published_at) }}
                                        </div>
                                    </div>
                                    <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
                                        {{ $article->title }}
                                    </h3>
                                    <p class="text-gray-600 dark:text-gray-400 mb-6 leading-relaxed">
                                        {{ Str::limit($article->description, 100) }}
                                    </p>

                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-2">
                                            <i class="fas fa-user h-4 w-4 text-gray-500"></i>
                                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ $article->author->name }}</span>
                                        </div>
                                        <a href="{{ route('posts.show', $article) }}" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 text-primary-foreground bg-primary hover:bg-primary/90 h-10 px-4 py-2">
                                            <i class="fa-solid fa-arrow-right h-4 w-4 ml-1"></i>
                                            Lire l'article
                                        </a>
                                    </div>

                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            @if($posts->count())
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Tous les articles</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach($posts as $post)
                            <div class="rounded-lg border shadow-md hover:shadow-lg transition-all duration-300 overflow-hidden group backdrop-blur-xl bg-white/80 dark:bg-slate-800/80">
                                <div class="relative">
                                    <img src="{{ $post->thumbnail() }}" alt="{{ $post->title }}" class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
                                    <div class="absolute top-4 left-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-semibold rounded-full border bg-secondary text-secondary-foreground">
                                            {{ $post->type ?? 'Article' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="p-5">
                                    <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 mb-2">
                                        <i class="fas fa-calendar-alt"></i>
                                        {{ format_date($post->published_at) }}
                                    </div>
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-white group-hover:text-primary transition-colors mb-2">
                                        <a href="{{ route('posts.show', $post) }}">{{ $post->title }}</a>
                                    </h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                        {{ Str::limit($post->description, 100) }}
                                    </p>
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                            <i class="fas fa-user"></i>
                                            {{ $post->author->name ?? 'Anonyme' }}
                                        </div>
                                        <a href="{{ route('posts.show', $post) }}" class="text-sm text-primary hover:underline inline-flex items-center gap-1">
                                            Lire plus <i class="fas fa-arrow-right"></i>
                                        </a>
                                    </div>
                                    @if($post->tags)
                                        <div class="mt-4 flex flex-wrap gap-2">
                                            @foreach($post->tags as $tag)
                                                <span class="text-xs inline-flex items-center gap-1 px-2 py-0.5 border rounded-full text-muted-foreground bg-muted">
                                                    <i class="fas fa-tag text-gray-400"></i> {{ $tag }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="mt-10">
                {{ $posts->withQueryString()->links() }}
            </div>
        </div>
    </section>
@endsection
