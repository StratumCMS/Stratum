@extends('admin.layouts.admin')

@section('title', 'Paramètres')

@section('content')
    <div class="max-w-6xl mx-auto space-y-6" x-data="settingsApp()" x-init="init()">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
                    <i class="fas fa-cogs text-primary text-sm"></i>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-semibold text-foreground">Paramètres du système</h1>
                    <p class="text-sm text-muted-foreground hidden sm:block">Configurez les paramètres de votre application</p>
                </div>
            </div>
        </div>

        <template x-if="successMessage">
            <div class="p-4 rounded-xl bg-green-500/10 border border-green-500/20 text-green-600 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-check-circle"></i>
                    <span x-text="successMessage"></span>
                </div>
                <button @click="successMessage = ''" class="text-green-600 hover:text-green-800">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </template>

        <template x-if="errorMessage">
            <div class="p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-600 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-exclamation-circle"></i>
                    <span x-text="errorMessage"></span>
                </div>
                <button @click="errorMessage = ''" class="text-red-600 hover:text-red-800">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </template>

        @if ($errors->any())
            <div class="p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-600">
                <div class="flex items-center space-x-2 mb-2">
                    <i class="fas fa-exclamation-circle"></i>
                    <span class="font-medium">Des erreurs sont présentes dans le formulaire :</span>
                </div>
                <ul class="list-disc pl-5 space-y-1 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="settings-form" action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
            @csrf

            <div class="border-b border-border">
                <nav class="-mb-px flex space-x-2 sm:space-x-8 overflow-x-auto">
                    <template x-for="tab in tabs" :key="tab.key">
                        <button
                            type="button"
                            @click="setActiveTab(tab.key)"
                            :class="{
                                'border-primary text-primary': activeTab === tab.key,
                                'border-transparent text-muted-foreground hover:text-foreground hover:border-foreground/20': activeTab !== tab.key
                            }"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors flex items-center space-x-2"
                        >
                            <i :class="tab.icon" class="w-4 h-4"></i>
                            <span x-text="tab.label"></span>
                        </button>
                    </template>
                </nav>
            </div>

            <div x-show="activeTab === 'general'" class="space-y-6" x-cloak>
                <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
                    <div class="p-4 sm:p-6 border-b border-border">
                        <x-setting.title icon="fas fa-cog" label="Paramètres généraux" />
                    </div>
                    <div class="p-4 sm:p-6 space-y-6">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                            <x-setting.input name="site_name" label="Nom du site" :value="setting('site_name')" required />
                            <x-setting.input name="site_url" label="URL du site" :value="setting('site_url', config('app.url'))" required />
                        </div>

                        <x-setting.textarea name="site_description" label="Description du site" :value="setting('site_description')" />
                        <x-setting.input name="site_keywords" label="Mots-clés" :value="setting('site_keywords')"
                                         description="Séparez les mots-clés par des virgules" />

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <x-setting.label text="Logo du site" />
                                <div class="flex items-start space-x-4">
                                    <template x-if="site_logo">
                                        <div class="relative">
                                            <img :src="site_logo" class="w-20 h-20 rounded-lg border shadow-sm object-cover">
                                            <button type="button" @click="clearMedia('site_logo')"
                                                    class="absolute -top-2 -right-2 w-6 h-6 bg-destructive text-destructive-foreground rounded-full flex items-center justify-center text-xs shadow-lg">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </template>
                                    <div class="flex-1">
                                        <button type="button" @click="openMediaModal('site_logo')"
                                                class="inline-flex items-center justify-center gap-2 rounded-lg border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2 text-sm font-medium transition-colors w-full">
                                            <i class="fas fa-image"></i>
                                            <span x-text="site_logo ? 'Changer le logo' : 'Choisir un logo'"></span>
                                        </button>
                                        <p class="text-xs text-muted-foreground mt-2">
                                            Recommandé : 200×200px, format PNG ou SVG
                                        </p>
                                    </div>
                                </div>
                                <input type="hidden" name="site_logo" x-model="site_logo">
                            </div>

                            <div class="space-y-4">
                                <x-setting.label text="Favicon" />
                                <div class="flex items-start space-x-4">
                                    <template x-if="site_favicon">
                                        <div class="relative">
                                            <img :src="site_favicon" class="w-12 h-12 rounded-lg border shadow-sm object-cover">
                                            <button type="button" @click="clearMedia('site_favicon')"
                                                    class="absolute -top-2 -right-2 w-6 h-6 bg-destructive text-destructive-foreground rounded-full flex items-center justify-center text-xs shadow-lg">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </template>
                                    <div class="flex-1">
                                        <button type="button" @click="openMediaModal('site_favicon')"
                                                class="inline-flex items-center justify-center gap-2 rounded-lg border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2 text-sm font-medium transition-colors w-full">
                                            <i class="fas fa-image"></i>
                                            <span x-text="site_favicon ? 'Changer le favicon' : 'Choisir un favicon'"></span>
                                        </button>
                                        <p class="text-xs text-muted-foreground mt-2">
                                            Recommandé : 32×32px, format ICO ou PNG
                                        </p>
                                    </div>
                                </div>
                                <input type="hidden" name="site_favicon" x-model="site_favicon">
                            </div>
                        </div>

                        <div x-data="{ maintenanceMode: {{ setting('maintenance_mode') ? 'true' : 'false' }} }">
                            <x-setting.toggle name="maintenance_mode" label="Mode maintenance"
                                              description="Désactiver temporairement l'accès public"
                                              :checked="setting('maintenance_mode')"
                                              @change="maintenanceMode = $event.target.checked" />

                            <div x-show="maintenanceMode" x-cloak class="mt-6 space-y-4 border-t pt-6">
                                <x-setting.input name="maintenance_title" label="Titre de la page de maintenance"
                                                 :value="setting('maintenance_title', 'Site en maintenance')" />
                                <x-setting.textarea name="maintenance_message" label="Message de maintenance"
                                                    :value="setting('maintenance_message', 'Nous sommes actuellement en maintenance. Merci de revenir plus tard.')" />
                            </div>
                        </div>

                        <x-setting.input name="copyright" label="Copyright" :value="setting('copyright')" />
                        <x-setting.input name="site_key" label="Clé de licence" :value="setting('site_key')"
                                         description="Clé de licence pour les mises à jour et le support" />
                    </div>
                </div>
            </div>

            <div x-show="activeTab === 'seo'" class="space-y-6" x-cloak>
                <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
                    <div class="p-4 sm:p-6 border-b border-border">
                        <x-setting.title icon="fas fa-search" label="Référencement (SEO)" />
                    </div>
                    <div class="p-4 sm:p-6 space-y-6">
                        <x-setting.toggle name="seo_enabled" label="SEO activé"
                                          description="Activer les options SEO avancées"
                                          :checked="setting('seo_enabled')" />

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <x-setting.toggle name="xml_sitemap" label="Plan du site XML"
                                              description="Générer automatiquement le sitemap.xml"
                                              :checked="setting('xml_sitemap')" />
                            <x-setting.toggle name="robots_txt" label="Fichier robots.txt"
                                              description="Contrôle de l'indexation par les moteurs de recherche"
                                              :checked="setting('robots_txt')" />
                        </div>

                        <div class="p-4 rounded-lg bg-blue-500/10 border border-blue-500/20">
                            <div class="flex items-start space-x-3">
                                <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
                                <div class="flex-1">
                                    <h4 class="text-sm font-medium text-foreground mb-1">Conseils SEO</h4>
                                    <ul class="text-xs text-muted-foreground space-y-1">
                                        <li>• Utilisez des mots-clés pertinents dans vos titres et descriptions</li>
                                        <li>• Optimisez les images avec des balises alt descriptives</li>
                                        <li>• Créez un contenu de qualité et régulièrement mis à jour</li>
                                        <li>• Assurez-vous que votre site est mobile-friendly</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div x-show="activeTab === 'security'" class="space-y-6" x-cloak>
                <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
                    <div class="p-4 sm:p-6 border-b border-border">
                        <x-setting.title icon="fas fa-shield-alt" label="Sécurité" />
                    </div>
                    <div class="p-4 sm:p-6 space-y-6">
                        <div class="space-y-4">
                            <h3 class="text-lg font-medium text-foreground">Authentification</h3>
                            <x-setting.toggle name="two_factor_auth" label="Authentification à deux facteurs (2FA)"
                                              description="Renforce la sécurité de la connexion"
                                              :checked="setting('two_factor_auth')" />
                            <x-setting.toggle name="login_attempts" label="Limitation des tentatives de connexion"
                                              description="Bloque les attaques par force brute"
                                              :checked="setting('login_attempts')" />
                        </div>

                        <div x-data="ipWhitelist()" class="space-y-4">
                            <x-setting.toggle name="ip_whitelist" label="Liste blanche IP"
                                              description="Limiter l'accès admin à certaines IP"
                                              :checked="setting('ip_whitelist')"
                                              @change="whitelistEnabled = $event.target.checked" />

                            <div x-show="whitelistEnabled" x-cloak class="mt-4 space-y-4 p-4 rounded-lg border border-border bg-muted/20">
                                <x-setting.label text="Gestion des adresses IP autorisées" />

                                <div class="grid grid-cols-1 sm:grid-cols-[1fr_auto] gap-3 items-end">
                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-foreground">Nouvelle IP</label>
                                        <input type="text" x-model="newIp" placeholder="192.168.1.1"
                                               class="flex h-10 w-full rounded-lg border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                               @keydown.enter.prevent="addIp">
                                    </div>
                                    <button type="button" @click="addIp"
                                            class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2 text-sm font-medium transition-colors whitespace-nowrap">
                                        <i class="fas fa-plus"></i>
                                        <span class="hidden sm:inline">Ajouter</span>
                                    </button>
                                </div>

                                <template x-if="ips.length > 0">
                                    <div class="space-y-3">
                                        <p class="text-sm font-medium text-foreground">
                                            IPs autorisées (<span x-text="ips.length"></span>)
                                        </p>
                                        <div class="space-y-2">
                                            <template x-for="(ip, index) in ips" :key="index">
                                                <div class="flex items-center justify-between bg-background px-3 py-2 rounded-lg border border-border">
                                                    <div class="flex items-center space-x-3">
                                                        <i class="fas fa-network-wired text-muted-foreground"></i>
                                                        <span x-text="ip" class="font-mono text-sm text-foreground"></span>
                                                    </div>
                                                    <button type="button" @click="removeIp(index)"
                                                            class="text-destructive hover:text-destructive/80 transition-colors p-1 rounded">
                                                        <i class="fas fa-trash-alt w-4 h-4"></i>
                                                    </button>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>

                                <template x-if="ips.length === 0">
                                    <p class="text-sm text-muted-foreground italic text-center py-4">
                                        Aucune IP autorisée. Ajoutez-en une ci-dessus.
                                    </p>
                                </template>

                                <input type="hidden" name="ip_whitelist_list" x-model="JSON.stringify(ips)">
                            </div>
                        </div>

                        <div x-data="{ captchaEnabled: {{ setting('captcha_enabled') ? 'true' : 'false' }} }" class="space-y-4">
                            <x-setting.toggle name="captcha_enabled" label="Protection CAPTCHA"
                                              description="Protéger les formulaires publics"
                                              :checked="setting('captcha_enabled')"
                                              @change="captchaEnabled = $event.target.checked" />

                            <div x-show="captchaEnabled" x-cloak class="mt-4 space-y-4 p-4 rounded-lg border border-border bg-muted/20">
                                <x-setting.select name="captcha_type" label="Type de CAPTCHA" :value="setting('captcha_type', 'recaptcha')">
                                    <option value="recaptcha">Google reCAPTCHA v3</option>
                                    <option value="hcaptcha">hCaptcha</option>
                                    <option value="turnstile">Cloudflare Turnstile</option>
                                </x-setting.select>

                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                    <x-setting.input name="captcha_site_key" label="Clé du site" :value="setting('captcha_site_key')" />
                                    <x-setting.input name="captcha_secret_key" label="Clé secrète" :value="setting('captcha_secret_key')" type="password" />
                                </div>
                            </div>
                        </div>

                        <div x-data="{ emailEnabled: {{ setting('email_enabled') ? 'true' : 'false' }} }" class="space-y-4">
                            <x-setting.toggle name="email_enabled" label="Système d'emails"
                                              description="Activer l'envoi d'emails"
                                              :checked="setting('email_enabled')"
                                              @change="emailEnabled = $event.target.checked" />

                            <div x-show="emailEnabled" x-cloak class="mt-4 space-y-4 p-4 rounded-lg border border-border bg-muted/20">
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                    <x-setting.select name="mail_driver" label="Driver email" :value="setting('mail_driver', 'smtp')">
                                        <option value="smtp">SMTP</option>
                                        <option value="sendmail">Sendmail</option>
                                        <option value="mailgun">Mailgun</option>
                                        <option value="sendgrid">SendGrid</option>
                                        <option value="ses">Amazon SES</option>
                                    </x-setting.select>

                                    <x-setting.input name="mail_host" label="Hôte SMTP" :value="setting('mail_host')" />
                                    <x-setting.input name="mail_port" label="Port" :value="setting('mail_port', '587')" />
                                    <x-setting.select name="mail_encryption" label="Chiffrement" :value="setting('mail_encryption', 'tls')">
                                        <option value="">Aucun</option>
                                        <option value="tls">TLS</option>
                                        <option value="ssl">SSL</option>
                                    </x-setting.select>

                                    <x-setting.input name="mail_username" label="Nom d'utilisateur" :value="setting('mail_username')" />
                                    <x-setting.input name="mail_password" label="Mot de passe" :value="setting('mail_password')" type="password" />
                                    <x-setting.input name="mail_from_address" label="Email expéditeur" :value="setting('mail_from_address')" />
                                    <x-setting.input name="mail_from_name" label="Nom expéditeur" :value="setting('mail_from_name')" />
                                </div>

                                <div class="flex justify-end">
                                    <button type="button" @click="testEmailConfiguration"
                                            class="inline-flex items-center justify-center gap-2 rounded-lg border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2 text-sm font-medium transition-colors">
                                        <i class="fas fa-paper-plane"></i>
                                        Tester la configuration
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div x-show="activeTab === 'notifications'" class="space-y-6" x-cloak>
                <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
                    <div class="p-4 sm:p-6 border-b border-border">
                        <x-setting.title icon="fas fa-bell" label="Notifications" />
                    </div>
                    <div class="p-4 sm:p-6 space-y-6">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <x-setting.toggle name="email_notifications" label="Notifications par email"
                                              description="Recevoir des alertes par mail"
                                              :checked="setting('email_notifications')" />
                            <x-setting.toggle name="push_notifications" label="Notifications push"
                                              description="Notifications en temps réel"
                                              :checked="setting('push_notifications')" />
                            <x-setting.toggle name="admin_notifications" label="Alertes administrateur"
                                              description="Recevoir les alertes critiques"
                                              :checked="setting('admin_notifications')" />
                            <x-setting.toggle name="user_notifications" label="Notifications utilisateur"
                                              description="Activer les notifications pour les utilisateurs"
                                              :checked="setting('user_notifications')" />
                        </div>

                        <div class="p-4 rounded-lg bg-amber-500/10 border border-amber-500/20">
                            <div class="flex items-start space-x-3">
                                <i class="fas fa-info-circle text-amber-500 mt-0.5"></i>
                                <div class="flex-1">
                                    <h4 class="text-sm font-medium text-foreground mb-1">Configuration des notifications</h4>
                                    <ul class="text-xs text-muted-foreground space-y-1">
                                        <li>• Les notifications email nécessitent une configuration SMTP valide</li>
                                        <li>• Les notifications push nécessitent un service compatible</li>
                                        <li>• Les alertes administrateur concernent les événements critiques</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div x-show="activeTab === 'backup'" class="space-y-6" x-cloak>
                <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
                    <div class="p-4 sm:p-6 border-b border-border">
                        <x-setting.title icon="fas fa-database" label="Sauvegarde" />
                    </div>
                    <div class="p-4 sm:p-6 space-y-6">
                        <x-setting.toggle name="auto_backup" label="Sauvegarde automatique"
                                          description="Sauvegarde planifiée automatique"
                                          :checked="setting('auto_backup')" />

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <x-setting.select name="backup_frequency" label="Fréquence de sauvegarde" :value="setting('backup_frequency', 'daily')">
                                <option value="hourly">Toutes les heures</option>
                                <option value="daily">Tous les jours</option>
                                <option value="weekly">Chaque semaine</option>
                                <option value="monthly">Chaque mois</option>
                            </x-setting.select>

                            <x-setting.input name="backup_retention" label="Rétention (jours)" :value="setting('backup_retention', '30')"
                                             type="number" description="Nombre de jours de conservation des sauvegardes" />
                        </div>

                        <div class="p-4 rounded-lg bg-green-500/10 border border-green-500/20">
                            <div class="flex items-start space-x-3">
                                <i class="fas fa-shield-alt text-green-500 mt-0.5"></i>
                                <div class="flex-1">
                                    <h4 class="text-sm font-medium text-foreground mb-1">Sécurité des sauvegardes</h4>
                                    <ul class="text-xs text-muted-foreground space-y-1">
                                        <li>• Les sauvegardes incluent la base de données et les fichiers</li>
                                        <li>• Stockage sécurisé avec chiffrement</li>
                                        <li>• Test régulier de restauration recommandé</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-4 pt-4 border-t border-border">
                            <button type="button" @click="createBackup"
                                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2 text-sm font-medium transition-colors">
                                <i class="fas fa-plus"></i>
                                Créer une sauvegarde manuelle
                            </button>
                            <button type="button" @click="restoreBackup"
                                    class="inline-flex items-center justify-center gap-2 rounded-lg border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2 text-sm font-medium transition-colors">
                                <i class="fas fa-undo"></i>
                                Restaurer une sauvegarde
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div x-show="activeTab === 'performance'" class="space-y-6" x-cloak>
                <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
                    <div class="p-4 sm:p-6 border-b border-border">
                        <x-setting.title icon="fas fa-tachometer-alt" label="Performance" />
                    </div>
                    <div class="p-4 sm:p-6 space-y-6">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <x-setting.toggle name="cache_enabled" label="Cache activé"
                                              description="Activer le cache pour accélérer le site"
                                              :checked="setting('cache_enabled')" />
                            <x-setting.toggle name="compression_enabled" label="Compression GZIP"
                                              description="Réduction de la taille des réponses"
                                              :checked="setting('compression_enabled')" />
                            <x-setting.toggle name="image_optimization" label="Optimisation des images"
                                              description="Améliore le chargement des images"
                                              :checked="setting('image_optimization')" />
                            <x-setting.toggle name="lazy_loading" label="Chargement différé"
                                              description="Charge les images au fur et à mesure"
                                              :checked="setting('lazy_loading')" />
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <x-setting.select name="cache_driver" label="Moteur de cache" :value="setting('cache_driver', 'file')">
                                <option value="file">Fichiers</option>
                                <option value="redis">Redis</option>
                                <option value="memcached">Memcached</option>
                                <option value="database">Base de données</option>
                            </x-setting.select>

                            <x-setting.input name="cache_ttl" label="Durée de cache (minutes)" :value="setting('cache_ttl', '60')"
                                             type="number" description="Temps de conservation en cache" />
                        </div>

                        <div class="p-4 rounded-lg bg-purple-500/10 border border-purple-500/20">
                            <div class="flex items-start space-x-3">
                                <i class="fas fa-rocket text-purple-500 mt-0.5"></i>
                                <div class="flex-1">
                                    <h4 class="text-sm font-medium text-foreground mb-1">Optimisation des performances</h4>
                                    <ul class="text-xs text-muted-foreground space-y-1">
                                        <li>• Le cache réduit significativement les temps de chargement</li>
                                        <li>• La compression GZIP économise la bande passante</li>
                                        <li>• L'optimisation d'images améliore le Core Web Vitals</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end pt-4 border-t border-border">
                            <button type="button" @click="clearCache"
                                    class="inline-flex items-center justify-center gap-2 rounded-lg border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2 text-sm font-medium transition-colors">
                                <i class="fas fa-broom"></i>
                                Vider le cache
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-6 border-t border-border">
                <div class="flex items-center space-x-2 text-sm text-muted-foreground">
                    <i class="fas fa-info-circle"></i>
                    <span>Les modifications sont sauvegardées immédiatement</span>
                </div>

                <div class="flex items-center space-x-3">
                    <button type="button" @click="resetForm"
                            class="inline-flex items-center justify-center gap-2 rounded-lg border border-input bg-background hover:bg-accent hover:text-accent-foreground h-11 px-6 py-2 text-sm font-medium transition-colors">
                        <i class="fas fa-undo"></i>
                        Réinitialiser
                    </button>
                    <button type="submit" :disabled="isSubmitting"
                            class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary text-primary-foreground hover:bg-primary/90 h-11 px-6 py-2 text-sm font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas" :class="isSubmitting ? 'fa-spinner fa-spin' : 'fa-save'"></i>
                        <span x-text="isSubmitting ? 'Sauvegarde...' : 'Enregistrer les modifications'"></span>
                    </button>
                </div>
            </div>
        </form>

        <div x-show="mediaModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" x-cloak>
            <div class="bg-card rounded-xl shadow-2xl max-w-4xl w-full max-h-[80vh] flex flex-col" @click.away="closeMediaModal">
                <div class="flex items-center justify-between p-6 border-b border-border">
                    <h2 class="text-lg font-semibold text-foreground">Bibliothèque de médias</h2>
                    <button @click="closeMediaModal" class="text-muted-foreground hover:text-foreground p-2 rounded-lg">
                        <i class="fas fa-times w-5 h-5"></i>
                    </button>
                </div>

                <div class="flex-1 overflow-hidden">
                    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-4 p-6 overflow-y-auto max-h-[60vh]">
                        <template x-for="media in mediaItems" :key="media.id">
                            <div @click="selectMedia(media)"
                                 class="cursor-pointer border-2 rounded-lg p-2 transition-all hover:border-primary hover:shadow-md"
                                 :class="{ 'border-primary': isSelected(media) }">
                                <img :src="media.url" class="w-full h-20 object-cover rounded">
                                <p class="text-xs text-muted-foreground truncate mt-2" x-text="media.name"></p>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="flex justify-end p-6 border-t border-border">
                    <button @click="closeMediaModal"
                            class="inline-flex items-center justify-center gap-2 rounded-lg border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2 text-sm font-medium transition-colors">
                        Annuler
                    </button>
                </div>
            </div>
        </div>

        <div x-show="modal.open" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/50" x-cloak
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">

            <div class="bg-card rounded-xl shadow-2xl max-w-md w-full mx-auto max-h-[85vh] flex flex-col transform transition-all sm:modal-mobile-full"
                 x-show="modal.open"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 @click.away="modal.open = false">

                <div class="flex items-center justify-between p-6 border-b border-border">
                    <div class="flex items-center space-x-3">
                        <i class="fas text-lg" :class="modal.icon"></i>
                        <h2 class="text-lg font-semibold text-foreground" x-text="modal.title"></h2>
                    </div>
                    <button @click="modal.open = false"
                            class="text-muted-foreground hover:text-foreground p-2 rounded-lg transition-colors">
                        <i class="fas fa-times w-5 h-5"></i>
                    </button>
                </div>

                <div class="flex-1 p-6 overflow-y-auto modal-content-mobile">
                    <p class="text-foreground" x-text="modal.message"></p>

                    <template x-if="modal.type === 'confirm'">
                        <div class="mt-4 space-y-3">
                            <input type="text" x-model="modal.inputValue"
                                   placeholder="Entrez la valeur de confirmation..."
                                   class="flex h-10 w-full rounded-lg border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2">
                            <p class="text-xs text-muted-foreground" x-text="modal.instructions"></p>
                        </div>
                    </template>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 p-6 border-t border-border">
                    <template x-if="modal.type === 'confirm'">
                        <button @click="modal.resolve(false)"
                                class="order-2 sm:order-1 inline-flex items-center justify-center gap-2 rounded-lg border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2 text-sm font-medium transition-colors flex-1">
                            Annuler
                        </button>
                    </template>

                    <button @click="modal.resolve(true)"
                            :class="{
                                'bg-destructive text-destructive-foreground hover:bg-destructive/90': modal.type === 'confirm',
                                'bg-primary text-primary-foreground hover:bg-primary/90': modal.type !== 'confirm'
                            }"
                            class="order-1 sm:order-2 inline-flex items-center justify-center gap-2 rounded-lg h-10 px-4 py-2 text-sm font-medium transition-colors flex-1">
                        <span x-text="modal.confirmText || 'OK'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        @media (max-width: 640px) {
            .modal-mobile-full {
                margin: 0;
                border-radius: 0;
                max-height: 100vh;
                height: 100vh;
                width: 100%;
            }

            .modal-content-mobile {
                padding: 1rem;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        function settingsApp() {
            return {
                activeTab: 'general',
                isSubmitting: false,
                successMessage: '',
                errorMessage: '',
                mediaModalOpen: false,
                activeMediaField: null,
                mediaItems: @json($mediaItems ?? []),

                modal: {
                    open: false,
                    type: 'info',
                    title: '',
                    message: '',
                    icon: 'fa-info-circle',
                    confirmText: 'OK',
                    instructions: '',
                    inputValue: '',
                    resolve: null
                },

                site_logo: '{{ setting('site_logo') ?? '' }}',
                site_favicon: '{{ setting('site_favicon') ?? '' }}',

                tabs: [
                    { key: 'general', label: 'Général', icon: 'fas fa-cog' },
                    { key: 'seo', label: 'SEO', icon: 'fas fa-search' },
                    { key: 'security', label: 'Sécurité', icon: 'fas fa-shield-alt' },
                    { key: 'notifications', label: 'Notifications', icon: 'fas fa-bell' },
                    { key: 'backup', label: 'Sauvegarde', icon: 'fas fa-database' },
                    { key: 'performance', label: 'Performance', icon: 'fas fa-tachometer-alt' }
                ],

                init() {
                    window.settingsApp = this;

                    const savedTab = localStorage.getItem('settings_active_tab');
                    if (savedTab && this.tabs.some(tab => tab.key === savedTab)) {
                        this.activeTab = savedTab;
                    }

                    if (this.mediaItems.length === 0) {
                        this.loadMediaItems();
                    }
                },

                async showModal(type, message, title = null, options = {}) {
                    return new Promise((resolve) => {
                        const config = {
                            info: { icon: 'fa-info-circle text-blue-500', title: 'Information' },
                            error: { icon: 'fa-exclamation-triangle text-red-500', title: 'Erreur' },
                            confirm: { icon: 'fa-question-circle text-amber-500', title: 'Confirmation' }
                        }[type];

                        this.modal = {
                            open: true,
                            type,
                            title: title || config.title,
                            message,
                            icon: config.icon,
                            confirmText: options.confirmText || 'Confirmer',
                            instructions: options.instructions || '',
                            inputValue: '',
                            resolve: (result) => {
                                this.modal.open = false;
                                resolve(result);
                            }
                        };
                    });
                },

                setActiveTab(tab) {
                    this.activeTab = tab;
                    localStorage.setItem('settings_active_tab', tab);
                },

                async loadMediaItems() {
                    try {
                        const response = await fetch('{{ route('admin.media') }}?json=1');

                        const contentType = response.headers.get('content-type');
                        if (!contentType || !contentType.includes('application/json')) {
                            throw new Error('Réponse non-JSON reçue du serveur');
                        }

                        const data = await response.json();
                        this.mediaItems = data.media || [];
                    } catch (error) {
                        console.error('Erreur chargement médias:', error);

                        this.mediaItems = [
                            {
                                id: 1,
                                name: 'Logo par défaut',
                                url: '/images/default-logo.png',
                                type: 'image'
                            },
                            {
                                id: 2,
                                name: 'Favicon par défaut',
                                url: '/images/default-favicon.png',
                                type: 'image'
                            }
                        ];

                        this.showModal('error', 'Impossible de charger la bibliothèque de médias: ' + error.message);
                    }
                },

                openMediaModal(field) {
                    this.activeMediaField = field;
                    this.mediaModalOpen = true;
                },

                closeMediaModal() {
                    this.mediaModalOpen = false;
                    this.activeMediaField = null;
                },

                selectMedia(media) {
                    if (this.activeMediaField === 'site_logo') {
                        this.site_logo = media.url;
                    } else if (this.activeMediaField === 'site_favicon') {
                        this.site_favicon = media.url;
                    }
                    this.closeMediaModal();
                },

                isSelected(media) {
                    if (this.activeMediaField === 'site_logo') {
                        return this.site_logo === media.url;
                    } else if (this.activeMediaField === 'site_favicon') {
                        return this.site_favicon === media.url;
                    }
                    return false;
                },

                clearMedia(field) {
                    if (field === 'site_logo') {
                        this.site_logo = '';
                    } else if (field === 'site_favicon') {
                        this.site_favicon = '';
                    }
                },

                async submitForm() {
                    this.isSubmitting = true;
                    this.successMessage = '';
                    this.errorMessage = '';

                    try {
                        const form = document.getElementById('settings-form');
                        const formData = new FormData(form);

                        const response = await fetch(form.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        });

                        const data = await response.json();

                        if (response.ok) {
                            this.successMessage = data.message || 'Paramètres sauvegardés avec succès';
                            this.scrollToTop();

                            setTimeout(() => {
                                if (data.reload) {
                                    window.location.reload();
                                }
                            }, 1500);
                        } else {
                            this.errorMessage = data.message || 'Une erreur est survenue lors de la sauvegarde';
                            if (data.errors) {
                                this.displayFormErrors(data.errors);
                            }
                        }
                    } catch (error) {
                        console.error('Erreur:', error);
                        this.showModal('error', 'Erreur de connexion lors de la sauvegarde');
                    }

                    this.isSubmitting = false;
                },

                displayFormErrors(errors) {
                    let errorMessages = [];
                    for (const [field, messages] of Object.entries(errors)) {
                        errorMessages.push(...messages);
                    }
                    this.errorMessage = errorMessages.join(', ');
                },

                async testEmailConfiguration() {
                    try {
                        this.isSubmitting = true;
                        const response = await fetch('{{ route('settings.test_email') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        });

                        const data = await response.json();

                        if (response.ok) {
                            this.successMessage = data.message || 'Email de test envoyé avec succès';
                        } else {
                            this.showModal('error', data.message || 'Erreur lors de l\'envoi de l\'email de test');
                        }
                    } catch (error) {
                        console.error('Erreur test email:', error);
                        this.showModal('error', 'Erreur de connexion lors du test email');
                    }

                    this.isSubmitting = false;
                },

                async createBackup() {
                    const confirmed = await this.showModal(
                        'confirm',
                        'Êtes-vous sûr de vouloir créer une sauvegarde manuelle ?',
                        'Créer une sauvegarde',
                        { confirmText: 'Créer la sauvegarde' }
                    );

                    if (!confirmed) return;

                    try {
                        const response = await fetch('{{ route('admin.backup.create') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });

                        const data = await response.json();

                        if (response.ok) {
                            this.successMessage = data.message || 'Sauvegarde créée avec succès';
                        } else {
                            this.showModal('error', data.message || 'Erreur lors de la création de la sauvegarde');
                        }
                    } catch (error) {
                        console.error('Erreur sauvegarde:', error);
                        this.showModal('error', 'Erreur de connexion lors de la création de la sauvegarde');
                    }
                },

                async restoreBackup() {
                    this.showModal('info', 'Fonctionnalité de restauration à implémenter', 'Restauration');
                },

                async clearCache() {
                    const confirmed = await this.showModal(
                        'confirm',
                        'Vider le cache ? Cela peut ralentir temporairement le site lors du prochain chargement.',
                        'Vider le cache',
                        { confirmText: 'Vider le cache' }
                    );

                    if (!confirmed) return;

                    try {
                        const response = await fetch('{{ route('admin.cache.clear') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });

                        const data = await response.json();

                        if (response.ok) {
                            this.successMessage = data.message || 'Cache vidé avec succès';
                        } else {
                            this.showModal('error', data.message || 'Erreur lors du vidage du cache');
                        }
                    } catch (error) {
                        console.error('Erreur cache:', error);
                        this.showModal('error', 'Erreur de connexion lors du vidage du cache');
                    }
                },

                async resetForm() {
                    const confirmed = await this.showModal(
                        'confirm',
                        'Êtes-vous sûr de vouloir réinitialiser tous les paramètres aux valeurs actuelles ?',
                        'Réinitialiser le formulaire',
                        { confirmText: 'Réinitialiser' }
                    );

                    if (confirmed) {
                        this.site_logo = '{{ setting('site_logo') ?? '' }}';
                        this.site_favicon = '{{ setting('site_favicon') ?? '' }}';
                        document.getElementById('settings-form').reset();
                        this.successMessage = 'Formulaire réinitialisé aux valeurs actuelles';
                    }
                },

                scrollToTop() {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            }
        }

        function ipWhitelist() {
            return {
                whitelistEnabled: {{ setting('ip_whitelist') ? 'true' : 'false' }},
                newIp: '',
                ips: @js(is_array(setting('ip_whitelist_list')) ? setting('ip_whitelist_list') : (setting('ip_whitelist_list') ? json_decode(setting('ip_whitelist_list'), true) : [])),

                async addIp() {
                    const ip = this.newIp.trim();
                    if (!ip) return;

                    if (!this.isValidIP(ip)) {
                        if (window.settingsApp) {
                            window.settingsApp.showModal('error', 'Adresse IP invalide. Format attendu: 192.168.1.1');
                        }
                        return;
                    }

                    if (!this.ips.includes(ip)) {
                        this.ips.push(ip);
                        this.newIp = '';
                    } else {
                        if (window.settingsApp) {
                            window.settingsApp.showModal('info', 'Cette IP est déjà dans la liste');
                        }
                    }
                },

                async removeIp(index) {
                    const ip = this.ips[index];
                    if (window.settingsApp) {
                        const confirmed = await window.settingsApp.showModal(
                            'confirm',
                            `Êtes-vous sûr de vouloir supprimer l'adresse IP ${ip} de la liste blanche ?`,
                            'Supprimer IP',
                            { confirmText: 'Supprimer' }
                        );

                        if (confirmed) {
                            this.ips.splice(index, 1);
                        }
                    }
                },

                isValidIP(ip) {
                    const ipRegex = /^(\d{1,3}\.){3}\d{1,3}$/;
                    if (!ipRegex.test(ip)) return false;

                    const parts = ip.split('.');
                    return parts.every(part => {
                        const num = parseInt(part, 10);
                        return num >= 0 && num <= 255;
                    });
                }
            }
        }
    </script>
@endpush
