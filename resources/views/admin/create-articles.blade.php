@extends('admin.layouts.admin')

@section('title', 'Créer un article')

@section('content')
    <div x-data="createArticle()" class="max-w-4xl mx-auto space-y-6">

        <div class="flex items-center gap-4">
            <a href="{{ route('admin.articles') }}" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 hover:bg-accent hover:text-accent-foreground h-9 px-3 hover-glow-purple">
                <i class="fas fa-arrow-left mr-2"></i> Retour aux articles
            </a>
        </div>


        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
            <div class="flex flex-col space-y-1.5 p-6">
                <div class="text-2xl font-semibold leading-none tracking-tight">
                    Nouvel article
                </div>
            </div>

            <div class="p-6 pt-0">
                <form action="{{ route('admin.articles.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                        <div class="lg:col-span-2 space-y-6">
                            <div class="space-y-2">
                                <label class="form-label">Titre</label>
                                <input type="text" name="title" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm" placeholder="Titre de l'article" value="{{ old('title') }}" required>
                                @error('title') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="form-label">Extrait</label>
                                <textarea name="description" rows="3" class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50" placeholder="Bref résumé de l'article" required>{{ old('description') }}</textarea>
                                @error('description') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="form-label">Contenu</label>
                                <textarea name="content" id="content-editor" placeholder="Contenu complet de l'article" class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 min-h-[400px] resize-none">{{ old('content') }}</textarea>
                                @error('content') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                            </div>
                        </div>


                        <div class="space-y-6">
                            <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
                                <div class="flex flex-col space-y-1.5 p-6">
                                    <h3 class="font-semibold leading-none tracking-tight text-lg">Image de couverture</h3>
                                </div>
                                <div class="p-6 pt-0 space-y-4">
                                    <template x-if="thumbnailPreviewUrl">
                                        <div class="relative">
                                            <img :src="thumbnailPreviewUrl" class="w-full h-32 object-cover rounded-lg">
                                            <button type="button" @click="clearThumbnail()" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 bg-destructive text-destructive-foreground hover:bg-destructive/90 h-9 px-3 absolute top-2 right-2">
                                                <i class="fas fa-x"></i>
                                            </button>
                                        </div>
                                    </template>

                                    <div class="border-2 border-dashed border-border rounded-lg p-6 text-center">
                                        <i class="fa-regular fa-image h-12 w-12 mx-auto mb-4 text-muted-foreground"></i>
                                        <p class="text-sm text-muted-foreground mb-2">
                                            Cliquez pour ajouter une image
                                        </p>
                                        <input type="hidden" name="thumbnail_media_id" x-model="selectedMediaId">
                                        <button type="button" @click="openModal()" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-3 w-full mt-2">
                                            <i class="fa-solid fa-upload h-4 w-4"></i> Choisir une image
                                        </button>
                                    </div>
                                </div>
                            </div>


                            <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
                                <div class="flex flex-col space-y-1.5 p-6">
                                    <h3 class="text-lg font-semibold leading-none tracking-tight">Paramètres</h3>
                                </div>
                                <div class="p-6 pt-0 space-y-4">

                                    <div class="space-y-2">
                                        <label class="form-label">Statut</label>
                                        <select name="is_published" class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 [&>span]:line-clamp-1">
                                            <option class="relative flex w-full cursor-default select-none items-center rounded-sm py-1.5 pl-8 pr-2 text-sm outline-none focus:bg-accent focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50" value="0" {{ old('is_published') == '0' ? 'selected' : '' }}>Brouillon</option>
                                            <option class="relative flex w-full cursor-default select-none items-center rounded-sm py-1.5 pl-8 pr-2 text-sm outline-none focus:bg-accent focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50" value="1" {{ old('is_published') == '1' ? 'selected' : '' }}>Publié</option>
                                        </select>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="form-label">Date de publication</label>
                                        <input type="datetime-local" name="published_at" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm" value="{{ old('published_at') }}">
                                    </div>

                                    <div>
                                        <label class="form-label">Catégorie</label>
                                        <input name="type" value="{{ old('type') }}" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm" placeholder="Ex: Développement">
                                    </div>

                                    <div>
                                        <label class="form-label">Tags</label>
                                        <input name="tags" value="{{ old('tags') }}" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm" placeholder="Laravel, SEO, UI">
                                    </div>
                                </div>
                            </div>


                            <div class="space-y-3">
                                <button type="submit" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 text-primary-foreground h-9 px-3 w-full bg-primary hover:bg-primary/90 hover-glow-purple">
                                    <i class="fas fa-save h-4 w-4 mr-2"></i> Créer l'article
                                </button>
                                <a href="{{ route('admin.articles') }}" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-3 w-full">Annuler</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>


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
