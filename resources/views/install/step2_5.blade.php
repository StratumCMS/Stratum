<h2 class="text-2xl font-bold mb-6 text-center text-text">Choix du mode du CMS</h2>

<form method="POST" action="{{ route('install.storeStep2_5') }}" class="space-y-6">
    @csrf

    <div>
        <label class="block text-text font-medium mb-2">Mode d'installation</label>
        <select name="cms_mode" class="w-full bg-input border-inputBorder rounded-lg px-4 py-2">
            <option value="standard">Mode standard</option>
            <option value="headless">Mode headless (backend uniquement)</option>
        </select>
    </div>

    <div>
        <label class="block text-text font-medium mb-2">Type d'API</label>
        <select name="cms_api_type" class="w-full bg-input border-inputBorder rounded-lg px-4 py-2">
            <option value="rest">REST API</option>
            <option value="graphql">GraphQL</option>
        </select>
    </div>

    <button type="submit"
            class="w-full bg-primary text-white px-6 py-3 rounded-lg shadow-skeuo hover:bg-blue-600 transition">
        Suivant
    </button>
</form>
