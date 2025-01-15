@extends('install.layout')

@section('content')
    <div class="text-center">
        <h2 class="text-2xl font-bold mb-4 text-text">Bienvenue sur StratumCMS</h2>
        <p class="text-gray-400 mb-6">
            Suivez les étapes ci-dessous pour configurer votre CMS en toute simplicité.
        </p>
        <a href="{{ route('install.step2') }}"
           class="bg-primary text-white px-6 py-3 rounded-lg shadow-skeuo hover:bg-blue-600 transition">
            Commencer
        </a>
    </div>
@endsection
