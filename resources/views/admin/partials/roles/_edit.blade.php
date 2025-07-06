<x-modal name="editRoleModal-{{ $role->id }}">
    <form method="POST" action="{{ route('admin.roles.update', $role) }}" class="space-y-6 px-6 py-4">
        @csrf
        @method('PUT')

        <h2 class="text-xl font-bold mb-4">Modifier le rôle « {{ $role->name }} »</h2>

        @include('admin.partials.roles._form', ['role' => $role])

        <div class="flex justify-end space-x-2 mt-4 border-t pt-4">
            <button type="button" class="btn btn-secondary" x-on:click="$dispatch('close-modal', 'editRoleModal-{{ $role->id }}')">Annuler</button>
            <button type="submit" class="btn btn-primary">Enregistrer</button>
        </div>
    </form>
</x-modal>
