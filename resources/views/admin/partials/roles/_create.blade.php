<x-modal name="createRoleModal">
    <form method="POST" action="{{ route('admin.roles.store') }}" class="space-y-6 px-6 py-4">
        @csrf

        <h2 class="text-xl font-bold mb-4">Créer un nouveau rôle</h2>

        @include('admin.partials.roles._form')

        <div class="flex justify-end space-x-2 mt-6 border-t pt-4">
            <button type="button" class="btn btn-secondary" x-on:click="$dispatch('close-modal', 'createRoleModal')">
                Annuler
            </button>
            <button type="submit" class="btn btn-primary">Enregistrer</button>
        </div>
    </form>
</x-modal>
