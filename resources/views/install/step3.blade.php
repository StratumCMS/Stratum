@extends('install.layout')

@section('content')
    <div class="text-center">
        <h2 class="text-2xl font-bold mb-4 text-text">Création des rôles et permissions</h2>
        <p class="text-gray-400 mb-6">
            Les rôles et permissions nécessaires seront créés automatiquement.
        </p>
        <a href="{{ route('install.step4') }}"
           class="bg-primary text-white px-6 py-3 rounded-lg shadow-skeuo hover:bg-blue-600 transition">
            Continuer
        </a>
    </div>
@endsection
