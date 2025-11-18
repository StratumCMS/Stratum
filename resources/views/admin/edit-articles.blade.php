@extends('admin.layouts.admin')

@section('title', 'Modifier un article')

@section('content')
    <div x-data="articleForm(@json($mediaItems), '{{ $article->thumbnail() }}')" x-init="init()" class="max-w-7xl mx-auto space-y-4 sm:space-y-6">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.articles') }}"
                   class="inline-flex items-center justify-center gap-2 rounded-lg border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2 text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-ring">
                    <i class="fas fa-arrow-left w-4 h-4"></i>
                    <span class="hidden sm:inline">Retour aux articles</span>
                    <span class="sm:hidden">Retour</span>
                </a>
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
                        <i class="fas fa-edit text-primary text-sm"></i>
                    </div>
                    <div>
                        <h1 class="text-xl sm:text-2xl font-semibold text-foreground">Modifier l'article</h1>
                        <p class="text-sm text-muted-foreground hidden sm:block">
                            Modifiez les informations de votre article
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex items-center space-x-2">
                <template x-if="form.isPublished">
                    <span class="inline-flex items-center rounded-full bg-green-500/10 text-green-600 px-3 py-1 text-sm font-medium">
                        <i class="fas fa-check-circle mr-1.5 w-4 h-4"></i>
                        Publié
                    </span>
                </template>
                <template x-if="!form.isPublished">
                    <span class="inline-flex items-center rounded-full bg-amber-500/10 text-amber-600 px-3 py-1 text-sm font-medium">
                        <i class="fas fa-edit mr-1.5 w-4 h-4"></i>
                        Brouillon
                    </span>
                </template>
            </div>
        </div>

        <form action="{{ route('admin.articles.update', $article) }}" method="POST" enctype="multipart/form-data" class="space-y-4 sm:space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 sm:gap-6">
                <div class="xl:col-span-2 space-y-4 sm:space-y-6">
                    <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
                        <div class="p-4 sm:p-6 border-b border-border">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center">
                                    <i class="fas fa-info-circle text-blue-500 text-sm"></i>
                                </div>
                                <h2 class="text-lg sm:text-xl font-semibold text-foreground">Informations de l'article</h2>
                            </div>
                        </div>
                        <div class="p-4 sm:p-6 space-y-4 sm:space-y-6">
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-foreground flex items-center justify-between">
                                    <span>Titre de l'article</span>
                                    <span class="text-xs text-muted-foreground font-normal">Requis</span>
                                </label>
                                <input type="text"
                                       name="title"
                                       x-model="form.title"
                                       class="flex h-10 w-full rounded-lg border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 transition-colors"
                                       placeholder="Ex: Les nouvelles tendances du développement web"
                                       required>
                                @error('title')
                                <p class="text-sm text-destructive flex items-center space-x-1">
                                    <i class="fas fa-exclamation-circle w-4 h-4"></i>
                                    <span>{{ $message }}</span>
                                </p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium text-foreground flex items-center justify-between">
                                    <span>Extrait / Description</span>
                                    <span class="text-xs text-muted-foreground font-normal">Requis</span>
                                </label>
                                <textarea name="description"
                                          rows="3"
                                          x-model="form.description"
                                          class="flex min-h-[100px] w-full rounded-lg border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 resize-none transition-colors"
                                          placeholder="Brève description qui apparaîtra dans les listes d'articles..."
                                          required>{{ old('description', $article->description) }}</textarea>
                                @error('description')
                                <p class="text-sm text-destructive flex items-center space-x-1">
                                    <i class="fas fa-exclamation-circle w-4 h-4"></i>
                                    <span>{{ $message }}</span>
                                </p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium text-foreground">Contenu de l'article</label>
                                <textarea name="content"
                                          id="content-editor"
                                          class="tinymce hidden">{{ old('content', $article->content) }}</textarea>
                                @error('content')
                                <p class="text-sm text-destructive flex items-center space-x-1">
                                    <i class="fas fa-exclamation-circle w-4 h-4"></i>
                                    <span>{{ $message }}</span>
                                </p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-4 sm:space-y-6">
                    <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
                        <div class="p-4 sm:p-6 border-b border-border">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-lg bg-purple-500/10 flex items-center justify-center">
                                    <i class="fas fa-image text-purple-500 text-sm"></i>
                                </div>
                                <h2 class="text-lg font-semibold text-foreground">Image de couverture</h2>
                            </div>
                        </div>
                        <div class="p-4 sm:p-6 space-y-4">
                            <template x-if="thumbnailPreviewUrl">
                                <div class="relative group">
                                    <img :src="thumbnailPreviewUrl"
                                         class="w-full h-32 sm:h-40 object-cover rounded-lg shadow-sm">
                                    <button type="button"
                                            @click="clearThumbnail()"
                                            class="absolute top-2 right-2 inline-flex items-center justify-center rounded-full bg-destructive text-destructive-foreground hover:bg-destructive/90 w-8 h-8 transition-colors opacity-0 group-hover:opacity-100 focus:opacity-100">
                                        <i class="fas fa-times w-4 h-4"></i>
                                    </button>
                                </div>
                            </template>

                            <div class="border-2 border-dashed border-border rounded-lg p-4 sm:p-6 text-center transition-colors hover:border-primary/50">
                                <i class="fas fa-image text-3xl text-muted-foreground mb-3"></i>
                                <p class="text-sm text-muted-foreground mb-2">
                                    Glissez une image ou cliquez pour choisir
                                </p>
                                <p class="text-xs text-muted-foreground mb-4">
                                    PNG, JPG, WEBP jusqu'à 5MB
                                </p>
                                <input type="hidden" name="thumbnail_media_id" x-model="selectedMediaId">
                                <button type="button"
                                        @click="openMediaModal()"
                                        class="inline-flex items-center justify-center gap-2 rounded-lg border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2 text-sm font-medium transition-colors w-full">
                                    <i class="fas fa-upload w-4 h-4"></i>
                                    Choisir une image
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
                        <div class="p-4 sm:p-6 border-b border-border">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-lg bg-green-500/10 flex items-center justify-center">
                                    <i class="fas fa-cog text-green-500 text-sm"></i>
                                </div>
                                <h2 class="text-lg font-semibold text-foreground">Paramètres</h2>
                            </div>
                        </div>
                        <div class="p-4 sm:p-6 space-y-4">
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-foreground">Statut</label>
                                <select name="is_published"
                                        x-model="form.isPublished"
                                        class="flex h-10 w-full rounded-lg border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 appearance-none transition-colors">
                                    <option value="0">Brouillon</option>
                                    <option value="1">Publié</option>
                                </select>
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium text-foreground">Date de publication</label>
                                <input type="datetime-local"
                                       name="published_at"
                                       x-model="form.publishedAt"
                                       class="flex h-10 w-full rounded-lg border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 transition-colors">
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium text-foreground">Catégorie</label>
                                <input type="text"
                                       name="type"
                                       x-model="form.type"
                                       class="flex h-10 w-full rounded-lg border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 transition-colors"
                                       placeholder="Ex: Développement">
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium text-foreground">Tags</label>
                                <input type="text"
                                       name="tags"
                                       x-model="form.tags"
                                       class="flex h-10 w-full rounded-lg border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 transition-colors"
                                       placeholder="Laravel, SEO, UI (séparés par des virgules)">
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
                        <div class="p-4 sm:p-6 space-y-3">
                            <button type="submit"
                                    :disabled="isSubmitting"
                                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2 text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-ring disabled:opacity-50 disabled:cursor-not-allowed w-full"
                                    :class="{'opacity-50 cursor-not-allowed': isSubmitting}">
                                <i class="fas" :class="isSubmitting ? 'fa-spinner fa-spin' : 'fa-save'"></i>
                                <span x-text="isSubmitting ? 'Mise à jour...' : 'Mettre à jour'"></span>
                            </button>
                            <a href="{{ route('admin.articles') }}"
                               class="inline-flex items-center justify-center gap-2 rounded-lg border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2 text-sm font-medium transition-colors w-full">
                                <i class="fas fa-times w-4 h-4"></i>
                                Annuler
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div x-show="mediaModalOpen"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
            <div x-show="mediaModalOpen"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="bg-card border border-border rounded-xl shadow-2xl max-w-4xl w-full max-h-[80vh] flex flex-col">

                <div class="p-6 border-b border-border flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-foreground">Bibliothèque de médias</h2>
                    <button @click="closeMediaModal()"
                            class="inline-flex items-center justify-center rounded-lg p-2 text-muted-foreground hover:text-foreground hover:bg-accent transition-colors">
                        <i class="fas fa-times w-5 h-5"></i>
                    </button>
                </div>

                <div class="flex-1 overflow-hidden">
                    <div class="p-6 overflow-y-auto max-h-[60vh]">
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                            <template x-for="media in mediaItems" :key="media.id">
                                <div @click="selectMedia(media)"
                                     class="cursor-pointer border-2 rounded-lg p-2 transition-all duration-200 hover:border-primary hover:shadow-md group"
                                     :class="selectedMediaId === media.id ? 'border-primary bg-primary/5' : 'border-border'">
                                    <img :src="media.url"
                                         class="w-full h-20 object-cover rounded mb-2">
                                    <p class="text-xs text-muted-foreground truncate text-center group-hover:text-foreground"
                                       x-text="media.name"></p>
                                </div>
                            </template>
                        </div>

                        <div x-show="mediaItems.length === 0" class="text-center py-12">
                            <i class="fas fa-image text-4xl text-muted-foreground mb-4"></i>
                            <p class="text-muted-foreground">Aucun média disponible</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 border-t border-border">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-muted-foreground" x-text="`${mediaItems.length} média(s)`"></span>
                        <button @click="closeMediaModal()"
                                class="inline-flex items-center justify-center gap-2 rounded-lg border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2 text-sm font-medium transition-colors">
                            Fermer
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            window.tinyLoadAndInit({
                selector: '#content-editor',
                height: 400,
                menubar: false,
                plugins: 'link lists code fullscreen table',
                toolbar: 'undo redo | formatselect | bold italic underline | bullist numlist | link table | code fullscreen',
                branding: false,
                skin: 'oxide-dark',
                content_css: 'dark',
                base_url: '/vendor/tinymce',
                suffix: '.min',
                license_key: 'gpl'
            }).catch(e => console.error(e));
        });

        function articleForm(initialMediaItems, initialThumbnail = null) {
            return {
                form: {
                    title: '{{ old('title', $article->title) }}',
                    description: '{{ old('description', $article->description) }}',
                    type: '{{ old('type', $article->type) }}',
                    tags: '{{ old('tags', implode(',', $article->tags ?? [])) }}',
                    publishedAt: '{{ old('published_at', $article->published_at) }}',
                    isPublished: '{{ old('is_published', $article->is_published) }}'
                },
                mediaItems: initialMediaItems,
                mediaModalOpen: false,
                selectedMediaId: {{ $article->thumbnail_media_id ?? 'null' }},
                thumbnailPreviewUrl: initialThumbnail,
                editorLoaded: false,
                isSubmitting: false,

                openMediaModal() {
                    this.mediaModalOpen = true;
                },

                closeMediaModal() {
                    this.mediaModalOpen = false;
                },

                selectMedia(media) {
                    this.selectedMediaId = media.id;
                    this.thumbnailPreviewUrl = media.url;
                    this.closeMediaModal();
                },

                clearThumbnail() {
                    this.selectedMediaId = null;
                    this.thumbnailPreviewUrl = null;
                },

                async uploadMedia(file) {
                    if (!file) return;

                    const formData = new FormData();
                    formData.append('media', file);
                    formData.append('name', file.name);

                    try {
                        const response = await fetch('{{ route("admin.media.upload") }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: formData
                        });

                        const data = await response.json();
                        if (data.media) {
                            this.mediaItems.unshift(data.media);
                            this.selectMedia(data.media);
                        }
                    } catch (error) {
                        console.error('Upload error:', error);
                        this.showNotification('Erreur lors du téléchargement', 'error');
                    }
                },

                showNotification(message, type = 'info') {
                    const toast = document.createElement('div');
                    toast.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg border transition-all duration-300 transform translate-x-full ${
                        type === 'success' ? 'bg-green-500/10 border-green-500/20 text-green-600' :
                            type === 'error' ? 'bg-red-500/10 border-red-500/20 text-red-600' :
                                'bg-blue-500/10 border-blue-500/20 text-blue-600'
                    }`;
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
                    }, 3000);
                }
            }
        }
    </script>
@endpush
