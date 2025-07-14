@extends('layouts.app')
@section('title', 'Accueil')

@section('content')

    <section class="relative py-16 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-slate-900 dark:to-slate-800 opacity-50"></div>
        <div class="relative container mx-auto px-4 text-center">
            <div class="max-w-4xl mx-auto">
                <div class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 border-transparent bg-secondary text-secondary-foreground hover:bg-secondary/80 backdrop-blur-sm mb-6">
                    ✨ Installation réussie
                </div>
                <h1 class="text-4xl md:text-6xl font-bold text-foreground mb-6 leading-tight">
                    Bienvenue dans <span class="text-primary">StratumCMS</span>
                </h1>
                <p class="text-xl md:text-2xl text-muted-foreground mb-8 leading-relaxed">
                    Votre système de gestion de contenu est prêt !
                    Commencez à créer du contenu exceptionnel dès maintenant.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    @auth
                        @can('access_dashboard')
                            <a href="{{ route('dashboard') }}">
                                <button class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 px-8 py-4 bg-primary text-primary-foreground hover:bg-primary/90 h-11">
                                    Accéder à l'administration
                                    <i class="fas fa-cog"></i>
                                </button>
                            </a>
                        @endcan
                    @endauth
                    <a href="{{ route('posts.index') }}">
                        <button class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 backdrop-blur-sm px-8 py-4 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-11">
                            Voir le blog
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Quick Actions --}}
    @auth
        @can('access_dashboard')
            <section class="py-16">
                <div class="container mx-auto px-4">
                    <div class="text-center mb-12">
                        <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">Premiers pas</h2>
                        <p class="text-xl text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                            Configurez votre CMS en quelques clics
                        </p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <a href="{{ route('dashboard') }}" class="group">
                            <div class="rounded-lg border shadow-lg bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl p-6 transition-transform group-hover:scale-105">
                                <div class="w-16 h-16 mb-4 rounded-full bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 flex items-center justify-center mx-auto">
                                    <i class="fas fa-cogs fa-2x"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-center text-gray-900 dark:text-white mb-1 group-hover:text-primary">
                                    Configuration générale
                                </h3>
                                <p class="text-sm text-center text-gray-600 dark:text-gray-400">
                                    Configurez les paramètres de base de votre CMS
                                </p>
                            </div>
                        </a>
                        <a href="{{ route('themes.index') }}" class="group">
                            <div class="rounded-lg border shadow-lg bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl p-6 transition-transform group-hover:scale-105">
                                <div class="w-16 h-16 mb-4 rounded-full bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400 flex items-center justify-center mx-auto">
                                    <i class="fas fa-paint-brush fa-2x"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-center text-gray-900 dark:text-white mb-1 group-hover:text-primary">
                                    Gestion des thèmes
                                </h3>
                                <p class="text-sm text-center text-gray-600 dark:text-gray-400">
                                    Personnalisez l'apparence de votre site
                                </p>
                            </div>
                        </a>
                        <a href="{{ route('admin.articles.create') }}" class="group">
                            <div class="rounded-lg border shadow-lg bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl p-6 transition-transform group-hover:scale-105">
                                <div class="w-16 h-16 mb-4 rounded-full bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 flex items-center justify-center mx-auto">
                                    <i class="fas fa-plus fa-2x"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-center text-gray-900 dark:text-white mb-1 group-hover:text-primary">
                                    Créer du contenu
                                </h3>
                                <p class="text-sm text-center text-gray-600 dark:text-gray-400">
                                    Ajoutez vos premiers articles et pages
                                </p>
                            </div>
                        </a>
                        <a href="{{ route('admin.users') }}" class="group">
                            <div class="rounded-lg border shadow-lg bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl p-6 transition-transform group-hover:scale-105">
                                <div class="w-16 h-16 mb-4 rounded-full bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400 flex items-center justify-center mx-auto">
                                    <i class="fas fa-users fa-2x"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-center text-gray-900 dark:text-white mb-1 group-hover:text-primary">
                                    Gestion des utilisateurs
                                </h3>
                                <p class="text-sm text-center text-gray-600 dark:text-gray-400">
                                    Ajoutez des auteurs et modérateurs
                                </p>
                            </div>
                        </a>
                    </div>
                </div>
            </section>
        @endcan
    @endauth

    {{-- Recent Posts --}}
    <section class="py-16 bg-gray-50/50 dark:bg-slate-900/50">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between mb-12">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Contenu récent</h2>
                    <p class="text-lg text-gray-600 dark:text-gray-400">Vos derniers articles publiés</p>
                </div>
                @can('access_dashboard')
                    <a href="{{ route('admin.articles') }}">
                        <button class="border border-input bg-background hover:bg-accent hover:text-accent-foreground px-4 py-2 rounded inline-flex items-center gap-2">
                            <i class="fas fa-edit"></i>
                            Gérer le contenu
                        </button>
                    </a>
                @endcan
            </div>

            @if($recentArticles->count())
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    @foreach($recentArticles as $article)
                        <div class="rounded-lg border bg-white/80 dark:bg-slate-800/80 text-card-foreground shadow-lg backdrop-blur-xl overflow-hidden">
                            {{-- Image de l'article --}}
                            <a href="{{ route('posts.show', $article) }}">
                                <img src="{{ $article->thumbnail ?? 'https://placehold.co/600x400?text=Sans+image' }}"
                                     alt="{{ $article->title }}"
                                     class="w-full h-48 object-cover">
                            </a>

                            <div class="p-6">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold bg-secondary text-secondary-foreground backdrop-blur-sm">
                                        {{ $article->type ?? 'Article' }}
                                    </div>
                                    <span class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $article->published_at?->format('d/m/Y') ?? $article->created_at->format('d/m/Y') }}
                                </span>
                                </div>

                                <h3 class="text-xl font-semibold leading-tight mb-2 text-gray-900 dark:text-white">
                                    <a href="{{ route('posts.show', $article) }}">{{ $article->title }}</a>
                                </h3>

                                <p class="text-gray-600 dark:text-gray-400 mb-4">
                                    {{ Str::limit($article->description, 100) }}
                                </p>

                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <i class="fas fa-user text-gray-500"></i>
                                        <span class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $article->author->name ?? 'Inconnu' }}
                                    </span>
                                    </div>
                                    <a href="{{ route('posts.show', $article) }}" class="text-primary hover:text-primary/80 text-sm inline-flex items-center gap-1">
                                        Lire <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>


    {{-- Features Overview --}}
    <section class="py-16">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">Fonctionnalités principales</h2>
                <p class="text-xl text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                    Découvrez tout ce que Stratum CMS peut faire pour vous
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center rounded-lg border shadow-lg bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl p-6">
                    <div class="w-16 h-16 mb-4 mx-auto rounded-full bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                        <i class="fas fa-file-alt fa-2x"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Gestion de contenu</h3>
                    <p class="text-gray-600 dark:text-gray-400">Créez et gérez vos articles, pages et médias facilement</p>
                </div>
                <div class="text-center rounded-lg border shadow-lg bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl p-6">
                    <div class="w-16 h-16 mb-4 mx-auto rounded-full bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400 flex items-center justify-center">
                        <i class="fas fa-paint-brush fa-2x"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Thèmes personnalisables</h3>
                    <p class="text-gray-600 dark:text-gray-400">Personnalisez l'apparence avec nos thèmes modernes</p>
                </div>
                <div class="text-center rounded-lg border shadow-lg bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl p-6">
                    <div class="w-16 h-16 mb-4 mx-auto rounded-full bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 flex items-center justify-center">
                        <i class="fas fa-shield-alt fa-2x"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Sécurité avancée</h3>
                    <p class="text-gray-600 dark:text-gray-400">Protection renforcée et gestion des utilisateurs</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Final CTA --}}
    <section class="py-16">
        <div class="container mx-auto px-4 text-center">
            <div class="rounded-lg border shadow-2xl bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl max-w-4xl mx-auto p-12">
                <i class="fas fa-bolt fa-3x text-primary mb-6"></i>
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">Prêt à créer du contenu exceptionnel ?</h2>
                <p class="text-xl text-gray-600 dark:text-gray-400 mb-8">Votre CMS est configuré et prêt à l'emploi. Commencez dès maintenant !</p>
                @auth
                        <div class="flex flex-col sm:flex-row gap-4 justify-center">
                            @can('manage_articles')
                                <a href="{{ route('admin.articles.create') }}">
                                    <button class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 px-8 py-4 bg-primary text-primary-foreground hover:bg-primary/90 h-11">
                                        <i class="fas fa-plus"></i>
                                        Créer mon premier article
                                    </button>
                                </a>
                            @endcan
                            @can('manage_themes')
                                    <a href="{{ route('themes.index') }}">
                                        <button class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 border border-input bg-background hover:bg-accent hover:text-accent-foreground backdrop-blur-sm px-8 py-4 h-11">
                                            <i class="fas fa-paint-brush"></i>
                                            Personnaliser le thème
                                        </button>
                                    </a>
                            @endcan
                        </div>
                @else
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('login') }}">
                            <button class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 px-8 py-4 bg-primary text-primary-foreground hover:bg-primary/90 h-11">
                                <i class="fas fa-plus"></i>
                                Connexion
                            </button>
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </section>

@endsection
