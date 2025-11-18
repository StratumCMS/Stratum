@extends('admin.layouts.admin')

@section('title', 'Modifier une page')

@section('content')
    <div class="max-w-4xl mx-auto space-y-4 sm:space-y-6">

        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.pages') }}"
                   class="inline-flex items-center justify-center gap-2 rounded-lg border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2 text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-ring">
                    <i class="fas fa-arrow-left w-4 h-4"></i>
                    <span class="hidden sm:inline">Retour aux pages</span>
                    <span class="sm:hidden">Retour</span>
                </a>
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
                        <i class="fas fa-edit text-primary text-sm"></i>
                    </div>
                    <div>
                        <h1 class="text-xl sm:text-2xl font-semibold text-foreground">Modifier la page</h1>
                        <p class="text-sm text-muted-foreground hidden sm:block">Modifiez les informations de la page "{{ $page->title }}"</p>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.pages.update', $page) }}" method="POST" x-data="pageForm()" @submit.prevent="submitForm">
            @csrf
            @method('PUT')

            <div class="space-y-4 sm:space-y-6">

                <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
                    <div class="p-4 sm:p-6 border-b border-border">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center">
                                <i class="fas fa-info-circle text-blue-500 text-sm"></i>
                            </div>
                            <h2 class="text-lg sm:text-xl font-semibold text-foreground">Informations générales</h2>
                        </div>
                    </div>
                    <div class="p-4 sm:p-6 space-y-4 sm:space-y-6">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-foreground flex items-center justify-between">
                                    <span>Titre de la page</span>
                                    <span class="text-xs text-muted-foreground font-normal">Requis</span>
                                </label>
                                <div class="relative">
                                    <input type="text"
                                           name="title"
                                           x-model="form.title"
                                           @input="generateSlug($event.target.value)"
                                           class="flex h-10 w-full rounded-lg border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 transition-colors"
                                           placeholder="Ex: À propos de nous"
                                           required
                                           value="{{ old('title', $page->title) }}">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3" x-show="form.title">
                                        <button type="button" @click="form.title = ''; form.slug = ''" class="text-muted-foreground hover:text-foreground transition-colors">
                                            <i class="fas fa-times w-4 h-4"></i>
                                        </button>
                                    </div>
                                </div>
                                @error('title')
                                <p class="text-sm text-destructive flex items-center space-x-1">
                                    <i class="fas fa-exclamation-circle w-4 h-4"></i>
                                    <span>{{ $message }}</span>
                                </p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium text-foreground flex items-center justify-between">
                                    <span>Slug (URL)</span>
                                    <span class="text-xs text-muted-foreground font-normal">Requis</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <i class="fas fa-link text-muted-foreground w-4 h-4"></i>
                                    </div>
                                    <input type="text"
                                           name="slug"
                                           id="slug"
                                           x-model="form.slug"
                                           class="flex h-10 w-full rounded-lg border border-input bg-background pl-10 pr-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 transition-colors font-mono"
                                           placeholder="ex: a-propos"
                                           required
                                           value="{{ old('slug', $page->slug) }}">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3" x-show="form.slug">
                                        <button type="button" @click="form.slug = ''" class="text-muted-foreground hover:text-foreground transition-colors">
                                            <i class="fas fa-times w-4 h-4"></i>
                                        </button>
                                    </div>
                                </div>
                                @error('slug')
                                <p class="text-sm text-destructive flex items-center space-x-1">
                                    <i class="fas fa-exclamation-circle w-4 h-4"></i>
                                    <span>{{ $message }}</span>
                                </p>
                                @enderror
                                <p class="text-xs text-muted-foreground" x-show="form.slug">
                                    URL : <span class="font-mono text-foreground">/</span><span class="font-mono text-primary" x-text="form.slug"></span>
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-foreground">Template</label>
                                <div class="relative">
                                    <select name="template"
                                            class="flex h-10 w-full rounded-lg border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 appearance-none transition-colors">
                                        @foreach($templates as $tpl)
                                            <option value="{{ $tpl['value'] }}" @selected(old('template', $page->template) == $tpl['value'])>{{ $tpl['label'] }}</option>
                                        @endforeach
                                    </select>
                                    <i class="fas fa-chevron-down absolute right-3 top-1/2 transform -translate-y-1/2 text-muted-foreground pointer-events-none"></i>
                                </div>
                                @error('template')
                                <p class="text-sm text-destructive flex items-center space-x-1">
                                    <i class="fas fa-exclamation-circle w-4 h-4"></i>
                                    <span>{{ $message }}</span>
                                </p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium text-foreground">Statut</label>
                                <div class="relative">
                                    <select name="status"
                                            class="flex h-10 w-full rounded-lg border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 appearance-none transition-colors">
                                        <option value="draft" @selected(old('status', $page->status) == 'draft')>Brouillon</option>
                                        <option value="published" @selected(old('status', $page->status) == 'published')>Publié</option>
                                        <option value="archived" @selected(old('status', $page->status) == 'archived')>Archivé</option>
                                    </select>
                                    <i class="fas fa-chevron-down absolute right-3 top-1/2 transform -translate-y-1/2 text-muted-foreground pointer-events-none"></i>
                                </div>
                                @error('status')
                                <p class="text-sm text-destructive flex items-center space-x-1">
                                    <i class="fas fa-exclamation-circle w-4 h-4"></i>
                                    <span>{{ $message }}</span>
                                </p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex items-center justify-between p-4 rounded-lg border border-border bg-muted/20 transition-colors hover:bg-muted/30">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-lg bg-blue-500/10 flex items-center justify-center">
                                    <i class="fas fa-home text-blue-500 text-sm"></i>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-foreground cursor-pointer" for="is_home">
                                        Définir comme page d'accueil
                                    </label>
                                    <p class="text-xs text-muted-foreground">
                                        Cette page sera la première page visible sur votre site
                                    </p>
                                </div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox"
                                       name="is_home"
                                       id="is_home"
                                       value="1"
                                       @checked(old('is_home', $page->is_home))
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-muted-foreground/20 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-ring rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
                    <div class="p-4 sm:p-6 border-b border-border">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-lg bg-green-500/10 flex items-center justify-center">
                                <i class="fas fa-search text-green-500 text-sm"></i>
                            </div>
                            <h2 class="text-lg sm:text-xl font-semibold text-foreground">Optimisation SEO</h2>
                        </div>
                    </div>
                    <div class="p-4 sm:p-6 space-y-4">
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-foreground flex items-center justify-between">
                                <span>Méta Description</span>
                                <span class="text-xs text-muted-foreground font-normal" x-text="`${form.metaDescription.length}/160`"></span>
                            </label>
                            <textarea name="meta_description"
                                      x-model="form.metaDescription"
                                      rows="4"
                                      class="flex min-h-[100px] w-full rounded-lg border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 resize-none transition-colors"
                                      placeholder="Décrivez le contenu de cette page pour les moteurs de recherche (recommandé : 150-160 caractères)..."
                                      maxlength="160">{{ old('meta_description', $page->meta_description) }}</textarea>
                            @error('meta_description')
                            <p class="text-sm text-destructive flex items-center space-x-1">
                                <i class="fas fa-exclamation-circle w-4 h-4"></i>
                                <span>{{ $message }}</span>
                            </p>
                            @enderror
                            <div class="flex items-center space-x-2 text-xs text-muted-foreground">
                                <i class="fas fa-info-circle w-3 h-3"></i>
                                <span>Cette description apparaîtra dans les résultats de recherche</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
                    <div class="p-4 sm:p-6 border-b border-border">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-lg bg-purple-500/10 flex items-center justify-center">
                                    <i class="fas fa-edit text-purple-500 text-sm"></i>
                                </div>
                                <h2 class="text-lg sm:text-xl font-semibold text-foreground">Contenu de la page</h2>
                            </div>
                            <div class="flex items-center space-x-2 text-xs text-muted-foreground">
                                <i class="fas fa-keyboard w-3 h-3"></i>
                                <span class="hidden sm:inline">Éditeur riche</span>
                            </div>
                        </div>
                    </div>
                    <div class="p-4 sm:p-6">
                        <div class="space-y-2">
                            <textarea name="content"
                                      id="tinymce"
                                      class="tinymce hidden">{{ old('content', $page->content) }}</textarea>
                            <div x-show="!editorLoaded" class="flex items-center justify-center py-12">
                                <div class="text-center">
                                    <div class="w-12 h-12 rounded-full bg-muted/50 flex items-center justify-center mx-auto mb-3">
                                        <i class="fas fa-spinner fa-spin text-muted-foreground text-lg"></i>
                                    </div>
                                    <p class="text-sm text-muted-foreground">Chargement de l'éditeur...</p>
                                </div>
                            </div>
                            @error('content')
                            <p class="text-sm text-destructive flex items-center space-x-1 mt-2">
                                <i class="fas fa-exclamation-circle w-4 h-4"></i>
                                <span>{{ $message }}</span>
                            </p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 p-6 rounded-xl border bg-card text-card-foreground shadow-sm">
                    <div class="flex items-center space-x-2 text-sm text-muted-foreground">
                        <i class="fas fa-info-circle w-4 h-4"></i>
                        <span>Tous les champs marqués comme "Requis" doivent être remplis</span>
                    </div>

                    <div class="flex items-center space-x-3">
                        <a href="{{ route('admin.pages') }}"
                           class="inline-flex items-center justify-center gap-2 rounded-lg border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-6 py-2 text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-ring w-full sm:w-auto">
                            <i class="fas fa-times w-4 h-4"></i>
                            Annuler
                        </a>
                        <button type="submit"
                                :disabled="isSubmitting"
                                class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-6 py-2 text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-ring disabled:opacity-50 disabled:cursor-not-allowed w-full sm:w-auto"
                                :class="{'opacity-50 cursor-not-allowed': isSubmitting}">
                            <i class="fas" :class="isSubmitting ? 'fa-spinner fa-spin' : 'fa-save'"></i>
                            <span x-text="isSubmitting ? 'Modification...' : 'Modifier la page'"></span>
                        </button>
                    </div>
                </div>

            </div>
        </form>

    </div>
