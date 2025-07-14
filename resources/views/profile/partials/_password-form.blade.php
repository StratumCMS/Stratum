<div class="rounded-lg border text-card-foreground backdrop-blur-xl bg-card/80 border-border/50 shadow-lg">
    <div class="flex flex-col space-y-1.5 p-6">
        <h2 class="text-2xl font-semibold leading-none tracking-tight">
            Changer le mot de passe
        </h2>
    </div>

    <div class="p-6 pt-0">
        <form method="POST" action="{{ route('password.update') }}" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Password: current --}}
            <div class="space-y-2">
                <label for="current_password" class="block text-sm font-medium">Mot de passe actuel</label>
                <div class="relative">
                    <input
                        id="current_password"
                        name="current_password"
                        type="password"
                        required
                        placeholder="Votre mot de passe actuel"
                        class="w-full px-3 py-2 border rounded-md pr-10"
                    />
                    <button
                        type="button"
                        onclick="togglePasswordVisibility('current_password')"
                        class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 hover:text-primary"
                        title="Afficher / masquer"
                    >
                        <i class="fa-solid fa-eye" id="icon_current_password"></i>
                    </button>
                </div>
            </div>

            {{-- Password: new --}}
            <div class="space-y-2">
                <label for="password" class="block text-sm font-medium">Nouveau mot de passe</label>
                <div class="relative">
                    <input
                        id="password"
                        name="password"
                        type="password"
                        required
                        placeholder="Votre nouveau mot de passe"
                        class="w-full px-3 py-2 border rounded-md pr-10"
                    />
                    <button
                        type="button"
                        onclick="togglePasswordVisibility('password')"
                        class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 hover:text-primary"
                        title="Afficher / masquer"
                    >
                        <i class="fa-solid fa-eye" id="icon_password"></i>
                    </button>
                </div>
                <p class="text-sm text-muted-foreground">
                    Au moins 8 caractères avec des lettres majuscules, minuscules et des chiffres.
                </p>
            </div>

            {{-- Password: confirmation --}}
            <div class="space-y-2">
                <label for="password_confirmation" class="block text-sm font-medium">Confirmer le mot de passe</label>
                <div class="relative">
                    <input
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        required
                        placeholder="Confirmez votre mot de passe"
                        class="w-full px-3 py-2 border rounded-md pr-10"
                    />
                    <button
                        type="button"
                        onclick="togglePasswordVisibility('password_confirmation')"
                        class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 hover:text-primary"
                        title="Afficher / masquer"
                    >
                        <i class="fa-solid fa-eye" id="icon_password_confirmation"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium bg-primary text-white rounded-md hover:bg-primary/90 transition">
                <i class="fa-solid fa-lock text-sm"></i>
                Changer le mot de passe
            </button>
        </form>
    </div>
</div>

@push('scripts')
    <script>
        function togglePasswordVisibility(fieldId) {
            const input = document.getElementById(fieldId);
            const icon = document.getElementById('icon_' + fieldId);

            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = "password";
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
@endpush
