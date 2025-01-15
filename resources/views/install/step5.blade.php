@extends('install.layout')

@section('content')
    <h2 class="text-2xl font-bold mb-6 text-center text-text">Créer un administrateur</h2>
    <form method="POST" action="{{ route('install.storeStep5') }}" class="space-y-6">
        @csrf
        <div>
            <label class="block text-text font-medium mb-2">Nom</label>
            <input type="text" name="admin_name" placeholder="Nom complet"
                   class="w-full border border-inputBorder bg-input text-text rounded-lg px-4 py-2 shadow-skeuo focus:ring focus:ring-primary transition">
        </div>
        <div>
            <label class="block text-text font-medium mb-2">Email</label>
            <input type="email" name="admin_email" placeholder="Adresse email"
                   class="w-full border border-inputBorder bg-input text-text rounded-lg px-4 py-2 shadow-skeuo focus:ring focus:ring-primary transition">
        </div>
        <div>
            <label class="block text-text font-medium mb-2">Mot de passe</label>
            <input type="password" name="admin_password" placeholder="Mot de passe"
                   class="w-full border border-inputBorder bg-input text-text rounded-lg px-4 py-2 shadow-skeuo focus:ring focus:ring-primary transition">
        </div>
        <div>
            <label class="block text-text font-medium mb-2">Confirmer le mot de passe</label>
            <input type="password" name="admin_password_confirmation" placeholder="Confirmer le mot de passe"
                   class="w-full border border-inputBorder bg-input text-text rounded-lg px-4 py-2 shadow-skeuo focus:ring focus:ring-primary transition">
        </div>
        <button type="submit"
                class="w-full bg-primary text-white px-6 py-3 rounded-lg shadow-skeuo hover:bg-blue-600 transition">
            Finaliser
        </button>
    </form>
@endsection
