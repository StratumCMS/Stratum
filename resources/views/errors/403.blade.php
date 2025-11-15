@extends('layouts.app')

@section('title', 'Accès interdit')

@section('content')
    <div class="min-h-[70vh] flex items-center justify-center px-4 py-12">
        <div class="max-w-2xl w-full">
            <div class="relative">
                <div class="absolute inset-0 -z-10">
                    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-red-500/10 rounded-full blur-3xl animate-pulse"></div>
                </div>

                <div class="text-center space-y-8 animate-fade-in">
                    <div class="relative inline-block">
                        <div class="absolute inset-0 bg-red-500/20 rounded-full blur-2xl animate-pulse"></div>
                        <div class="relative bg-gradient-to-br from-red-500 to-red-600 rounded-full p-8 shadow-2xl transform hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-shield-alt text-6xl text-white animate-shake"></i>
                        </div>
                        <div class="absolute -top-2 -right-2 w-4 h-4 bg-red-400 rounded-full animate-ping"></div>
                        <div class="absolute -bottom-2 -left-2 w-3 h-3 bg-red-500 rounded-full animate-ping" style="animation-delay: 0.3s;"></div>
                    </div>

                    <div class="space-y-2">
                        <h1 class="text-8xl md:text-9xl font-bold bg-gradient-to-r from-red-600 to-red-400 bg-clip-text text-transparent animate-gradient">
                            403
                        </h1>
                        <div class="h-1 w-24 mx-auto bg-gradient-to-r from-transparent via-red-500 to-transparent rounded-full"></div>
                    </div>

                    <div class="space-y-4">
                        <h2 class="text-3xl md:text-4xl font-bold text-foreground">
                            Accès interdit
                        </h2>
                        <p class="text-lg text-muted-foreground max-w-md mx-auto">
                            Vous n'avez pas les permissions nécessaires pour accéder à cette ressource.
                            Cette zone est protégée.
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-3 justify-center items-center">
                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-red-500/10 text-red-600 dark:text-red-400 text-sm font-medium border border-red-500/20">
                            <i class="fas fa-lock"></i>
                            Accès refusé
                        </span>
                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-muted text-muted-foreground text-sm font-medium">
                            <i class="fas fa-exclamation-triangle"></i>
                            Permissions insuffisantes
                        </span>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4 justify-center pt-8">
                        <a href="{{ url()->previous() }}"
                           class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-lg border border-input bg-background hover:bg-accent hover:text-accent-foreground transition-all duration-300 transform hover:scale-105 font-medium">
                            <i class="fas fa-arrow-left"></i>
                            Retour
                        </a>
                        <a href="{{ url('/') }}"
                           class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-lg bg-primary text-primary-foreground hover:bg-primary/90 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl font-medium">
                            <i class="fas fa-home"></i>
                            Accueil
                        </a>
                    </div>

                    <div class="pt-8 border-t border-border">
                        <p class="text-sm text-muted-foreground">
                            Besoin d'accéder à cette page ?
                            <a href="{{ route('login') }}" class="text-primary hover:underline font-medium">
                                Connectez-vous
                            </a>
                            ou contactez un administrateur.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <style>
            @keyframes fade-in {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            @keyframes shake {
                0%, 100% { transform: rotate(0deg); }
                10%, 30%, 50%, 70%, 90% { transform: rotate(-5deg); }
                20%, 40%, 60%, 80% { transform: rotate(5deg); }
            }

            @keyframes gradient {
                0%, 100% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
            }

            .animate-fade-in {
                animation: fade-in 0.6s ease-out;
            }

            .animate-shake {
                animation: shake 2s ease-in-out infinite;
            }

            .animate-gradient {
                background-size: 200% 200%;
                animation: gradient 3s ease infinite;
            }
        </style>
    @endpush
@endsection
