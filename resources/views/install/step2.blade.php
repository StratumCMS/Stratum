@extends('install.layout')

@section('content')
    <h2 class="text-2xl font-bold mb-6 text-center text-text">Configuration de la base de données</h2>
    <form method="POST" action="{{ route('install.storeStep2') }}" class="space-y-6">
        @csrf
        <div>
            <label class="block text-text font-medium mb-2">Hôte</label>
            <input type="text" name="db_host" value="127.0.0.1"
                   class="w-full border border-inputBorder bg-input text-text rounded-lg px-4 py-2 shadow-skeuo focus:ring focus:ring-primary transition">
        </div>
        <div>
            <label class="block text-text font-medium mb-2">Port</label>
            <input type="number" name="db_port" value="3306"
                   class="w-full border border-inputBorder bg-input text-text rounded-lg px-4 py-2 shadow-skeuo focus:ring focus:ring-primary transition">
        </div>
        <div>
            <label class="block text-text font-medium mb-2">Nom de la base de données</label>
            <input type="text" name="db_database" placeholder="Nom de la base"
                   class="w-full border border-inputBorder bg-input text-text rounded-lg px-4 py-2 shadow-skeuo focus:ring focus:ring-primary transition">
        </div>
        <div>
            <label class="block text-text font-medium mb-2">Utilisateur</label>
            <input type="text" name="db_username" value="root"
                   class="w-full border border-inputBorder bg-input text-text rounded-lg px-4 py-2 shadow-skeuo focus:ring focus:ring-primary transition">
        </div>
        <div>
            <label class="block text-text font-medium mb-2">Mot de passe</label>
            <input type="password" name="db_password" placeholder="Mot de passe"
                   class="w-full border border-inputBorder bg-input text-text rounded-lg px-4 py-2 shadow-skeuo focus:ring focus:ring-primary transition">
        </div>
        <button type="submit"
                class="w-full bg-primary text-white px-6 py-3 rounded-lg shadow-skeuo hover:bg-blue-600 transition">
            Suivant
        </button>
    </form>
@endsection
