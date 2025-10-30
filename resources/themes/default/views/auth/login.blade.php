@extends('theme::layouts.app')

@section('content')
    <section class="relative min-h-screen flex items-center justify-center bg-gradient-hero overflow-hidden py-20">
        <div class="absolute inset-0 -z-10">
            <div class="absolute top-1/4 left-1/4 w-72 h-72 bg-primary/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-1/3 right-1/4 w-96 h-96 bg-primary/5 rounded-full blur-3xl"></div>
        </div>

        <div class="container-custom relative z-10">
            <div class="max-w-md mx-auto bg-bg-elevated border border-border-subtle rounded-2xl shadow-2xl p-8 backdrop-blur-xl animate-fade-in">
                <h1 class="text-center text-fluid-3xl font-display font-bold text-text mb-6">
                    Connexion
                </h1>
                <p class="text-center text-text-muted mb-8">
                    Accédez à votre compte pour continuer.
                </p>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="mb-4 text-sm text-primary font-medium">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-6" novalidate>
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-text mb-2">Adresse email</label>
                        <input id="email"
                               type="email"
                               name="email"
                               value="{{ old('email') }}"
                               required autofocus
                               autocomplete="username"
                               class="pl-10 w-full py-2 px-4 rounded-lg bg-white/70 dark:bg-slate-700/50 border border-gray-300 dark:border-slate-600 text-sm"
                               aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}">
                        @error('email')
                        <p class="mt-2 text-sm text-red-500" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-text mb-2">Mot de passe</label>

                        <div class="relative">
                            <input id="password"
                                   type="password"
                                   name="password"
                                   required
                                   autocomplete="current-password"
                                   class="pl-10 w-full py-2 px-4 rounded-lg bg-white/70 dark:bg-slate-700/50 border border-gray-300 dark:border-slate-600 text-sm"
                                   aria-describedby="password-help"
                                   aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}">

                            <!-- show/hide button -->
                            <button type="button"
                                    id="togglePassword"
                                    aria-pressed="false"
                                    aria-label="Afficher le mot de passe"
                                    class="absolute inset-y-0 right-3 flex items-center justify-center p-1 text-text-muted focus:outline-none focus:ring-2 focus:ring-primary rounded">
                                <!-- eye icon (visible by default) -->
                                <svg id="iconEye" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>

                                <!-- eye-off icon (hidden by default) -->
                                <svg id="iconEyeOff" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.965 9.965 0 012.74-4.042M6.1 6.1A9.965 9.965 0 0112 5c4.477 0 8.268 2.943 9.542 7a9.99 9.99 0 01-4.903 5.675M3 3l18 18" />
                                </svg>
                            </button>
                        </div>

                        @error('password')
                        <p class="mt-2 text-sm text-red-500" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    @include('elements.captcha', ['center' => true])

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between text-sm">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="remember" class="rounded border-gray-300 text-primary">
                            Se souvenir de moi
                        </label>
                        <a href="{{ route('password.request') }}" class="text-primary hover:underline">
                            Mot de passe oublié ?
                        </a>
                    </div>

                    <!-- Submit -->
                    <div>
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-xl text-sm font-medium bg-primary text-primary-foreground hover:bg-primary/90 h-11 px-6 transition-colors duration-200 shadow-sm">
                            Se connecter
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M5 12h14"></path>
                                <path d="m12 5 7 7-7 7"></path>
                            </svg>
                        </button>
                    </div>
                </form>

                <div class="mt-8 text-center text-sm text-text-muted">
                    Pas encore de compte ?
                    <a href="{{ route('register') }}" class="text-primary hover:text-primary/80 font-medium">
                        Créer un compte
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Inline script pour toggle password — accessible et léger -->
    <script>
        (function () {
            const pwd = document.getElementById('password');
            const toggle = document.getElementById('togglePassword');
            const iconEye = document.getElementById('iconEye');
            const iconEyeOff = document.getElementById('iconEyeOff');

            if (!pwd || !toggle) return;

            toggle.addEventListener('click', function () {
                const isHidden = pwd.type === 'password';
                pwd.type = isHidden ? 'text' : 'password';
                toggle.setAttribute('aria-pressed', isHidden ? 'true' : 'false');
                toggle.setAttribute('aria-label', isHidden ? 'Masquer le mot de passe' : 'Afficher le mot de passe');

                if (isHidden) {
                    iconEye.classList.add('hidden');
                    iconEyeOff.classList.remove('hidden');
                } else {
                    iconEye.classList.remove('hidden');
                    iconEyeOff.classList.add('hidden');
                }
            });

            // Optionally allow toggle with Enter/Space when focused
            toggle.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    toggle.click();
                }
            });
        })();
    </script>
@endsection
