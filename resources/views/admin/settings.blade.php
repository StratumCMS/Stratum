@extends('admin.layouts.admin')

@section('title', 'Paramètres')

@section('content')
    <div class="max-w-4xl mx-auto">
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


                {{-- Onglet Général --}}
                <div x-show="tab === 'general'" class="flex flex-col p-6 rounded-lg border bg-card text-card-foreground shadow-sm space-y-5 hover-glow-purple"
                     x-data="{ maintenanceChecked: {{ setting('maintenance_mode') ? 'true' : 'false' }} }">
                    <x-setting.title icon="fas fa-cog" label="Paramètres généraux" />
                    <x-setting.input name="site_name" label="Nom du site" :value="setting('site_name')" />
                    <x-setting.textarea name="site_description" label="Description"
                                        :value="setting('site_description')" />
                    <x-setting.input name="site_keywords" label="Mots-clés"
                                     :value="setting('site_keywords')" />

                    <div @change="maintenanceChecked = $event.target.checked">
                        <x-setting.toggle
                            name="maintenance_mode"
                            label="Mode maintenance"
                            description="Désactiver temporairement l'accès public"
                            :checked="setting('maintenance_mode')" />
                    </div>

                    {{-- Détails maintenance --}}
                    <div x-show="maintenanceChecked" x-cloak class="mt-4 space-y-4 border-t pt-4">
                        <x-setting.input
                            name="maintenance_title"
                            label="Titre de la page de maintenance"
                            :value="setting('maintenance_title', 'Site en maintenance')"
                            description="Titre affiché aux visiteurs" />
                        <x-setting.textarea
                            name="maintenance_message"
                            label="Message de maintenance"
                            :value="setting('maintenance_message', 'Nous sommes actuellement en maintenance. Merci de revenir plus tard.')"
                            description="Message affiché aux visiteurs" />
                    </div>
                </div>

                {{-- SEO --}}
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

                {{-- Onglet Sécurité --}}
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

                    {{-- Manager IPs --}}
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

                        {{-- Important : encodage JSON des IPs --}}
                        <input type="hidden" name="ip_whitelist_list" :value="JSON.stringify(ips)">
                    </div>
                </div>





                {{-- Notifications --}}
                <div x-show="tab === 'notifications'" x-cloak class="flex flex-col p-6 rounded-lg border bg-card text-card-foreground shadow-sm space-y-5 hover-glow-purple">
                    <x-setting.title icon="fas fa-bell" label="Notifications" />
                    <x-setting.toggle name="email_notifications" label="Email"
                                      description="Recevoir des alertes par mail" :checked="setting('email_notifications')" />
                    <x-setting.toggle name="push_notifications" label="Push"
                                      description="Notifications en temps réel" :checked="setting('push_notifications')" />
                    <x-setting.toggle name="admin_notifications" label="Alertes admin"
                                      description="Recevoir les alertes critiques" :checked="setting('admin_notifications')" />
                </div>

                {{-- Sauvegarde --}}
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

                {{-- Performance --}}
                <div x-show="tab === 'performance'" x-cloak class="flex flex-col p-6 rounded-lg border bg-card text-card-foreground shadow-sm space-y-5 hover-glow-purple">
                    <x-setting.title icon="fas fa-tachometer-alt" label="Performance" />
                    <x-setting.toggle name="cache_enabled" label="Cache activé"
                                      description="Activer le cache pour accélérer le site" :checked="setting('cache_enabled')" />
                    <x-setting.toggle name="compression_enabled" label="Compression GZIP"
                                      description="Réduction de la taille des réponses" :checked="setting('compression_enabled')" />
                    <x-setting.toggle name="image_optimization" label="Optimisation des images"
                                      description="Améliore le chargement des images" :checked="setting('image_optimization')" />
                </div>

                {{-- Bouton Submit --}}
                <div class="text-right pt-6">
                    <button type="submit"
                            class="px-6 py-2 bg-primary text-white rounded hover:bg-primary/90 transition shadow">
                        <i class="fas fa-save mr-2"></i> Enregistrer les modifications
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/alpinejs" defer></script>
@endpush
