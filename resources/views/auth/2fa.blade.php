@extends('layouts.app')

@section('content')
    <div class="max-w-md mx-auto mt-10 p-6 bg-white rounded-lg shadow">
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
                class="w-full px-4 py-2 border rounded-md text-center tracking-widest text-lg"
            />
            <button type="submit" class="btn-primary w-full">
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
