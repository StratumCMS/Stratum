@extends('layouts.app')

@section('content')
    <div class="max-w-md mx-auto mt-12 p-6 bg-white/50 dark:bg-slate-800/60 backdrop-blur-md rounded-2xl shadow-md">
        <h1 class="text-3xl font-bold text-center text-gray-900 dark:text-white mb-2">
            Mot de passe oublié ?
        </h1>
        <p class="text-center text-gray-600 dark:text-gray-400 mb-6">
            Entrez votre adresse email pour recevoir un lien de réinitialisation.
        </p>

        @if (session('status'))
            <div class="text-green-600 text-sm mb-4 text-center">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
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

            <button type="submit" class="w-full bg-primary text-white py-3 rounded-xl hover:bg-primary/90 transition">
                Envoyer le lien de réinitialisation
            </button>
        </form>
    </div>
@endsection
