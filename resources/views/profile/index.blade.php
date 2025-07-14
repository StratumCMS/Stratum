@extends('layouts.app')

@section('title', 'Mon profil')

@section('content')
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-10 min-h-screen">

        <div class="rounded-xl border backdrop-blur-xl bg-white/80 dark:bg-slate-800/80 border-gray-200/50 dark:border-slate-700/50 shadow-lg">
            <div class="p-6 sm:p-8">
                <div class="flex flex-col md:flex-row gap-6 sm:gap-8">
                    <div class="flex-shrink-0 text-center md:text-left">
                        <img src="{{ $user->avatar_url ?? '/images/default-avatar.png' }}" alt="{{ $user->name }}" class="w-32 h-32 rounded-full mx-auto md:mx-0 object-cover shadow-lg">
                    </div>

                    <div class="flex-1 text-center md:text-left">
                        <div class="flex flex-col md:flex-row md:items-start md:justify-between mb-4">
                            <div>
                                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-1">{{ $user->name }}</h1>
                                <p class="text-gray-600 dark:text-gray-400 text-base">
                                    {{ "@" . $user->name }}
                                </p>
                            </div>

                            <a href="{{ route('profile.edit') }}" class="inline-flex items-center gap-2 rounded-md border border-input bg-background px-3 py-1.5 text-sm font-medium text-gray-800 dark:text-gray-200 hover:bg-accent hover:text-accent-foreground backdrop-blur-sm transition">
                                <i class="fas fa-cog text-sm"></i> Modifier mon profil
                            </a>
                        </div>

                        @if ($user->bio)
                            <p class="text-gray-700 dark:text-gray-300 mb-6 leading-relaxed">
                                {{ $user->bio }}
                            </p>
                        @endif

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6 text-sm text-gray-600 dark:text-gray-400">
                            @if ($user->location)
                                <div class="flex items-center gap-2 justify-center sm:justify-start">
                                    <i class="fas fa-map-marker-alt text-primary"></i>
                                    {{ $user->location }}
                                </div>
                            @endif
                            <div class="flex items-center gap-2 justify-center sm:justify-start">
                                <i class="fas fa-calendar-alt text-primary"></i>
                                Membre depuis {{ $user->created_at->translatedFormat('d F Y') }}
                            </div>
                            @if ($user->website)
                                <div class="flex items-center gap-2 justify-center sm:justify-start">
                                    <i class="fas fa-globe text-primary"></i>
                                    <a href="{{ $user->website }}" class="text-primary hover:underline" target="_blank">
                                        {{ Str::limit($user->website, 30) }}
                                    </a>
                                </div>
                            @endif
                            <div class="flex items-center gap-2 justify-center sm:justify-start">
                                <i class="fas fa-envelope text-primary"></i>
                                {{ $user->email }}
                            </div>
                        </div>

                        @if (!empty($user->social_links))
                            <div class="flex justify-center sm:justify-start gap-4 mb-6">
                                @foreach ($user->social_links as $link)
                                    @php
                                        $iconMap = [
                                            'github.com' => ['fa-github', 'GitHub', 'fab'],
                                            'twitter.com' => ['fa-twitter', 'Twitter', 'fab'],
                                            'x.com' => ['fa-twitter', 'X', 'fab'],
                                            'linkedin.com' => ['fa-linkedin', 'LinkedIn', 'fab'],
                                            'facebook.com' => ['fa-facebook', 'Facebook', 'fab'],
                                            'instagram.com' => ['fa-instagram', 'Instagram', 'fab'],
                                            'tiktok.com' => ['fa-tiktok', 'TikTok', 'fab'],
                                            'youtube.com' => ['fa-youtube', 'YouTube', 'fab'],
                                            'discord.gg' => ['fa-discord', 'Discord', 'fab'],
                                            'discord.com' => ['fa-discord', 'Discord', 'fab'],
                                            'dribbble.com' => ['fa-dribbble', 'Dribbble', 'fab'],
                                            'behance.net' => ['fa-behance', 'Behance', 'fab'],
                                            'medium.com' => ['fa-medium', 'Medium', 'fab'],
                                            'reddit.com' => ['fa-reddit', 'Reddit', 'fab'],
                                            'dev.to' => ['fa-dev', 'Dev.to', 'fab'],
                                            'pinterest.com' => ['fa-pinterest', 'Pinterest', 'fab'],
                                            'snapchat.com' => ['fa-snapchat', 'Snapchat', 'fab'],
                                            'twitch.tv' => ['fa-twitch', 'Twitch', 'fab'],
                                            'soundcloud.com' => ['fa-soundcloud', 'SoundCloud', 'fab'],
                                            'spotify.com' => ['fa-spotify', 'Spotify', 'fab'],
                                            'codepen.io' => ['fa-codepen', 'CodePen', 'fab'],
                                            'stackoverflow.com' => ['fa-stack-overflow', 'Stack Overflow', 'fab'],
                                        ];

                                        $host = parse_url($link, PHP_URL_HOST);
                                        $icon = ['fa-globe', ucfirst($host), 'fas'];

                                        foreach ($iconMap as $domain => $info) {
                                            if (Str::contains($host, $domain)) {
                                                $icon = $info;
                                                break;
                                            }
                                        }
                                    @endphp

                                    <a href="{{ $link }}"
                                       target="_blank"
                                       rel="noopener noreferrer"
                                       title="{{ $icon[1] }}"
                                       aria-label="Voir le profil {{ $icon[1] }} de {{ $user->name }}"
                                       class="text-gray-600 hover:text-primary transition text-xl"
                                    >
                                        <i class="{{ $icon[2] }} {{ $icon[0] }}"></i>
                                    </a>
                                @endforeach
                            </div>
                        @endif

                        <div class="grid grid-cols-3 text-center gap-4">
                            <div class="p-4 bg-muted/40 dark:bg-muted/20 rounded-lg shadow-sm">
                                <div class="text-xl font-bold text-primary">{{ $articlesCount }}</div>
                                <div class="text-sm text-muted-foreground">Mes articles</div>
                            </div>
                            <div class="p-4 bg-muted/40 dark:bg-muted/20 rounded-lg shadow-sm">
                                <div class="text-xl font-bold text-primary">{{ $likesCount }}</div>
                                <div class="text-sm text-muted-foreground">J'aime</div>
                            </div>
                            <div class="p-4 bg-muted/40 dark:bg-muted/20 rounded-lg shadow-sm">
                                <div class="text-xl font-bold text-primary">{{ $followersCount }}</div>
                                <div class="text-sm text-muted-foreground">Abonnés</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">Mes articles</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($articles as $article)
                    <div class="relative rounded-xl shadow-md hover:shadow-xl transition overflow-hidden bg-white/80 dark:bg-slate-800/80 backdrop-blur-md border border-gray-200 dark:border-slate-700">

                        @if($article->thumbnail)
                            <img src="{{ $article->thumbnail }}" class="w-full h-48 object-cover" alt="{{ $article->title }}">
                        @endif

                        <div class="p-6 space-y-3">
                            <div class="flex justify-between text-xs text-muted-foreground">
                                <span class="bg-secondary text-secondary-foreground px-2 py-0.5 rounded">{{ $article->type ?? 'Article' }}</span>
                                <span>{{ $article->published_at->format('d/m/Y') }}</span>
                            </div>
                            <h3 class="text-lg font-bold text-foreground">
                                <a href="{{ route('posts.show', $article) }}" class="hover:text-primary transition">{{ $article->title }}</a>
                            </h3>
                            <p class="text-sm text-muted-foreground">{{ Str::limit($article->excerpt, 100) }}</p>
                            <div class="flex justify-between text-xs text-muted-foreground">
                                <span><i class="fas fa-heart mr-1"></i>{{ $article->likes->count() }}</span>
                                <span><i class="fas fa-comments mr-1"></i>{{ $article->comments->count() }}</span>
                            </div>

                            <div class="pt-2 flex justify-between items-center">
                                <a href="{{ route('posts.show', $article) }}" class="text-sm font-semibold text-primary hover:underline">
                                    Voir
                                </a>
                                <a href="{{ route('admin.articles.edit', $article->id) }}"
                                   class="text-sm text-gray-500 hover:text-primary transition"
                                   title="Modifier l'article">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center text-muted-foreground py-12">
                        <i class="fas fa-file-alt text-4xl text-gray-400 mb-4"></i>
                        <p class="text-lg">Tu n'as encore écrit aucun article.</p>
                    </div>
                @endforelse


            </div>
        </div>

    </div>
@endsection
