@extends('admin.layouts.admin')

@section('title', 'Mise à jour du CMS')

@section('content')
    <div x-data="updater()" x-init="init(window.__changelogs)" class="max-w-4xl mx-auto space-y-4 sm:space-y-6">

        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
                    <i class="fas fa-sync-alt text-primary text-sm"></i>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-semibold text-foreground">Mise à jour du système</h1>
                    <p class="text-sm text-muted-foreground hidden sm:block">Gérez les mises à jour de StratumCMS</p>
                </div>
            </div>
            <div class="text-right">
                <div class="text-sm text-muted-foreground">Version actuelle</div>
                <div class="text-lg font-bold text-primary">{{ config('app.version') ?? 'v0.1.0' }}</div>
            </div>
        </div>

        <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
            <div class="p-4 sm:p-6 border-b border-border">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg sm:text-xl font-semibold text-foreground">Statut de mise à jour</h2>
                    <button
                        @click="checkForUpdate"
                        :disabled="checking"
                        class="inline-flex items-center justify-center gap-2 rounded-lg border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-3 text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-ring disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <i class="fas" :class="checking ? 'fa-spinner fa-spin' : 'fa-sync-alt'"></i>
                        <span class="hidden sm:inline" x-text="checking ? 'Vérification...' : 'Revérifier'"></span>
                    </button>
                </div>
            </div>

            <div class="p-4 sm:p-6 space-y-4 sm:space-y-6">
                <template x-if="checking">
                    <div class="flex items-center justify-center py-4">
                        <div class="text-center">
                            <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-spinner fa-spin text-primary text-lg"></i>
                            </div>
                            <p class="text-sm text-muted-foreground">Vérification des mises à jour en cours...</p>
                        </div>
                    </div>
                </template>

                <template x-if="!checking && !updateAvailable">
                    <div class="text-center py-6">
                        <div class="w-16 h-16 rounded-full bg-green-500/10 flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-check-circle text-green-500 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-foreground mb-2">StratumCMS est à jour</h3>
                        <p class="text-muted-foreground max-w-sm mx-auto">
                            Vous utilisez la dernière version disponible. Votre système est sécurisé et optimisé.
                        </p>
                    </div>
                </template>

                <template x-if="!checking && updateAvailable">
                    <div class="space-y-4">
                        <div class="flex items-center space-x-4 p-4 rounded-lg bg-blue-500/10 border border-blue-500/20">
                            <div class="w-12 h-12 rounded-full bg-blue-500/20 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-download text-blue-500 text-lg"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-foreground">Nouvelle version disponible !</h3>
                                <p class="text-sm text-muted-foreground">
                                    Version <span x-text="latestVersion" class="font-semibold text-foreground"></span> est disponible
                                </p>
                            </div>
                        </div>

                        <button
                            :disabled="updating"
                            @click="startUpdate"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-lg bg-primary text-primary-foreground hover:bg-primary/90 h-11 px-6 py-2 text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-ring disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <i class="fas" :class="updating ? 'fa-spinner fa-spin' : 'fa-download'"></i>
                            <span x-text="updating ? 'Mise à jour en cours...' : 'Mettre à jour maintenant'"></span>
                        </button>

                        <template x-if="progress > 0">
                            <div class="space-y-2">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-foreground font-medium">Progression</span>
                                    <span class="text-muted-foreground" x-text="progress + '%'"></span>
                                </div>
                                <div class="w-full bg-muted h-3 rounded-full overflow-hidden">
                                    <div
                                        class="h-3 bg-primary transition-all duration-500 ease-out rounded-full"
                                        :style="'width:' + progress + '%'"
                                        :aria-valuenow="progress"
                                        role="progressbar"
                                        aria-valuemin="0"
                                        aria-valuemax="100"
                                    ></div>
                                </div>
                                <div class="grid grid-cols-4 gap-2 text-xs text-muted-foreground">
                                    <template x-for="step in updateSteps" :key="step.id">
                                        <div class="text-center" :class="{'text-primary font-medium': progress >= step.threshold}">
                                            <i class="fas" :class="progress >= step.threshold ? 'fa-check-circle' : step.icon"></i>
                                            <div class="mt-1" x-text="step.label"></div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <div class="p-4 rounded-lg bg-amber-500/10 border border-amber-500/20">
                            <div class="flex items-start space-x-3">
                                <i class="fas fa-exclamation-triangle text-amber-500 mt-0.5 flex-shrink-0"></i>
                                <div class="flex-1">
                                    <h4 class="text-sm font-medium text-foreground mb-1">Recommandations</h4>
                                    <ul class="text-xs text-muted-foreground space-y-1">
                                        <li>• Sauvegardez votre base de données avant la mise à jour</li>
                                        <li>• Effectuez la mise à jour pendant une période de faible trafic</li>
                                        <li>• Ne fermez pas cette page pendant le processus</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
            <div class="p-4 sm:p-6 border-b border-border">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg sm:text-xl font-semibold text-foreground">Notes de version</h2>
                    <div class="text-xs text-muted-foreground hidden sm:block">
                        <span x-text="pagedChangelogs.length"></span> sur <span x-text="changelogs.length"></span> versions
                    </div>
                </div>
            </div>

            <div class="p-4 sm:p-6">
                <template x-if="pagedChangelogs.length">
                    <div class="space-y-4">
                        <template x-for="log in pagedChangelogs" :key="log.tag_name">
                            <div class="rounded-lg border border-border bg-background hover:border-primary/40 transition-colors group">
                                <a :href="log.html_url" target="_blank" class="block p-4 sm:p-6">
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex-1 min-w-0">
                                            <h3 class="font-semibold text-foreground group-hover:text-primary transition-colors truncate" x-text="log.name"></h3>
                                            <p class="text-sm text-muted-foreground mt-1">
                                                Version <span x-text="log.tag_name" class="font-mono"></span>
                                            </p>
                                        </div>
                                        <i class="fas fa-external-link-alt text-muted-foreground group-hover:text-primary transition-colors mt-1 flex-shrink-0"></i>
                                    </div>

                                    <div class="prose prose-sm max-w-none text-foreground"
                                         :class="{'max-h-32 overflow-hidden': !log.expanded}"
                                         x-html="log.expanded ? log.body_full : log.body_short">
                                    </div>

                                    <template x-if="!log.expanded && log.body_full !== log.body_short">
                                        <button
                                            @click.prevent="log.expanded = true"
                                            class="mt-3 inline-flex items-center gap-2 text-sm text-primary hover:text-primary/80 transition-colors"
                                        >
                                            <span>Voir plus de détails</span>
                                            <i class="fas fa-chevron-down text-xs"></i>
                                        </button>
                                    </template>

                                    <template x-if="log.expanded && log.body_full !== log.body_short">
                                        <button
                                            @click.prevent="log.expanded = false"
                                            class="mt-3 inline-flex items-center gap-2 text-sm text-primary hover:text-primary/80 transition-colors"
                                        >
                                            <span>Voir moins</span>
                                            <i class="fas fa-chevron-up text-xs"></i>
                                        </button>
                                    </template>
                                </a>
                            </div>
                        </template>
                    </div>
                </template>

                <template x-if="!pagedChangelogs.length && !checking">
                    <div class="text-center py-8">
                        <div class="w-16 h-16 rounded-full bg-muted/50 flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-file-alt text-2xl text-muted-foreground"></i>
                        </div>
                        <h3 class="text-lg font-medium text-foreground mb-2">Aucun changelog disponible</h3>
                        <p class="text-muted-foreground max-w-sm mx-auto">
                            Les notes de version n'ont pas pu être chargées ou aucune version n'est disponible.
                        </p>
                    </div>
                </template>

                <div class="flex items-center justify-between pt-6 border-t border-border mt-6" x-show="totalPages > 1">
                    <button
                        @click="currentPage = Math.max(currentPage - 1, 1)"
                        :disabled="currentPage === 1"
                        class="inline-flex items-center justify-center gap-2 rounded-lg border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-3 text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-ring disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <i class="fas fa-chevron-left w-4 h-4"></i>
                        <span class="hidden sm:inline">Précédent</span>
                    </button>

                    <div class="flex items-center space-x-2 text-sm text-muted-foreground">
                        <span>Page</span>
                        <span class="font-semibold text-foreground" x-text="currentPage"></span>
                        <span>sur</span>
                        <span class="font-semibold text-foreground" x-text="totalPages"></span>
                    </div>

                    <button
                        @click="currentPage = Math.min(currentPage + 1, totalPages)"
                        :disabled="currentPage === totalPages"
                        class="inline-flex items-center justify-center gap-2 rounded-lg border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-3 text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-ring disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span class="hidden sm:inline">Suivant</span>
                        <i class="fas fa-chevron-right w-4 h-4"></i>
                    </button>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <script>
        window.__changelogs = @json($changelogs);

        function updater() {
            return {
                checking: false,
                updateAvailable: false,
                latestVersion: '',
                versionTag: '',
                downloadUrl: '',
                changelogs: [],
                currentPage: 1,
                perPage: 3,
                progress: 0,
                updating: false,
                updateSteps: [
                    { id: 1, label: 'Préparation', icon: 'fa-cog', threshold: 25 },
                    { id: 2, label: 'Téléchargement', icon: 'fa-download', threshold: 50 },
                    { id: 3, label: 'Installation', icon: 'fa-wrench', threshold: 75 },
                    { id: 4, label: 'Finalisation', icon: 'fa-check', threshold: 100 }
                ],

                get totalPages() {
                    return Math.ceil(this.changelogs.length / this.perPage);
                },

                get pagedChangelogs() {
                    const start = (this.currentPage - 1) * this.perPage;
                    return this.changelogs.slice(start, start + this.perPage);
                },

                init(data = []) {
                    this.processChangelogs(data);
                    this.checkForUpdate();
                },

                processChangelogs(data) {
                    this.changelogs = data.map(r => {
                        const rawBody = r.body || 'Aucune note de version disponible.';
                        const htmlFull = marked.parse(rawBody);
                        const maxLength = 300;
                        const shortBody = rawBody.length > maxLength
                            ? marked.parse(rawBody.substring(0, maxLength)) + '...'
                            : htmlFull;

                        return {
                            tag_name: r.tag_name,
                            name: r.name || r.tag_name,
                            body_full: htmlFull,
                            body_short: shortBody,
                            html_url: r.html_url || `https://github.com/StratumCMS/Stratum/releases/tag/${r.tag_name}`,
                            expanded: false
                        };
                    });
                },

                async checkForUpdate() {
                    this.checking = true;
                    try {
                        const res = await fetch('{{ route('admin.update.check') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });

                        if (!res.ok) throw new Error('Network response was not ok');

                        const data = await res.json();
                        this.updateAvailable = data.update_available;
                        if (data.update_available) {
                            this.latestVersion = data.latest_version;
                            this.versionTag = data.version_tag;
                            this.downloadUrl = data.download_url;
                        }
                    } catch (error) {
                        console.error('Erreur lors de la vérification:', error);
                        this.showNotification('Erreur lors de la vérification des mises à jour', 'error');
                    }
                    this.checking = false;
                },

                async startUpdate() {
                    if (!confirm('Êtes-vous sûr de vouloir lancer la mise à jour ? Assurez-vous d\'avoir une sauvegarde récente.')) {
                        return;
                    }

                    this.updating = true;
                    this.progress = 10;

                    try {
                        this.simulateProgress();

                        const res = await fetch('{{ route('admin.update.run') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                download_url: this.downloadUrl,
                                version_tag: this.versionTag
                            })
                        });

                        const result = await res.json();

                        if (result.success) {
                            this.progress = 100;
                            this.showNotification(result.message || 'Mise à jour terminée avec succès', 'success');

                            setTimeout(() => {
                                location.reload();
                            }, 2000);
                        } else {
                            this.progress = 0;
                            this.showNotification(result.message || 'Erreur pendant la mise à jour', 'error');
                        }
                    } catch (error) {
                        console.error('Erreur mise à jour:', error);
                        this.progress = 0;
                        this.showNotification('Erreur inattendue lors de la mise à jour', 'error');
                    }

                    this.updating = false;
                },

                simulateProgress() {
                    const interval = setInterval(() => {
                        if (this.progress < 90) {
                            this.progress += Math.random() * 15;
                        } else {
                            clearInterval(interval);
                        }
                    }, 800);
                },

                showNotification(message, type = 'info') {
                    const toast = document.createElement('div');
                    const typeClasses = {
                        success: 'bg-green-500/10 border-green-500/20 text-green-600',
                        error: 'bg-red-500/10 border-red-500/20 text-red-600',
                        info: 'bg-blue-500/10 border-blue-500/20 text-blue-600'
                    };

                    toast.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg border transition-all duration-300 transform translate-x-full ${typeClasses[type]}`;
                    toast.innerHTML = `
                        <div class="flex items-center space-x-2">
                            <i class="fas ${
                        type === 'success' ? 'fa-check-circle' :
                            type === 'error' ? 'fa-exclamation-circle' :
                                'fa-info-circle'
                    }"></i>
                            <span class="text-sm font-medium">${message}</span>
                        </div>
                    `;

                    document.body.appendChild(toast);

                    setTimeout(() => toast.classList.remove('translate-x-full'), 100);

                    setTimeout(() => {
                        toast.classList.add('translate-x-full');
                        setTimeout(() => {
                            if (toast.parentNode) {
                                toast.parentNode.removeChild(toast);
                            }
                        }, 300);
                    }, 5000);
                }
            }
        }
    </script>
@endpush

@push('styles')
    <style>
        .prose {
            color: inherit;
        }

        .prose h1, .prose h2, .prose h3, .prose h4 {
            color: inherit;
            margin-top: 1em;
            margin-bottom: 0.5em;
        }

        .prose ul, .prose ol {
            padding-left: 1.5em;
            margin: 0.5em 0;
        }

        .prose li {
            margin: 0.25em 0;
        }

        .prose code {
            background: rgba(148, 163, 184, 0.1);
            padding: 0.125rem 0.25rem;
            border-radius: 0.25rem;
            font-size: 0.875em;
        }

        .prose pre {
            background: rgba(148, 163, 184, 0.1);
            padding: 1rem;
            border-radius: 0.5rem;
            overflow-x: auto;
            margin: 1em 0;
        }

        .prose a {
            color: rgb(59, 130, 246);
            text-decoration: underline;
        }

        .transition-all {
            transition-property: all;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 500ms;
        }
    </style>
@endpush
