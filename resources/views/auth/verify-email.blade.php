@extends('layouts.app')

@section('content')
    <div class="max-w-md mx-auto mt-12 p-6 bg-white/50 dark:bg-slate-800/60 backdrop-blur-md rounded-2xl shadow-md text-center">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
            Vérification de l'adresse email
        </h1>

        <p class="text-gray-600 dark:text-gray-400 mb-4">
            Merci de vous être inscrit ! Avant de commencer, veuillez vérifier votre adresse e-mail
            en cliquant sur le lien que nous venons de vous envoyer.
        </p>

        @if (session('status') == 'verification-link-sent')
            <div class="text-green-600 text-sm mb-4">
                Un nouveau lien de vérification a été envoyé à votre adresse e-mail.
            </div>
        @endif

        <div class="flex items-center justify-between gap-4 mt-6">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary/90 transition">
                    Renvoyer l'e-mail
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm text-gray-600 dark:text-gray-400 hover:underline">
                    Se déconnecter
                </button>
            </form>
        </div>
    </div>
@endsection
