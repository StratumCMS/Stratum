@extends('layouts.app')

@section('content')
    <div class="max-w-md mx-auto mt-12 p-6 bg-white/50 dark:bg-slate-800/60 backdrop-blur-md rounded-2xl shadow-md">
        <h1 class="text-2xl font-bold text-center text-gray-900 dark:text-white mb-2">
            Confirmation requise
        </h1>
        <p class="text-center text-gray-600 dark:text-gray-400 mb-6">
            Veuillez confirmer votre mot de passe pour continuer.
        </p>

        <form method="POST" action="{{ route('password.confirm') }}" class="space-y-6">
            @csrf

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
                        onclick="togglePassword('password', 'eye-icon-password')"
                        class="absolute right-3 top-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                    >
                        <i id="eye-icon-password" class="fas fa-eye"></i>
                    </button>
                </div>
                @error('password')
                <div class="text-red-600 text-sm">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="w-full bg-primary text-white py-3 rounded-xl hover:bg-primary/90 transition">
                Confirmer
            </button>
        </form>
    </div>

    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);

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
