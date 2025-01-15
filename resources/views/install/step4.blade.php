@extends('install.layout')

@section('content')
    <h2 class="text-2xl font-bold mb-6 text-center text-text">Configuration générale</h2>
    <form method="POST" action="{{ route('install.storeStep4') }}" class="space-y-6">
        @csrf
        <div>
            <label class="block text-text font-medium mb-2">Nom du site</label>
            <input type="text" name="site_name" placeholder="Nom du site"
                   class="w-full border border-inputBorder bg-input text-text rounded-lg px-4 py-2 shadow-skeuo focus:ring focus:ring-primary transition">
        </div>
        <div>
            <label class="block text-text font-medium mb-2">URL du site</label>
            <input type="text" name="site_url" placeholder="URL du site"
                   class="w-full border border-inputBorder bg-input text-text rounded-lg px-4 py-2 shadow-skeuo focus:ring focus:ring-primary transition">
        </div>
        <div>
            <label class="block text-text font-medium mb-2">Description</label>
            <textarea name="site_description" placeholder="Description du site"
                      class="w-full border border-inputBorder bg-input text-text rounded-lg px-4 py-2 shadow-skeuo focus:ring focus:ring-primary transition"></textarea>
        </div>
        <div>
            <label class="block text-text font-medium mb-2">Mots-clés (SEO)</label>
            <input type="text" name="site_keywords" placeholder="Exemple : CMS, Laravel"
                   class="w-full border border-inputBorder bg-input text-text rounded-lg px-4 py-2 shadow-skeuo focus:ring focus:ring-primary transition">
        </div>
        <button type="submit"
                class="w-full bg-primary text-white px-6 py-3 rounded-lg shadow-skeuo hover:bg-blue-600 transition">
            Suivant
        </button>
    </form>
@endsection
