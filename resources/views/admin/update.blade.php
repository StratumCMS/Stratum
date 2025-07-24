@extends('admin.layouts.admin')

@section('title', 'Mise à jour du CMS')

@section('content')
    <div x-data="updater()" x-init="init(window.__changelogs)" class="max-w-3xl mx-auto space-y-6">

        <!-- Section Mise à jour -->
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm p-6 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold">Mise à jour de StratumCMS</h2>
                <span class="text-muted-foreground text-sm">Version actuelle : <strong>{{ config('app.version') ?? 'v0.1.0' }}</strong></span>
            </div>

            <div class="flex justify-end">
                <button
                    @click="checkForUpdate"
                    :disabled="checking"
                    class="bg-muted hover:brightness-110 hover:ring-2 hover:ring-primary text-xs px-3 py-1 rounded text-muted-foreground border"
                >
                    <i class="fas fa-sync-alt mr-1"></i> Revérifier
                </button>
            </div>

            <template x-if="checking">
                <p class="text-sm text-muted-foreground flex items-center">
                    <i class="fas fa-circle-notch fa-spin mr-2"></i> Vérification des mises à jour...
                </p>
            </template>

            <template x-if="!checking && !updateAvailable">
                <div class="text-green-600 text-sm flex items-center">
                    <i class="fas fa-check-circle mr-2"></i> StratumCMS est à jour.
                </div>
            </template>

            <template x-if="updateAvailable">
                <button
                    :disabled="updating"
                    @click="startUpdate"
                    class="bg-primary text-white text-sm font-medium px-4 py-2 rounded hover:bg-primary/90 disabled:opacity-50"
                >
                    <template x-if="!updating">
                        <span><i class="fas fa-download mr-2"></i>Mettre à jour maintenant</span>
                    </template>
                    <template x-if="updating">
                        <span><i class="fas fa-spinner fa-spin mr-2"></i>Mise à jour en cours...</span>
                    </template>
                </button>
            </template>

            <template x-if="progress > 0">
                <div>
                    <div class="w-full bg-muted h-4 rounded overflow-hidden">
                        <div class="h-4 bg-primary transition-all" :style="'width:' + progress + '%'" x-bind:aria-valuenow="progress"></div>
                    </div>
                    <p class="text-xs mt-1 text-muted-foreground">Progression : <span x-text="progress + '%' "></span></p>
                </div>
            </template>
        </div>

        <!-- Section changelogs -->
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm p-6 space-y-4">
            <h3 class="text-base font-semibold mb-2">Changelog (versions disponibles)</h3>

            <template x-if="pagedChangelogs.length">
                <div class="text-sm space-y-4">
                    <template x-for="log in pagedChangelogs" :key="log.tag_name">
                        <a :href="log.html_url" target="_blank" class="block bg-muted p-3 rounded border hover:ring hover:ring-primary/40">
                            <p class="font-semibold text-primary" x-text="log.name"></p>
                            <p class="text-xs text-muted-foreground mb-1">Version <span x-text="log.tag_name"></span></p>

                            <div class="text-muted-foreground text-sm mt-1 prose prose-sm max-w-none" x-html="log.expanded ? log.body_full : log.body_short"></div>

                            <template x-if="!log.expanded && log.body_full !== log.body_short">
                                <button class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-3 mt-1" @click.prevent="log.expanded = true">Voir plus</button>
                            </template>
                        </a>
                    </template>
                </div>
            </template>

            <template x-if="!pagedChangelogs.length">
                <div class="bg-muted p-3 rounded border text-sm text-muted-foreground">
                    Aucun changelog disponible pour le moment.
                </div>
            </template>

            <div class="flex justify-between pt-4" x-show="totalPages > 1">
                <button @click="currentPage = Math.max(currentPage - 1, 1)" :disabled="currentPage === 1" class="text-xs px-2 py-1 border rounded disabled:opacity-50">Précédent</button>
                <span class="text-xs text-muted-foreground">Page <span x-text="currentPage"></span> sur <span x-text="totalPages"></span></span>
                <button @click="currentPage = Math.min(currentPage + 1, totalPages)" :disabled="currentPage === totalPages" class="text-xs px-2 py-1 border rounded disabled:opacity-50">Suivant</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/alpinejs" defer></script>
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

                get totalPages() {
                    return Math.ceil(this.changelogs.length / this.perPage);
                },

                get pagedChangelogs() {
                    const start = (this.currentPage - 1) * this.perPage;
                    return this.changelogs.slice(start, start + this.perPage);
                },

                init(data = []) {
                    this.changelogs = data.map(r => {
                        const rawBody = r.body || 'Pas de changelog.';
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
                            html_url: r.html_url || `https://github.com/YuketsuSh/Stratum/releases/tag/${r.tag_name}`,
                            expanded: false
                        };
                    });
                    this.checkForUpdate();
                },

                async checkForUpdate() {
                    this.checking = true;
                    try {
                        const res = await fetch('{{ route('admin.update.check') }}', {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                        });
                        const data = await res.json();
                        this.updateAvailable = data.update_available;
                        if (data.update_available) {
                            this.latestVersion = data.latest_version;
                            this.versionTag = data.version_tag;
                            this.downloadUrl = data.download_url;
                        }
                    } catch (e) {
                        console.error('Erreur vérification maj:', e);
                    }
                    this.checking = false;
                },

                async startUpdate() {
                    this.updating = true;
                    this.progress = 10;
                    try {
                        const res = await fetch('{{ route('admin.update.run') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                download_url: this.downloadUrl,
                                version_tag: this.versionTag
                            })
                        });

                        const result = await res.json();
                        if (result.success) {
                            this.progress = 100;
                            alert(result.message);
                            location.reload();
                        } else {
                            alert(result.message || 'Erreur pendant la mise à jour.');
                            this.progress = 0;
                        }
                    } catch (e) {
                        alert('Erreur inattendue.');
                        console.error(e);
                    }
                    this.updating = false;
                }
            }
        }
    </script>
@endpush
