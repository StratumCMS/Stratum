@extends('theme::layout.app')

@section('content')
    @php
        $h = [
          'title'    => theme_config('home.hero.title')    ?? 'Bienvenue sur '.site_name(),
          'subtitle' => theme_config('home.hero.subtitle') ?? 'Un thème Tailwind propre, mobile-first et SEO-ready pour StratumCMS.',
          'cta'      => [
            'label' => theme_config('home.hero.cta.label') ?? 'Explorer les articles',
            'url'   => theme_config('home.hero.cta.url')   ?? url('/blog'),
          ],
          'cta2'      => [
            'label' => theme_config('home.hero.cta2.label') ?? 'À propos',
            'url'   => theme_config('home.hero.cta2.url')   ?? url('#'),
          ],
        ];

        $articles = [
          'heading'   => theme_config('articles.heading')    ?? 'Derniers articles',
          'lead'      => theme_config('articles.lead')       ?? 'Restez à jour avec nos dernières publications sur les technologies web, les tendances du développement et les bonnes pratiques.',
          'index_url' => theme_config('articles.index_url')  ?? url('/articles'),
          'count'     => (int) (theme_config('articles.count') ?? 3),
        ];

        $about = [
          'title'  => theme_config('about.title')  ?? 'Notre mission',
          'p1'     => theme_config('about.p1')     ?? "Nous partageons notre passion pour le développement web à travers des articles techniques, des tutoriels approfondis et des analyses des dernières tendances technologiques.",
          'p2'     => theme_config('about.p2')     ?? "Notre équipe d'experts vous accompagne dans votre apprentissage et votre montée en compétences, que vous soyez débutant ou développeur expérimenté.",
          'btn'    => [
              'label' => theme_config('about.button.label') ?? 'En savoir plus',
              'url'   => theme_config('about.button.url')   ?? (theme_config('about_url') ?? '#'),
          ],
          'image'  => theme_config('about.image')  ?? theme_asset('images/about.png'),
        ];
    @endphp

    <section class="relative py-20 lg:py-32 overflow-hidden bg-gradient-hero">
        <div class="container-custom relative z-10">
            <div class="max-w-4xl mx-auto text-center animate-fade-in">
                <h1 class="text-fluid-5xl font-display font-bold text-text mb-6 leading-tight">
                    {{ $h['title'] }}
                </h1>
                <p class="text-fluid-xl text-text-muted mb-8 max-w-2xl mx-auto leading-relaxed">
                    {{ $h['subtitle'] }}
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                    <a href="{{ $h['cta']['url'] }}" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 bg-primary text-primary-foreground hover:bg-primary/90 h-11 rounded-md px-8 animate-scale-in">
                        {{ $h['cta']['label'] }}
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M13.5 4.5L21 12l-7.5 7.5M21 12H3"/>
                        </svg>
                    </a>

                    <a href="{{ $h['cta2']['url'] }}" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-11 rounded-md px-8 animate-scale-in">
                        {{ $h['cta2']['label'] }}
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M13.5 4.5L21 12l-7.5 7.5M21 12H3"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
        <div class="absolute inset-0 -z-10">
            <div class="absolute top-1/4 left-1/4 w-72 h-72 bg-primary/5 rounded-full blur-3xl"></div>
            <div class="absolute top-3/4 right-1/4 w-96 h-96 bg-primary/3 rounded-full blur-3xl"></div>
        </div>
    </section>

    <section class="py-16 bg-surface" aria-labelledby="last-articles-heading">
        <div class="container-custom">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-12">
                <div>
                    <h2 id="last-articles-heading" class="text-fluid-3xl font-display font-bold text-text mb-4">
                        {{ $articles['heading'] }}
                    </h2>
                    <p class="text-text-muted max-w-2xl">
                        {{ $articles['lead'] }}
                    </p>
                </div>

                <a href="{{ $articles['index_url'] }}" class="mt-6 md:mt-0 inline-flex">
                    <span class="sr-only">Voir tous les articles</span>
                    <button class="border border-input bg-background hover:bg-accent hover:text-accent-foreground px-4 py-2 rounded inline-flex items-center gap-2">
                        Voir tous les articles
                        <svg class="ml-2 h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M13.5 4.5 21 12l-7.5 7.5M21 12H3"/></svg>
                    </button>
                </a>
            </div>

            @php
                $items = collect($posts ?? $recentArticles ?? [])->take(max(1, $articles['count']))->values();
                $placeholder = 'https://placehold.co/600x400';
                $ldItems = [];
            @endphp

            @if($items->isNotEmpty())
                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-8">
                    @foreach($items as $index => $item)
                        @php
                            $url   = $item->url
                                   ?? $item->link
                                   ?? (isset($item->slug) ? route('posts.show', $item->slug) : (isset($item->id) ? route('posts.show', $item) : '#'));

                            $cover = $item->cover
                                   ?? $item->image
                                   ?? $item->thumbnail
                                   ?? $placeholder;

                            $title = $item->title ?? 'Sans titre';
                            $desc  = $item->excerpt
                                   ?? $item->description
                                   ?? \Illuminate\Support\Str::limit(strip_tags((string)($item->content ?? '')), 160);

                            $date  = $item->published_at
                                   ?? $item->date
                                   ?? $item->created_at
                                   ?? null;

                            try { if ($date && !($date instanceof \Illuminate\Support\Carbon)) { $date = \Illuminate\Support\Carbon::parse($date); } }
                            catch (\Throwable $e) { $date = null; }

                            $authorName   = $item->author->name ?? ($item->author_name ?? null);
                            $authorAvatar = $item->author->avatar ?? ($item->author_avatar ?? null);
                            $readTime     = $item->read_time ?? $item->readTime ?? null;
                            $tags         = collect($item->tags ?? [])->map(fn($t) => is_string($t) ? $t : ($t->name ?? null))->filter()->values()->all();

                            $ldItems[] = [
                                '@type' => 'ListItem',
                                'position' => $index + 1,
                                'url' => $url,
                                'name' => $title,
                                'image' => $cover,
                                'datePublished' => $date?->toIso8601String(),
                                'author' => $authorName ? ['@type' => 'Person', 'name' => $authorName] : null,
                            ];

                            $isFeatured = $index === 0;
                        @endphp

                        <div class="animate-stagger {{ $isFeatured ? 'md:col-span-2 lg:col-span-3' : '' }}">
                            <article class="group card-hover">
                                <a href="{{ $url }}" class="block bg-bg-elevated border border-border-subtle rounded-xl overflow-hidden h-full">
                                    <figure class="relative overflow-hidden {{ $isFeatured ? 'h-64 md:h-80' : 'h-48' }}">
                                        <img
                                            src="{{ $cover }}"
                                            alt="{{ $title }}"
                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                            loading="lazy" decoding="async" width="1200" height="800">
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                    </figure>

                                    <div class="p-6">
                                        <div class="flex items-center gap-4 text-xs text-text-muted mb-3">
                                            @if($date)
                                                <div class="flex items-center gap-1">
                                                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M7 2h2v2h6V2h2v2h3v18H4V4h3V2Zm13 8H6v10h14V10Z"/></svg>
                                                    <time datetime="{{ $date->toDateString() }}">{{ $date->locale('fr_FR')->translatedFormat('d/m/Y') }}</time>
                                                </div>
                                            @endif

                                            @if($readTime)
                                                <div class="flex items-center gap-1">
                                                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 1.75a10.25 10.25 0 1 0 0 20.5 10.25 10.25 0 0 0 0-20.5ZM12.75 6v6l4 2"/></svg>
                                                    <span>{{ $readTime }} min</span>
                                                </div>
                                            @endif
                                        </div>

                                        <h3 class="font-display font-semibold text-text mb-3 group-hover:text-primary transition-colors duration-200 line-clamp-2 {{ $isFeatured ? 'text-fluid-2xl' : 'text-fluid-lg' }}">
                                            {{ $title }}
                                        </h3>

                                        <p class="text-text-muted mb-4 {{ $isFeatured ? 'text-fluid-base line-clamp-3' : 'text-sm line-clamp-2' }}">
                                            {{ $desc }}
                                        </p>

                                        @if(!empty($tags))
                                            <div class="flex flex-wrap gap-2 mb-4">
                                                @php $maxTags = $isFeatured ? 3 : 3; @endphp
                                                @foreach(array_slice($tags, 0, $maxTags) as $tg)
                                                    <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold bg-surface text-primary font-medium">
                                                    {{ $tg }}
                                                </span>
                                                @endforeach
                                                @if(count($tags) > $maxTags)
                                                    <span class="text-xs text-text-muted">+{{ count($tags) - $maxTags }}</span>
                                                @endif
                                            </div>
                                        @endif

                                        <div class="flex items-center justify-between">
                                            @if($authorName)
                                                <div class="flex items-center gap-2">
                                                <span class="relative flex h-6 w-6 shrink-0 overflow-hidden rounded-full bg-muted">
                                                    <img src="{{ $authorAvatar ?? 'https://placehold.co/32x32' }}" alt="{{ $authorName }}" class="h-full w-full object-cover" loading="lazy" decoding="async" width="32" height="32">
                                                </span>
                                                    <span class="text-xs text-text-muted font-medium">{{ $authorName }}</span>
                                                </div>
                                            @else
                                                <span aria-hidden="true"></span>
                                            @endif

                                            <span class="flex items-center gap-1 text-xs text-primary font-medium group-hover:gap-2 transition-all duration-200">
                                            Lire
                                            <svg class="w-3 h-3" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M13.5 4.5 21 12l-7.5 7.5M21 12H3"/></svg>
                                        </span>
                                        </div>
                                    </div>
                                </a>
                            </article>
                        </div>
                    @endforeach
                </div>

                <script type="application/ld+json">
                    {!! json_encode([
                        '@context' => 'https://schema.org',
                        '@type' => 'ItemList',
                        'itemListElement' => array_values(array_filter($ldItems)),
                    ], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}
                </script>
            @else
                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-8">
                    @for($i=0;$i<3;$i++)
                        <div class="bg-bg-elevated border border-border-subtle rounded-xl overflow-hidden">
                            <div class="h-48 bg-surface animate-pulse"></div>
                            <div class="p-6 space-y-3">
                                <div class="h-4 w-1/3 bg-surface animate-pulse rounded"></div>
                                <div class="h-6 w-5/6 bg-surface animate-pulse rounded"></div>
                                <div class="h-4 w-4/5 bg-surface animate-pulse rounded"></div>
                                <div class="h-4 w-2/5 bg-surface animate-pulse rounded"></div>
                            </div>
                        </div>
                    @endfor
                </div>
            @endif
        </div>
    </section>

    <section class="py-20">
        <div class="container-custom">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 border-transparent bg-surface text-primary font-medium hover:bg-primary/10 mb-4">À propos</span>
                    <h2 class="text-fluid-3xl font-display font-bold text-text mb-6">
                        {{ $about['title'] }}
                    </h2>
                    <p class="text-text-muted mb-6 text-fluid-base leading-relaxed">
                        {{ $about['p1'] }}
                    </p>
                    <p class="text-text-muted mb-8 text-fluid-base leading-relaxed">
                        {{ $about['p2'] }}
                    </p>
                    <a href="{{ $about['btn']['url'] }}" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2">
                        {{ $about['btn']['label'] }}
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-2 h-4 w-4"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                    </a>
                </div>

                <div class="relative">
                    <div class="bg-gradient-primary rounded-2xl shadow-2xl overflow-hidden">
                        <div class="bg-bg-elevated px-4 py-3 flex items-center space-x-2">
                            <img src="{{ $about['image'] }}" class="p-6 w-full rounded-lg" alt="À propos" loading="lazy" decoding="async" />
                        </div>
                    </div>
                    <div class="absolute -top-4 -right-4 w-24 h-24 bg-primary/10 rounded-full blur-xl"></div>
                    <div class="absolute -bottom-4 -left-4 w-32 h-32 bg-primary/5 rounded-full blur-2xl"></div>
                </div>

            </div>
        </div>
    </section>
@endsection
