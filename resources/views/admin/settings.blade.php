@extends('admin.layouts.admin')

@section('title', 'Paramètres')

@section('content')
    <div class="max-w-4xl mx-auto" x-data="mediaSelector()">
        @if (session('success'))
            <div class="mb-6 p-4 bg-green-100 text-green-800 rounded-lg border border-green-300 shadow">
                <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-100 text-red-800 rounded-lg border border-red-300 shadow">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li><i class="fas fa-exclamation-circle mr-1"></i> {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-8">
            @csrf

            <div x-data="{ tab: 'general' }" class="space-y-8">
                <div class="flex flex-wrap justify-start gap-2 border-b pb-2 mb-4">
                    <template x-for="tabInfo in [
                        { key: 'general', label: 'Général', icon: 'fas fa-cog' },
                        { key: 'seo', label: 'SEO', icon: 'fas fa-search' },
                        { key: 'security', label: 'Sécurité', icon: 'fas fa-shield-alt' },
                        { key: 'notifications', label: 'Notifications', icon: 'fas fa-bell' },
                        { key: 'backup', label: 'Sauvegarde', icon: 'fas fa-database' },
                        { key: 'performance', label: 'Performance', icon: 'fas fa-tachometer-alt' }
                    ]">
                        <button
                            type="button"
                            @click="tab = tabInfo.key"
                            :class="{
                                'bg-primary text-white shadow-sm': tab === tabInfo.key,
                                'bg-muted text-muted-foreground hover:bg-muted/80': tab !== tabInfo.key
                            }"
                            class="flex items-center space-x-2 px-4 py-2 rounded-lg text-sm font-medium transition-all">
                            <i :class="tabInfo.icon"></i>
                            <span x-text="tabInfo.label"></span>
                        </button>
                    </template>
                </div>

                <div x-show="tab === 'general'" class="flex flex-col p-6 rounded-lg border bg-card text-card-foreground shadow-sm space-y-5 hover-glow-purple" x-data="{ maintenanceChecked: {{ setting('maintenance_mode') ? 'true' : 'false' }} }">
                    <x-setting.title icon="fas fa-cog" label="Paramètres généraux" />
                    <x-setting.input name="site_name" label="Nom du site" :value="setting('site_name')" />
                    <x-setting.input name="site_url" label="URL du site" :value="setting('site_url', config('app.url'))" />
                    <x-setting.textarea name="site_description" label="Description" :value="setting('site_description')" />
                    <x-setting.input name="site_keywords" label="Mots-clés" :value="setting('site_keywords')" />

                    <div class="space-y-2">
                        <x-setting.label text="Logo du site" />
                        <template x-if="site_logo">
                            <div class="relative w-40">
                                <img :src="site_logo" class="rounded border shadow-sm">
                                <button type="button" @click="clear('site_logo')" class="btn btn-destructive btn-sm absolute top-1 right-1">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </template>
                        <button type="button" @click="openModal('site_logo')" class="btn btn-outline">
                            <i class="fas fa-image mr-2"></i> Choisir le logo
                        </button>
                        <input type="hidden" name="site_logo" :value="site_logo">
                    </div>

                    <div class="space-y-2">
                        <x-setting.label text="Favicon" />
                        <template x-if="site_favicon">
                            <div class="relative w-16">
                                <img :src="site_favicon" class="rounded border shadow-sm">
                                <button type="button" @click="clear('site_favicon')" class="btn btn-destructive btn-sm absolute top-1 right-1">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </template>
                        <button type="button" @click="openModal('site_favicon')" class="btn btn-outline">
                            <i class="fas fa-image mr-2"></i> Choisir le favicon
                        </button>
                        <input type="hidden" name="site_favicon" :value="site_favicon">
                    </div>

                    <div @change="maintenanceChecked = $event.target.checked">
                        <x-setting.toggle name="maintenance_mode" label="Mode maintenance" description="Désactiver temporairement l'accès public" :checked="setting('maintenance_mode')" />
                    </div>

                    <div x-show="maintenanceChecked" x-cloak class="mt-4 space-y-4 border-t pt-4">
                        <x-setting.input name="maintenance_title" label="Titre de la page de maintenance" :value="setting('maintenance_title', 'Site en maintenance')" />
                        <x-setting.textarea name="maintenance_message" label="Message de maintenance" :value="setting('maintenance_message', 'Nous sommes actuellement en maintenance. Merci de revenir plus tard.')" />
                    </div>

                    <x-setting.input name="copyright" label="Copyright" :value="setting('copyright')" />
                    <x-setting.input name="site_key" label="Clé license" :value="setting('site_key')" />
                </div>

                <div x-show="tab === 'seo'" x-cloak class="flex flex-col p-6 rounded-lg border bg-card text-card-foreground shadow-sm space-y-5 hover-glow-purple">
                    <x-setting.title icon="fas fa-search" label="Référencement (SEO)" />
                    <x-setting.toggle name="seo_enabled" label="SEO activé" description="Activer les options SEO"
                                      :checked="setting('seo_enabled')" />
                    <x-setting.toggle name="xml_sitemap" label="Plan du site XML" description="Générer le sitemap.xml"
                                      :checked="setting('xml_sitemap')" />
                    <x-setting.toggle name="robots_txt" label="Fichier robots.txt"
                                      description="Contrôle de l'indexation par les moteurs de recherche"
                                      :checked="setting('robots_txt')" />
                </div>
                <div x-show="tab === 'security'" x-cloak
                     class="flex flex-col p-6 rounded-lg border bg-card text-card-foreground shadow-sm space-y-5 hover-glow-purple"
                     x-data="{
        whitelistEnabled: {{ setting('ip_whitelist') ? 'true' : 'false' }},
        newIp: '',
        ips: {{ json_encode(setting('ip_whitelist_list', [])) }}
     }">
                    <x-setting.title icon="fas fa-shield-alt" label="Sécurité" />

                    <x-setting.toggle name="two_factor_auth" label="2FA"
                                      description="Authentification à deux facteurs"
                                      :checked="setting('two_factor_auth')" />

                    <x-setting.toggle name="login_attempts" label="Tentatives limitées"
                                      description="Limiter les connexions échouées"
                                      :checked="setting('login_attempts')" />

                    <div @change="whitelistEnabled = $event.target.checked">
                        <x-setting.toggle name="ip_whitelist" label="Liste blanche IP"
                                          description="Limiter l'accès admin à certaines IP"
                                          :checked="setting('ip_whitelist')" />
                    </div>

                    <div x-show="whitelistEnabled" x-cloak class="mt-4 space-y-4 border-t pt-4">
                        <x-setting.label text="Adresse IP à ajouter" />

                        <div class="grid grid-cols-1 sm:grid-cols-[1fr_auto] gap-2 items-end">
                            <x-setting.input
                                label="Nouvelle IP"
                                name="Nouvelle IP"
                                placeholder="192.168.1.1"
                                x-model="newIp"
                            />

                            <button
                                type="button"
                                class="bg-primary text-white px-4 py-2 rounded-md text-sm hover:bg-primary/90 transition shadow"
                                @click="
                    let ip = newIp.trim();
                    if (ip && !ips.includes(ip)) {
                        ips.push(ip);
                        newIp = '';
                    }
                "
                            >
                                <i class="fas fa-plus mr-1"></i> Ajouter
                            </button>
                        </div>

                        <template x-if="ips.length">
                            <ul class="space-y-2 mt-2">
                                <template x-for="(ip, index) in ips" :key="index">
                                    <li class="flex items-center justify-between bg-muted px-3 py-2 rounded shadow-sm">
                                        <span x-text="ip" class="font-mono text-sm text-muted-foreground"></span>
                                        <button
                                            type="button"
                                            class="text-red-600 hover:text-red-800"
                                            @click="ips.splice(index, 1)"
                                        >
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </li>
                                </template>
                            </ul>
                        </template>

                        <input type="hidden" name="ip_whitelist_list" :value="JSON.stringify(ips)">
                    </div>

                    <div x-data="{ captchaEnabled: {{ setting('captcha_enabled') ? 'true' : 'false' }} }">
                        <div @change="captchaEnabled = $event.target.checked">
                            <x-setting.toggle
                                name="captcha_enabled"
                                label="Activer CAPTCHA"
                                description="Protéger les formulaires avec un système CAPTCHA"
                                :checked="setting('captcha_enabled')"
                            />
                        </div>

                        <div x-show="captchaEnabled" x-cloak class="mt-4 space-y-4 border-t pt-4">
                            <x-setting.select name="captcha.type" label="Type de CAPTCHA" :value="setting('captcha_type', 'recaptcha')">
                                <option value="recaptcha">Google reCAPTCHA</option>
                                <option value="hcaptcha">hCaptcha</option>
                                <option value="turnstile">Cloudflare Turnstile</option>
                            </x-setting.select>

                            <x-setting.input name="captcha.site_key" label="Site Key" :value="setting('captcha_site_key')" />
                            <x-setting.input name="captcha.secret_key" label="Clé secrète" :value="setting('captcha_secret_key')" />
                        </div>
                    </div>

                    <div x-data="{ emailEnabled: {{ setting('email_enabled') ? 'true' : 'false' }} }">
                        <div @change="emailEnabled = $event.target.checked">
                            <x-setting.toggle
                                name="email_enabled"
                                label="Activer les e-mails"
                                description="Permet d'envoyer des e-mails via SMTP ou un service tiers"
                                :checked="setting('email_enabled')" />
                        </div>

                        <div x-show="emailEnabled" x-cloak class="space-y-4 border-t pt-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <x-setting.select name="mail.driver" label="Driver" :value="setting('mail_driver')">
                                    <option value="smtp">SMTP</option>
                                    <option value="sendmail">Sendmail</option>
                                    <option value="mailgun">Mailgun</option>
                                    <option value="sendgrid">SendGrid</option>
                                </x-setting.select>

                                <x-setting.input name="mail.host" label="Hôte SMTP" :value="setting('mail_host')" />
                                <x-setting.input name="mail.port" label="Port" :value="setting('mail_port')" />
                                <x-setting.input name="mail.encryption" label="Chiffrement (tls/ssl)" :value="setting('mail_encryption')" />
                                <x-setting.input name="mail.username" label="Nom d'utilisateur" :value="setting('mail_username')" />
                                <x-setting.input name="mail.password" label="Mot de passe" :value="setting('mail_password')" type="password" />
                                <x-setting.input name="mail.from_address" label="Email expéditeur" :value="setting('mail_from_address')" />
                                <x-setting.input name="mail.from_name" label="Nom expéditeur" :value="setting('mail_from_name')" />
                            </div>

                        </div>
                    </div>




                </div>

                <div x-show="tab === 'notifications'" x-cloak class="flex flex-col p-6 rounded-lg border bg-card text-card-foreground shadow-sm space-y-5 hover-glow-purple">
                    <x-setting.title icon="fas fa-bell" label="Notifications" />
                    <x-setting.toggle name="email_notifications" label="Email"
                                      description="Recevoir des alertes par mail" :checked="setting('email_notifications')" />
                    <x-setting.toggle name="push_notifications" label="Push"
                                      description="Notifications en temps réel" :checked="setting('push_notifications')" />
                    <x-setting.toggle name="admin_notifications" label="Alertes admin"
                                      description="Recevoir les alertes critiques" :checked="setting('admin_notifications')" />
                </div>

                <div x-show="tab === 'backup'" x-cloak class="flex flex-col p-6 rounded-lg border bg-card text-card-foreground shadow-sm space-y-5 hover-glow-purple">
                    <x-setting.title icon="fas fa-database" label="Sauvegarde" />
                    <x-setting.toggle name="auto_backup" label="Auto sauvegarde"
                                      description="Sauvegarde planifiée automatique" :checked="setting('auto_backup')" />
                    <x-setting.select name="backup_frequency" label="Fréquence de sauvegarde" :value="setting('backup_frequency', 'daily')">
                        <option value="daily">Tous les jours</option>
                        <option value="weekly">Chaque semaine</option>
                        <option value="monthly">Chaque mois</option>
                        <option value="yearly">Chaque année</option>
                    </x-setting.select>

                </div>

                <div x-show="tab === 'performance'" x-cloak class="flex flex-col p-6 rounded-lg border bg-card text-card-foreground shadow-sm space-y-5 hover-glow-purple">
                    <x-setting.title icon="fas fa-tachometer-alt" label="Performance" />
                    <x-setting.toggle name="cache_enabled" label="Cache activé"
                                      description="Activer le cache pour accélérer le site" :checked="setting('cache_enabled')" />
                    <x-setting.toggle name="compression_enabled" label="Compression GZIP"
                                      description="Réduction de la taille des réponses" :checked="setting('compression_enabled')" />
                    <x-setting.toggle name="image_optimization" label="Optimisation des images"
                                      description="Améliore le chargement des images" :checked="setting('image_optimization')" />
                </div>

                <div class="text-right pt-6">
                    <button type="submit" class="px-6 py-2 bg-primary text-white rounded hover:bg-primary/90 transition shadow">
                        <i class="fas fa-save mr-2"></i> Enregistrer les modifications
                    </button>
                </div>
            </div>
        </form>

            <div x-show="modalOpen" class="fixed inset-0 bg-black/50 flex items-center justify-center px-4 z-50">
                <div class="bg-card rounded-lg max-w-3xl w-full p-6 space-y-4" @click.away="closeModal()">
                    <div class="flex justify-between items-center">
                        <h2 class="text-lg font-semibold">Bibliothèque de médias</h2>
                        <button @click="closeModal()" class="text-muted-foreground"><i class="fas fa-times"></i></button>
                    </div>

                    <div class="grid grid-cols-4 gap-4 overflow-y-auto max-h-80">
                        <template x-for="media in mediaItems" :key="media.id">
                            <div @click="selectMedia(media)" class="cursor-pointer border hover:bg-muted/10 p-1 rounded">
                                <img :src="media.url" class="w-full h-20 object-cover rounded">
                            </div>
                        </template>
                    </div>
                </div>
            </div>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/alpinejs" defer></script>
    <script>
        function mediaSelector() {
            return {
                modalOpen: false,
                mediaItems: @json($mediaItems),
                activeField: null,

                site_logo: '{{ setting('site_logo') }}',
                site_favicon: '{{ setting('site_favicon') }}',

                openModal(field) {
                    this.activeField = field;
                    this.modalOpen = true;
                },

                closeModal() {
                    this.modalOpen = false;
                    this.activeField = null;
                },

                selectMedia(media) {
                    if (this.activeField === 'site_logo') {
                        this.site_logo = media.url;
                    } else if (this.activeField === 'site_favicon') {
                        this.site_favicon = media.url;
                    }
                    this.closeModal();
                },

                clear(field) {
                    if (field === 'site_logo') {
                        this.site_logo = '';
                    } else if (field === 'site_favicon') {
                        this.site_favicon = '';
                    }
                }
            }
        }
    </script>
@endpush

