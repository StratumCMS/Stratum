@extends('theme::layout')

@section('title', $article->title)
@section('description', $article->description)
@section('type', 'article')

@push('meta')
    <meta property="og:article:author:username" content="{{ $article->author->name }}">
    <meta property="og:article:published_time" content="{{ $article->published_at->toIso8601String() }}">
    <meta property="og:article:modified_time" content="{{ $article->updated_at->toIso8601String() }}">
@endpush

@section('content')
    <section class="py-20 bg-gray-900 text-white">
        <div class="max-w-4xl mx-auto px-4">
            <article class="mb-16">
                <img src="{{ $article->thumbnail ?? 'https://placehold.co/800x400?text=Sans+image' }}" alt="{{ $article->title }}" class="rounded-lg shadow mb-6">

                <h1 class="text-4xl font-bold mb-2">{{ $article->title }}</h1>
                <div class="text-sm text-slate-400 mb-4">
                    Par <strong>{{ $article->author->name ?? 'Anonyme' }}</strong>
                    <span class="mx-2">•</span>
                    {{ format_date($article->published_at) }}
                    <span class="mx-2">•</span>
                    💬 {{ $article->comments->count() }} commentaire{{ $article->comments->count() > 1 ? 's' : '' }}
                    <span class="mx-2">•</span>
                    ❤️ <span id="like-count">{{ $article->likes->count() }}</span> like{{ $article->likes->count() > 1 ? 's' : '' }}
                </div>

                <div class="prose prose-invert max-w-none">
                    {!! $article->content !!}
                </div>

                @auth
                    <div class="mt-8 flex flex-col items-center">
                        <button id="like-button" class="group transition duration-200">
                            <i id="like-icon" class="text-3xl transition-all duration-300
            {{ auth()->user()->likedArticles->contains($article->id) ? 'fas text-red-500' : 'far text-gray-400 group-hover:text-red-400' }} fa-heart">
                            </i>
                        </button>
                        <span id="like-count" class="mt-2 text-sm text-slate-400">
        {{ $article->likes->count() }} like{{ $article->likes->count() > 1 ? 's' : '' }}
    </span>
                    </div>
                @endauth

            </article>

            <hr class="border-gray-700 mb-6">

            <section>
                <h2 class="text-2xl font-bold mb-4">Commentaires</h2>

                @auth
                    <form method="POST" action="{{ route('comments.store', $article) }}" class="mb-6">
                        @csrf
                        <textarea name="content" rows="4" class="w-full p-3 rounded bg-gray-800 border border-gray-700 text-white" placeholder="Écrire un commentaire..."></textarea>
                        <button type="submit" class="mt-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded">Publier</button>
                    </form>
                @else
                    <p class="text-sm text-slate-400 mb-6">Connectez-vous pour commenter.</p>
                @endauth

                @foreach($article->comments as $comment)
                    <div class="mb-4 p-4 bg-gray-800 rounded shadow">
                        <div class="text-sm text-slate-400 mb-1">
                            {{ $comment->user->name }} • {{ $comment->created_at->diffForHumans() }}
                        </div>
                        <p class="text-white">{{ $comment->content }}</p>
                    </div>
                @endforeach
            </section>
        </div>
    </section>

    @auth
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const likeButton = document.getElementById('like-button');
                likeButton.addEventListener('click', async () => {
                    try {
                        const res = await fetch("{{ route('articles.like', $article) }}", {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            }
                        });

                        const data = await res.json();

                        const icon = document.getElementById('like-icon');
                        const label = document.getElementById('like-label');
                        const count = document.getElementById('like-count');

                        if (data.liked) {
                            icon.classList.remove('far');
                            icon.classList.add('fas');
                            label.innerText = 'Retirer le like';
                            count.innerText = parseInt(count.innerText) + 1;
                        } else {
                            icon.classList.remove('fas');
                            icon.classList.add('far');
                            label.innerText = 'Aimer cet article';
                            count.innerText = parseInt(count.innerText) - 1;
                        }

                    } catch (e) {
                        alert('Erreur lors du traitement du like.');
                    }
                });
            });
        </script>
    @endauth
@endsection
