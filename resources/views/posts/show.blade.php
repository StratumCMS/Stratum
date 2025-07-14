@extends('layouts.app')

@section('title', $article->title)
@section('description', $article->description)
@section('type', 'article')

@push('meta')
    <meta property="og:article:author:username" content="{{ $article->author->name }}">
    <meta property="og:article:published_time" content="{{ $article->published_at->toIso8601String() }}">
    <meta property="og:article:modified_time" content="{{ $article->updated_at->toIso8601String() }}">
@endpush

@section('content')
    <section >
        <div class="max-w-4xl mx-auto px-4">
            <div class="mb-6">
                <a href="{{ route('posts.index') }}" class="inline-flex items-center text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-primary transition">
                    <i class="fas fa-arrow-left mr-2"></i> Retour aux articles
                </a>
            </div>

            <div class="mb-8">
                <div class="mb-6">
                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold bg-secondary text-secondary-foreground border border-transparent backdrop-blur-sm mb-4">
                    {{ $article->type ?? 'Article' }}
                </span>
                    <h1 class="text-4xl md:text-5xl font-bold leading-tight mb-4">
                        {{ $article->title }}
                    </h1>

                    <div class="flex flex-wrap items-center gap-6 text-sm text-gray-600 dark:text-gray-400">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-calendar-alt"></i>
                            <span>{{ format_date($article->published_at) }}</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-clock"></i>
                            <span>{{ $article->read_time ?? '5 min' }} de lecture</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-user"></i>
                            <span>{{ $article->author->name ?? 'Anonyme' }}</span>
                        </div>
                    </div>
                </div>

                <div class="relative rounded-2xl overflow-hidden shadow-2xl mb-8">
                    <img src="{{ $article->thumbnail ?? 'https://placehold.co/1200x600' }}" alt="{{ $article->title }}" class="w-full h-[400px] object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <div class="lg:col-span-3">
                    <div class="rounded-lg border backdrop-blur-xl bg-white/80 dark:bg-slate-800/80 border-gray-200/50 dark:border-slate-700/50 shadow-lg">
                        <div class="p-8">
                            <div class="prose prose-lg max-w-none dark:prose-invert">
                                {!! $article->content !!}
                            </div>

                            @if ($article->tags)
                                <div class="my-8 border-t pt-6 flex flex-wrap gap-2">
                                    @foreach($article->tags as $tag)
                                        <span class="text-xs inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-muted-foreground bg-muted backdrop-blur-sm">
                                        <i class="fas fa-tag text-gray-400 mr-1"></i> {{ $tag }}
                                    </span>
                                    @endforeach
                                </div>
                            @endif

                            @auth
                                <div class="flex items-center justify-between pt-8">
                                    <div class="flex items-center space-x-4">
                                        <button id="like-button" class="text-red-400 hover:text-red-500 backdrop-blur-sm">
                                            <i id="like-icon" class="{{ auth()->user()->likedArticles->contains($article->id) ? 'fas' : 'far' }} fa-heart h-5 w-5 mr-2"></i>
                                            <span id="like-count">{{ $article->likes->count() }}</span>
                                        </button>

                                        <div class="text-gray-400 flex items-center gap-2 backdrop-blur-sm">
                                            <i class="fas fa-comment-alt"></i>
                                            <span>{{ $article->comments->count() }} commentaire{{ $article->comments->count() > 1 ? 's' : '' }}</span>
                                        </div>
                                    </div>

                                    <button onclick="navigator.clipboard.writeText(window.location.href)" class="text-gray-400 hover:text-primary text-sm flex items-center space-x-2 backdrop-blur-sm">
                                        <i class="fas fa-share-alt"></i>
                                        <span>Partager</span>
                                    </button>
                                </div>
                            @endauth
                        </div>
                    </div>

                    <div class="mt-12 space-y-8">
                        <h2 class="text-2xl font-bold">Commentaires ({{ $article->comments->count() }})</h2>

                        @auth
                            <form method="POST" action="{{ route('comments.store', $article) }}" class="space-y-4">
                                @csrf
                                <textarea name="content" rows="4" class="w-full p-3 rounded bg-white/10 dark:bg-white/5 border border-white/20 text-gray-900 dark:text-white resize-none" placeholder="Écrivez un commentaire..."></textarea>
                                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded">
                                    <i class="fas fa-paper-plane mr-2"></i> Publier
                                </button>
                            </form>
                        @else
                            <p class="text-sm text-gray-500 dark:text-gray-400">Connectez-vous pour commenter.</p>
                        @endauth

                        @foreach($article->comments as $comment)
                            @php
                                $user = $comment->user;
                                $rank = $user->roles->first() ?? null;
                            @endphp
                            <div class="flex items-start gap-4 p-4 rounded-xl bg-white/80 dark:bg-white/5 border border-gray-200/40 dark:border-white/10 shadow">
                                <img src="{{ $user->avatar_url ?? 'https://placehold.co/40x40' }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-full object-cover">


                                <div class="flex-1 min-w-0 space-y-1">
                                    <div class="flex flex-wrap items-center gap-2 text-sm">
                                        <span class="font-semibold text-gray-900 dark:text-white">{{ $user->name }}</span>

                                        @if($rank)
                                            <span class="inline-flex items-center text-xs font-medium px-2 py-0.5 rounded-full text-white" style="background-color: {{ $rank->color }};">
                                            @if($rank->icon)
                                                    <i class="fas fa-{{ $rank->icon }} mr-1"></i>
                                                @endif
                                                {{ $rank->name }}
                                        </span>
                                        @endif

                                        <span class="text-xs text-gray-500 dark:text-gray-400">• {{ $comment->created_at->diffForHumans() }}</span>
                                    </div>

                                    <p class="text-sm text-gray-800 dark:text-gray-300 leading-relaxed">{{ $comment->content }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="lg:col-span-1 space-y-6">
                    <div class="backdrop-blur-xl bg-white/80 dark:bg-slate-800/80 border border-white/10 p-6 rounded-xl shadow-lg text-center">
                        <img src="{{ $article->author->avatar_url ?? 'https://placehold.co/60x60' }}" alt="{{ $article->author->name }}" class="w-16 h-16 rounded-full mx-auto mb-4 object-cover">
                        <h3 class="font-bold mb-1">{{ $article->author->name }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Auteur du blog</p>
                    </div>

                    @if($relatedArticles->count())
                        <div class="rounded-lg border text-card-foreground backdrop-blur-xl bg-white/80 dark:bg-slate-800/80 border-gray-200/50 dark:border-slate-700/50 shadow-lg">
                            <div class="p-6 pt-0">
                                <h3 class="font-bold text-gray-900 dark:text-white mb-4">Articles similaires</h3>
                            </div>
                            <div class="space-y-4">
                                @foreach($relatedArticles as $related)
                                    <div class="flex space-x-3">
                                        <img src="{{ $related->thumbnail }}" alt="{{ $related->title }}" class="w-12 h-12 rounded-lg object-cover">
                                        <div class="flex-1 min-w-0">
                                            <a href="{{ route('posts.show', $related->id) }}" class="block">
                                                <h4 class="text-sm font-medium text-gray-900 dark:text-white line-clamp-2 hover:text-primary transition-colors">Article connexe #{{$related->id}}</h4>
                                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Publié le {{ format_date($related->published_at) }}</p>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </section>

    @auth
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const likeBtn = document.getElementById('like-button');
                const likeIcon = document.getElementById('like-icon');
                const likeCount = document.getElementById('like-count');

                likeBtn.addEventListener('click', async () => {
                    try {
                        const res = await fetch("{{ route('articles.like', $article) }}", {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            }
                        });

                        const data = await res.json();

                        if (data.liked) {
                            likeIcon.classList.remove('far');
                            likeIcon.classList.add('fas', 'text-red-500');
                            likeCount.textContent = parseInt(likeCount.textContent) + 1;
                        } else {
                            likeIcon.classList.remove('fas', 'text-red-500');
                            likeIcon.classList.add('far');
                            likeCount.textContent = parseInt(likeCount.textContent) - 1;
                        }
                    } catch (error) {
                        alert('Erreur lors du like.');
                    }
                });
            });
        </script>
    @endauth
@endsection
