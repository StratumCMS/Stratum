<form method="POST" action="{{ route('profile.destroy') }}" class="space-y-6">
    @csrf
    @method('DELETE')

    <p class="text-sm text-muted-foreground">
        Une fois votre compte supprimé, toutes vos données seront définitivement perdues. Veuillez confirmer votre mot de passe.
    </p>

    <div>
        <label class="block text-sm mb-1">Mot de passe</label>
        <input name="password" type="password" class="w-full input">
    </div>

    <button type="submit" class="btn-danger">Supprimer mon compte</button>
</form>
