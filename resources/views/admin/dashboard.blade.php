@extends('admin.layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="space-y-6">

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach ($stats as $stat)
                <div class="rounded-lg border bg-card text-card-foreground shadow-sm hover-lift hover-glow-purple transition-all duration-300">
                    <div class="flex flex-row items-center justify-between space-y-0 p-6 pb-2">
                        <div class="text-sm font-medium text-muted-foreground">
                            {{ $stat['title'] }}
                        </div>
                        <div class="{{ $stat['color'] }} text-white w-8 h-8 rounded-lg flex items-center justify-center glow-purple">
                            <i class="fas {{ $stat['icon'] }} w-4 h-4"></i>
                        </div>
                    </div>
                    <div class="p-6 pt-0">
                        <div class="text-2xl font-bold text-glow-purple">{{ number_format($stat['value']) }}</div>
                        <div class="text-xs mt-1 {{ $stat['change'] >= 0 ? 'text-success' : 'text-destructive' }}">
                            {{ $stat['change'] >= 0 ? '+' : '' }}{{ $stat['change'] }}% ce mois
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-card rounded-xl p-6 border border-border">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-foreground">Visiteurs</h3>
                    <div class="flex space-x-2">
                        <button class="px-3 py-1 text-xs font-medium bg-primary text-primary-foreground rounded-md" data-range="7">7j</button>
                        <button class="px-3 py-1 text-xs font-medium text-muted-foreground hover:text-foreground rounded-md" data-range="30">30j</button>
                        <button class="px-3 py-1 text-xs font-medium text-muted-foreground hover:text-foreground rounded-md" data-range="90">90j</button>
                    </div>
                </div>
                <div class="h-64">
                    <canvas id="visitorsChart" width="400" height="200"></canvas>
                </div>
            </div>


            <div class="bg-card rounded-xl p-6 border border-border">
                <h3 class="text-lg font-semibold text-foreground mb-4">Activité récente</h3>
                <div class="space-y-4">
                    @forelse ($recentActivities as $activity)
                        @php
                            $typeColors = [
                                'page' => 'bg-blue-500',
                                'article' => 'bg-green-500',
                                'module' => 'bg-purple-500',
                                'theme' => 'bg-orange-500',
                                'media' => 'bg-cyan-600',
                                'user' => 'bg-pink-500',
                                'settings' => 'bg-red-500',
                            ];
                        @endphp
                        <div class="flex items-start space-x-3 animate-fade-in">
                            <div class="w-3 h-3 rounded-full {{ $typeColors[$activity->type] ?? 'bg-muted-foreground' }} mt-2"></div>
                            <div class="flex-1">
                                <div class="flex justify-between items-center">
                                    <p class="text-sm font-medium text-foreground">
                                        {{ $activity->action }} • {{ $activity->description }}
                                    </p>
                                    <span class="text-xs text-muted-foreground">
                            {{ $activity->created_at->diffForHumans() }}
                        </span>
                                </div>
                                <p class="text-xs text-muted-foreground mt-1">Par {{ $activity->user->name ?? 'Système' }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-muted-foreground">Aucune activité enregistrée.</p>
                    @endforelse
                </div>
            </div>

        </div>

        <div class="bg-card/50 backdrop-blur-sm rounded-xl p-6 border border-border hover-glow-purple">
            <h3 class="text-lg font-semibold text-foreground mb-4 text-glow-purple">Actions rapides</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{route('admin.pages')}}">
                    <div class="p-4 bg-primary/5 rounded-lg border border-primary/20 hover-lift hover-glow-purple cursor-pointer transition-all">
                        <h4 class="font-medium text-foreground">Créer une page</h4>
                        <p class="text-sm text-muted-foreground mt-1">Ajouter une nouvelle page à votre site</p>
                    </div>
                </a>
                <div class="p-4 bg-success/5 rounded-lg border border-success/20 hover-lift hover-glow-purple cursor-pointer transition-all">
                    <h4 class="font-medium text-foreground">Nouvel article</h4>
                    <p class="text-sm text-muted-foreground mt-1">Rédiger et publier un article</p>
                </div>
                <a href="{{route('modules.index')}}">
                    <div class="p-4 bg-purple-500/5 rounded-lg border border-purple-500/20 hover-lift hover-glow-purple cursor-pointer transition-all">
                        <h4 class="font-medium text-foreground">Installer un module</h4>
                        <p class="text-sm text-muted-foreground mt-1">Étendre les fonctionnalités</p>
                    </div>
                </a>
            </div>
        </div>

    </div>
@endsection
