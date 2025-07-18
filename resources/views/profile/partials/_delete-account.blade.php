<div class="space-y-6">

    <div class="relative w-full rounded-lg border p-4 [&>svg~*]:pl-7 [&>svg+div]:translate-y-[-3px] [&>svg]:absolute [&>svg]:left-4 [&>svg]:top-4 border-destructive/50 text-destructive dark:border-destructive [&>svg]:text-destructive">
        <i class="fas fa-triangle-exclamation h-4 w-4"></i>
        <div class="mb-1 font-medium leading-none tracking-tight">
            Attention !
        </div>
        <div class="text-sm [&_p]:leading-relaxed">
            La suppression de votre compte est définitive et irréversible.
            Toutes vos données seront perdues.
        </div>
    </div>

    {{-- Explications --}}
    <div class="space-y-4">

        <h3 class="text-lg font-semibold text-destructive">
            Que se passe-t-il quand vous supprimez votre compte ?
        </h3>
        <ul class="space-y-2 text-sm text-muted-foreground">
            <li>• Tous vos articles et commentaires seront supprimés</li>
            <li>• Votre profil et vos informations personnelles seront effacés</li>
            <li>• Vous perdrez l'accès à tous vos contenus sauvegardés</li>
            <li>• Cette action ne peut pas être annulée</li>
        </ul>
    </div>

    <form method="POST" action="{{ route('profile.destroy') }}" class="space-y-4 p-4 border border-destructive/20 rounded-lg bg-destructive/5">
        @csrf
        @method('DELETE')

        <h4 class="font-medium">Confirmations requises :</h4>

        {{-- Cases à cocher --}}
        <div class="space-y-3">
            <div class="flex items-start space-x-2">
                <label class="text-sm leading-5">
                    <input type="checkbox" name="agree_irreversible" required class="mt-1 rounded border-gray-300 text-red-600 dark:bg-slate-700 dark:border-slate-600">
                    Je comprends que cette action est irréversible
                </label>
            </div>

            <div class="flex items-start space-x-2">
                <label class="text-sm leading-5">
                    <input type="checkbox" name="agree_dataloss" required class="mt-1 rounded border-gray-300 text-red-600 dark:bg-slate-700 dark:border-slate-600">
                    J'accepte de perdre définitivement toutes mes données
                </label>
            </div>

            <div class="flex items-start space-x-2">
                <label class="text-sm leading-5">
                    <input type="checkbox" name="agree_confirm" required class="mt-1 rounded border-gray-300 text-red-600 dark:bg-slate-700 dark:border-slate-600">
                    Je confirme vouloir supprimer définitivement mon compte
                </label>
            </div>
        </div>

        {{-- Champ mot de passe --}}
        <div class="space-y-2">
            <label for="password" class="text-sm text-gray-700 dark:text-gray-300">Saisissez votre mot de passe</label>
            <div class="relative">
                <i class="fas fa-lock absolute left-3 top-3 text-gray-400"></i>
                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    class="pl-10 pr-10 flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm"
                    placeholder="••••••••"
                />
            </div>
            @error('password')
            <div class="text-red-600 text-sm">{{ $message }}</div>
            @enderror
        </div>

        {{-- Champ confirmation texte --}}
        <div class="space-y-2">
            <label for="confirm_text" class="text-sm text-gray-700 dark:text-gray-300">
                Tapez <strong>"SUPPRIMER"</strong> pour confirmer
            </label>
            <input
                id="confirm_text"
                name="confirm_text"
                type="text"
                required
                pattern="SUPPRIMER"
                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm"
                placeholder="SUPPRIMER"
            />
            @error('confirm_text')
            <div class="text-red-600 text-sm">{{ $message }}</div>
            @enderror
        </div>

        {{-- Bouton --}}
        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 bg-destructive text-destructive-foreground hover:bg-destructive/90 h-10 px-4 py-2">
            <i class="fas fa-trash-alt h-4 w-4 mr-2"></i> Supprimer définitivement mon compte
        </button>
    </form>

    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);

            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                input.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }
    </script>
</div>
