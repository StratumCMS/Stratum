<div x-data="twoFactor()" x-init="init()" class="space-y-6">
    <h2 class="text-2xl font-semibold leading-none tracking-tight">Authentification à deux facteurs</h2>

    {{-- Statut 2FA --}}
    <div class="flex items-center justify-between p-4 border rounded-lg bg-card shadow-sm">
        <div class="flex items-center space-x-3">
            <i class="fa-solid fa-shield"
               :class="enabled ? 'text-green-500' : 'text-muted-foreground'"
               class="text-xl"></i>
            <div>
                <p class="font-medium" x-text="enabled ? '2FA activée' : '2FA désactivée'"></p>
                <p class="text-sm text-muted-foreground"
                   x-text="enabled ? 'Votre compte est protégé.' : 'Activez cette protection pour plus de sécurité.'">
                </p>
            </div>
        </div>

        {{-- Toggle Button --}}
        <template x-if="!enabled">
            <button
                @click="startSetup"
                type="button"
                class="btn-primary">
                <i class="fa-solid fa-lock"></i>
                <span>Activer 2FA</span>
            </button>
        </template>

        <template x-if="enabled">
            <form method="POST" action="{{ route('2fa.disable') }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-danger">
                    <i class="fa-solid fa-lock-open"></i> Désactiver 2FA
                </button>
            </form>
        </template>
    </div>

    {{-- Setup étape 1 + 2 --}}
    <div x-show="setupVisible" x-transition>
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
            <div class="p-6 border-b">
                <h3 class="text-lg font-semibold flex items-center gap-2">
                    <i class="fa-solid fa-qrcode"></i> Étape 1 : Scanner le QR Code
                </h3>
                <p class="text-sm text-muted-foreground mt-1">
                    Scannez ce code avec une application comme Google Authenticator.
                </p>
            </div>
            <div class="p-6 text-center" x-show="qr && secret">
                <div class="inline-block p-4 bg-card rounded-lg border shadow">
                    <img :src="qr" alt="QR Code pour 2FA" class="w-48 h-48 mx-auto" />
                </div>
                <p class="text-sm text-muted-foreground mt-2">
                    Clé manuelle : <code x-text="secret"></code>
                </p>
            </div>
        </div>

        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
            <div class="p-6 border-b">
                <h3 class="text-lg font-semibold flex items-center gap-2">
                    <i class="fa-solid fa-mobile-screen-button"></i> Étape 2 : Vérifier le code
                </h3>
                <p class="text-sm text-muted-foreground mt-1">
                    Entrez le code généré par votre application d'authentification.
                </p>
            </div>
            <form method="POST" action="{{ route('2fa.verify') }}" class="p-6 space-y-4">
                @csrf
                <input
                    name="otp"
                    type="text"
                    inputmode="numeric"
                    maxlength="6"
                    pattern="\d{6}"
                    required
                    placeholder="Code à 6 chiffres"
                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm text-center tracking-widest"
                />
                @error('otp')
                <p class="text-red-500 text-sm">{{ $message }}</p>
                @enderror
                <button type="submit" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2">
                    <i class="fa-solid fa-check-circle"></i> Vérifier et activer
                </button>
            </form>
        </div>
    </div>

    {{-- Codes de secours --}}
    @if ($user->two_factor_enabled && session('backup_codes'))
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
            <div class="p-6 border-b">
                <h3 class="text-lg font-semibold">Codes de récupération</h3>
                <p class="text-sm text-muted-foreground">
                    Ces codes vous permettent d'accéder à votre compte si vous perdez votre appareil.
                </p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 font-mono text-sm">
                    @foreach (session('backup_codes') as $code)
                        <div class="p-2 bg-muted rounded border">{{ $code }}</div>
                    @endforeach
                </div>
                <div class="mt-4 text-sm text-red-500 flex items-start gap-2">
                    <i class="fa-solid fa-triangle-exclamation mt-1"></i>
                    <span><strong>Important :</strong> chaque code est à usage unique. Sauvegardez-les !</span>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
    <script>
        function twoFactor() {
            return {
                enabled: @json($user->two_factor_enabled),
                setupVisible: false,
                secret: null,
                qr: null,
                loading: false,

                init() {
                    // Si la session contient les infos de setup
                    if (@json(session('2fa_setup'))) {
                        this.setupVisible = true;
                        this.secret = @json(session('2fa_secret'));
                        this.qr = @json(session('2fa_qr'));
                    }
                },

                async startSetup() {
                    if (this.loading) return;

                    this.loading = true;

                    try {
                        const res = await fetch('{{ route('2fa.enable') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            }
                        });

                        if (!res.ok) {
                            const errorText = await res.text();
                            console.error('Erreur:', errorText);
                            alert("Erreur lors de l'activation 2FA.");
                            return;
                        }

                        const data = await res.json();
                        this.secret = data.secret;
                        this.qr = data.qr;
                        this.setupVisible = true;
                    } catch (err) {
                        console.error('Exception:', err);
                        alert("Une erreur est survenue.");
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    </script>
@endpush