@endsection

@push('scripts')
    <script>
        function pageForm() {
            return {
                form: {
                    title: '{{ old('title', $page->title) }}',
                    slug: '{{ old('slug', $page->slug) }}',
                    metaDescription: '{{ old('meta_description', $page->meta_description) }}'
                },
                editorLoaded: false,
                isSubmitting: false,

                init() {
                    this.loadTinyMCE();
                },

                generateSlug(title) {
                    if (!this.form.slug || this.form.slug === this.slugify(this.form.title)) {
                        this.form.slug = this.slugify(title);
                    }
                },

                slugify(text) {
                    return text
                        .toLowerCase()
                        .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
                        .replace(/[^a-z0-9\s-]/g, '')
                        .trim()
                        .replace(/\s+/g, '-')
                        .replace(/-+/g, '-');
                },

                async loadTinyMCE() {
                    try {
                        const components = await this.fetchComponents();

                        window.tinyLoadAndInit({
                            selector: 'textarea#tinymce',
                            height: 500,
                            menubar: false,
                            plugins: 'link lists code fullscreen table image media',
                            toolbar: 'undo redo | formatselect | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image media table | modulecomponent | code fullscreen',
                            branding: false,
                            skin: 'oxide-dark',
                            content_css: 'dark',
                            base_url: '/vendor/tinymce',
                            suffix: '.min',
                            license_key: 'gpl',
                            setup: (editor) => {
                                editor.on('init', () => {
                                    this.editorLoaded = true;
                                });

                                this.setupModuleComponent(editor, components);
                            }
                        }).catch(e => {
                            console.error('TinyMCE error:', e);
                            this.editorLoaded = true;
                        });
                    } catch (error) {
                        console.error('Error loading components:', error);
                        this.editorLoaded = true;
                    }
                },

                async fetchComponents() {
                    const response = await fetch('{{ route("admin.modules.module-components") }}');
                    if (!response.ok) throw new Error('Failed to fetch components');
                    return await response.json();
                },

                setupModuleComponent(editor, components) {
                    editor.ui.registry.addMenuButton('modulecomponent', {
                        text: 'Composants',
                        icon: 'template',
                        fetch: (callback) => {
                            const items = components.map(comp => ({
                                type: 'menuitem',
                                text: comp.name,
                                onAction: () => {
                                    editor.insertContent('&lbrace;&lbrace; ' + comp.slug + ' &rbrace;&rbrace;');
                                }
                            }));

                            if (items.length === 0) {
                                items.push({
                                    type: 'menuitem',
                                    text: 'Aucun composant disponible',
                                    enabled: false
                                });
                            }

                            callback(items);
                        }
                    });
                },

                async submitForm() {
                    this.isSubmitting = true;

                    try {
                        this.$el.submit();
                    } catch (error) {
                        console.error('Form submission error:', error);
                        this.isSubmitting = false;
                    }
                }
            }
        }
    </script>
@endpush

@push('styles')
    <style>
        @media (max-width: 640px) {
            .tinymce-mobile-outer-container {
                border-radius: 0.5rem !important;
            }
        }

        .transition-colors {
            transition: all 0.2s ease-in-out;
        }

        input:focus, textarea:focus, select:focus {
            transform: translateY(-1px);
        }
    </style>
@endpush
