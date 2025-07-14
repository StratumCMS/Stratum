@extends('layouts.app')

@section('title', $user->name)

@section('content')
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-10 min-h-screen" x-data="{ copied: false }">

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
                                <p class="text-gray-600 dark:text-gray-400 text-base cursor-pointer" @click="navigator.clipboard.writeText('{{ route('profile.show', $user->name) }}'); copied = true; setTimeout(() => copied = false, 2000)">
                                    {{ "@" . $user->name }} <span x-show="copied" class="ml-2 text-xs text-green-600">copié !</span>
                                </p>
                            </div>
                            @auth
                                @if(auth()->id() === $user->id)
                                    <a href="{{ route('profile.edit') }}" class="inline-flex items-center gap-2 rounded-md border border-input bg-background px-3 py-1.5 text-sm font-medium text-gray-800 dark:text-gray-200 hover:bg-accent hover:text-accent-foreground backdrop-blur-sm transition">
                                        <i class="fas fa-cog text-sm"></i> Modifier le profil
                                    </a>
                                @endif
                            @endauth
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
                                        $icon = ['fa-globe', ucfirst($host), 'fas']; // fallback with 'solid' globe

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



                    </div>

                    <div class="grid grid-cols-3 text-center gap-4 min-w-full md:min-w-[200px]">
                        <div class="p-4 bg-muted/40 dark:bg-muted/20 rounded-lg shadow-sm">
                            <div class="text-xl font-bold text-primary">{{ $articlesCount }}</div>
                            <div class="text-sm text-muted-foreground">Articles</div>
                        </div>
                        <div class="p-4 bg-muted/40 dark:bg-muted/20 rounded-lg shadow-sm">
                            <div class="text-xl font-bold text-primary">{{ $likesCount }}</div>
                            <div class="text-sm text-muted-foreground">Likes</div>
                        </div>
                        <div class="p-4 bg-muted/40 dark:bg-muted/20 rounded-lg shadow-sm">
                            <div class="text-xl font-bold text-primary">{{ $followersCount }}</div>
                            <div class="text-sm text-muted-foreground">Followers</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div x-data="{ tab: 'articles' }" class="space-y-6">

            <div class="grid grid-cols-3 rounded-lg overflow-hidden ring-1 ring-gray-200 dark:ring-slate-700 shadow">
                <button @click="tab = 'articles'" :class="tab === 'articles' ? 'bg-primary text-white' : 'bg-muted text-muted-foreground'" class="text-sm font-medium px-3 py-2 transition-colors">Articles</button>
                <button @click="tab = 'liked'" :class="tab === 'liked' ? 'bg-primary text-white' : 'bg-muted text-muted-foreground'" class="text-sm font-medium px-3 py-2 transition-colors">Aimés</button>
                <button @click="tab = 'about'" :class="tab === 'about' ? 'bg-primary text-white' : 'bg-muted text-muted-foreground'" class="text-sm font-medium px-3 py-2 transition-colors">A propos</button>
            </div>

            <div x-show="tab === 'articles'" x-cloak class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 min-h-[300px]">
                @forelse ($articles as $article)
                    <div class="rounded-xl shadow-md hover:shadow-xl transition overflow-hidden bg-white/80 dark:bg-slate-800/80 backdrop-blur-md border border-gray-200 dark:border-slate-700">
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
                            <p class="text-sm text-muted-foreground">{{ Str::limit($article->excerpt, 120) }}</p>
                            <div class="flex items-center justify-between text-xs text-muted-foreground">
                                <span><i class="fas fa-heart mr-1"></i>{{ $article->likes->count() }}</span>
                                <span><i class="fas fa-comments mr-1"></i>{{ $article->comments->count() }}</span>
                            </div>
                            <a href="{{ route('posts.show', $article) }}" class="text-sm font-semibold text-primary hover:underline block pt-2">Lire l'article</a>
                        </div>
                    </div>
                @empty
                    <div class="rounded-lg border text-card-foreground backdrop-blur-xl bg-white/80 dark:bg-slate-800/80 border-gray-200/50 dark:border-slate-700/50 shadow-lg col-span-full">
                        <div class="pt-0 p-8 text-center">
                            <i class="fas fa-file-alt h-12 w-12 text-gray-400 mx-auto mb-4"></i>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Aucun article</h3>
                            <p class="text-gray-600 dark:text-gray-400">Les articles que {{ $user->name }} a écrit apparaîtront ici.</p>
                        </div>
                    </div>
                @endforelse
            </div>

            <div x-show="tab === 'liked'" x-cloak class="space-y-6 min-h-[200px]">
                @forelse ($user->likedArticles as $liked)
                    <div class="bg-white dark:bg-slate-800 p-4 rounded-xl shadow">
                        <h3 class="font-semibold text-lg">
                            <a href="{{ route('posts.show', $liked) }}" class="hover:text-primary transition">
                                {{ $liked->title }}
                            </a>
                        </h3>
                        <p class="text-sm text-muted-foreground">{{ $liked->excerpt }}</p>
                    </div>
                @empty
                    <div class="rounded-lg border text-card-foreground backdrop-blur-xl bg-white/80 dark:bg-slate-800/80 border-gray-200/50 dark:border-slate-700/50 shadow-lg">
                        <div class="flex flex-col items-center justify-center text-center p-8 space-y-4">
                            <i class="fas fa-heart text-gray-400 text-5xl"></i>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Aucun article aimé</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-sm">
                                {{ $user->name }} n’a pas encore aimé d’article. <br class="hidden sm:inline"> Les articles aimés apparaîtront ici.
                            </p>
                        </div>
                    </div>
                @endforelse
            </div>


            <div class="space-y-6">
                <div x-show="tab === 'about'" x-cloak class="rounded-lg border text-card-foreground backdrop-blur-xl bg-white/80 dark:bg-slate-800/80 border-gray-200/50 dark:border-slate-700/50 shadow-lg min-h-[200px]">
                    <div class="pt-0 p-8">
                        <h3 class="text-xl font-semibold text-foreground mb-4">À propos de {{ $user->name }}</h3>
                        <p class="text-gray-700 dark:text-gray-300 mb-4">
                            {{ $user->bio ?? 'Cet utilisateur n’a pas encore rédigé de biographie.' }}
                        </p>
                        <div class="space-y-4">
                            <div>
                                <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Contact</h4>
                                <div class="space-y-2 text-gray-600 dark:text-gray-400">
                                    <div class="flex items-center space-x-2"><i class="fas fa-envelope h-4 w-4"></i>{{ $user->email }}</div>
                                    @if($user->website)
                                        <div class="flex items-center space-x-2">
                                            <i class="fas fa-globe h-4 w-4"></i>
                                            <a href="{{ $user->website }}" target="_blank" rel="noopener noreferrer" class="text-primary hover:text-primary/80 transition-colors">{{ $user->website }}</a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
