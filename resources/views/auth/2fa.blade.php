@extends('layouts.app')

@section('content')
    <div class="max-w-md mx-auto mt-12 p-6 bg-white/50 dark:bg-slate-800/60 backdrop-blur-md rounded-2xl shadow-md">
        <h2 class="text-2xl font-semibold mb-4 flex items-center gap-2">
            <i class="fa-solid fa-shield-halved text-primary"></i>
            Vérification en deux étapes
        </h2>

        <p class="text-sm text-muted-foreground mb-4">
            Veuillez entrer le code généré par votre application d’authentification pour accéder à votre compte.
        </p>

        @if ($errors->any())
            <div class="text-red-600 text-sm mb-4">
                {{ $errors->first('otp') }}
            </div>
        @endif

        <form method="POST" action="{{ route('2fa.verify.challenge') }}" class="space-y-4">
            @csrf
            <input
                type="text"
                name="otp"
                inputmode="numeric"
                pattern="\d{6}"
                maxlength="6"
                required
                placeholder="Code à 6 chiffres"
                class="pl-10 w-full py-2 px-4 rounded-lg bg-white/70 dark:bg-slate-700/50 border border-gray-300 dark:border-slate-600 text-sm"
            />
            <button type="submit" class="w-full bg-primary text-white py-3 rounded-xl hover:bg-primary/90 transition">
                <i class="fa-solid fa-check mr-2"></i> Vérifier le code
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="mt-4 text-center">
            @csrf
            <button type="submit" class="text-sm text-muted-foreground hover:underline">
                Se déconnecter
            </button>
        </form>
    </div>
@endsection
