@extends('admin.layouts.admin')

@section('title', 'Créer un article')

@section('content')
    <div x-data="createArticle()" class="max-w-4xl mx-auto space-y-6">


        <a href="{{ route('admin.articles') }}" class="btn btn-ghost hover-glow-purple">
            <i class="fas fa-arrow-left mr-2"></i> Retour aux articles
        </a>


        <form action="{{ route('admin.articles.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <div class="lg:col-span-2 space-y-6">
                    <div>
                        <label class="form-label">Titre</label>
                        <input type="text" name="title" class="form-input" value="{{ old('title') }}" required>
                        @error('title') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label">Extrait</label>
                        <textarea name="description" rows="3" class="form-textarea" required>{{ old('description') }}</textarea>
                        @error('description') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label">Contenu</label>
                        <textarea name="content" id="content-editor" class="form-textarea">{{ old('content') }}</textarea>
                        @error('content') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                    </div>
                </div>


                <div class="space-y-6">

                    <div class="card">
                        <div class="card-header"><span class="card-title">Image de couverture</span></div>
                        <div class="card-content">
                            <template x-if="thumbnailPreviewUrl">
                                <div class="relative">
                                    <img :src="thumbnailPreviewUrl" class="w-full h-32 object-cover rounded-lg">
                                    <button type="button" @click="clearThumbnail()" class="btn btn-destructive btn-sm absolute top-2 right-2">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </template>

                            <button type="button" @click="openModal()" class="btn btn-outline w-full mt-2">
                                <i class="fas fa-image mr-2"></i> Choisir / Uploader une image
                            </button>

                            <input type="hidden" name="thumbnail_media_id" x-model="selectedMediaId">
                        </div>
                    </div>


                    <div class="card">
                        <div class="card-header"><span class="card-title">Paramètres</span></div>
                        <div class="card-content space-y-4">
                            <div>
                                <label class="form-label">Statut</label>
                                <select name="is_published" class="form-select">
                                    <option value="0" {{ old('is_published') == '0' ? 'selected' : '' }}>Brouillon</option>
                                    <option value="1" {{ old('is_published') == '1' ? 'selected' : '' }}>Publié</option>
                                </select>
                            </div>

                            <div>
                                <label class="form-label">Date de publication</label>
                                <input type="datetime-local" name="published_at" class="form-input" value="{{ old('published_at') }}">
                            </div>

                            <div>
                                <label class="form-label">Catégorie</label>
                                <input name="type" value="{{ old('type') }}" class="form-input" placeholder="Ex: Développement">
                            </div>

                            <div>
                                <label class="form-label">Tags</label>
                                <input name="tags" value="{{ old('tags') }}" class="form-input" placeholder="Laravel, SEO, UI">
                            </div>
                        </div>
                    </div>


                    <div class="space-y-3">
                        <button type="submit" class="btn btn-primary w-full">
                            <i class="fas fa-save mr-2"></i> Créer l'article
                        </button>
                        <a href="{{ route('admin.articles') }}" class="btn btn-outline w-full">Annuler</a>
                    </div>
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

                <a href="{{ route('admin.media') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-md text-sm font-medium bg-primary text-white hover:bg-primary/90 transition shadow">Upload un média</a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.tiny.cloud/1/gqsyll5b5xddmg9blp1h6i27e56ntb06o1tzzb2cbkd80jfd/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
    <script src="https://unpkg.com/alpinejs" defer></script>
    <script>
        function createArticle() {
            return {
                modalOpen: false,
                mediaItems: @json($mediaItems),
                selectedMediaId: null,
                thumbnailPreviewUrl: null,

                openModal() { this.modalOpen = true },
                closeModal() { this.modalOpen = false },

                selectMedia(media) {
                    this.selectedMediaId = media.id;
                    this.thumbnailPreviewUrl = media.url;
                    this.closeModal();
                },

                clearThumbnail() {
                    this.selectedMediaId = null;
                    this.thumbnailPreviewUrl = null;
                },

                async uploadMedia() {
                    const file = this.$refs.file.files[0];
                    if (!file) return alert('Aucun fichier sélectionné');

                    let form = new FormData();
                    form.append('media', file);
                    form.append('name', file.name);

                    let res = await fetch('{{ route("admin.media.upload") }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: form
                    });

                    let data = await res.json();
                    this.mediaItems.unshift(data.media);
                    this.selectMedia(data.media);
                }
            }
        }

        tinymce.init({
            selector: '#content-editor',
            plugins: 'link image media lists table',
            toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright | bullist numlist | link image media table',
            height: 400
        });
    </script>
@endpush
