@extends('layouts.app')

@section('content')
    <div class="max-w-md mx-auto mt-12 p-6 bg-white/50 dark:bg-slate-800/60 backdrop-blur-md rounded-2xl shadow-md">
        <h1 class="text-3xl font-bold text-center text-gray-900 dark:text-white mb-2">
            Connexion
        </h1>
        <p class="text-center text-gray-600 dark:text-gray-400 mb-6">
            Accédez à votre espace
        </p>

        @if (session('status'))
            <div class="text-green-600 text-sm mb-4">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            <div class="space-y-2">
                <label for="email" class="text-gray-700 dark:text-gray-300">Adresse email</label>
                <div class="relative">
                    <i class="fas fa-envelope absolute left-3 top-3 text-gray-400"></i>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        placeholder="votre@email.com"
                        class="pl-10 w-full py-2 px-4 rounded-lg bg-white/70 dark:bg-slate-700/50 border border-gray-300 dark:border-slate-600 text-sm"
                    />
                </div>
                @error('email')
                <div class="text-red-600 text-sm">{{ $message }}</div>
                @enderror
            </div>

            <div class="space-y-2">
                <label for="password" class="text-gray-700 dark:text-gray-300">Mot de passe</label>
                <div class="relative">
                    <i class="fas fa-lock absolute left-3 top-3 text-gray-400"></i>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        required
                        placeholder="••••••••"
                        class="pl-10 pr-10 w-full py-2 px-4 rounded-lg bg-white/70 dark:bg-slate-700/50 border border-gray-300 dark:border-slate-600 text-sm"
                    />
                    <button
                        type="button"
                        onclick="togglePassword()"
                        class="absolute right-3 top-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                    >
                        <i id="eye-icon" class="fas fa-eye"></i>
                    </button>
                </div>
                @error('password')
                <div class="text-red-600 text-sm">{{ $message }}</div>
                @enderror
            </div>

            @include('elements.captcha', ['center' => true])

            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="remember" class="rounded border-gray-300 text-primary">
                    Se souvenir de moi
                </label>
                <a href="{{ route('password.request') }}" class="text-primary hover:underline">
                    Mot de passe oublié ?
                </a>
            </div>

            <div class="mt-4 text-center text-sm">
                Pas encore inscrit ?
                <a href="{{ route('register') }}" class="text-primary hover:underline">Créer un compte</a>
            </div>

            <button type="submit" class="w-full bg-primary text-white py-3 rounded-xl hover:bg-primary/90 transition">
                Se connecter
            </button>
        </form>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById("password");
            const icon = document.getElementById("eye-icon");
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                input.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }
    </script>
@endsection
