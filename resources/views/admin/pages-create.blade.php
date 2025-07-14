@extends('admin.layouts.admin')

@section('title', 'Créer une page')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">

        <div class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0">
            <a href="{{ route('admin.pages') }}" class="border border-input bg-background hover:bg-accent hover:text-accent-foreground flex items-center gap-2 h-9 rounded-md px-3">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>

        <form action="{{ route('admin.pages.store') }}" method="POST">
            @csrf

            <div class="space-y-6">

                <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
                    <div class="flex flex-col space-y-1.5 p-6"><h2 class="text-2xl font-semibold leading-none tracking-tight">Informations générales</h2></div>
                    <div class="p-6 pt-0 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="label">Titre</label>
                                <input type="text" name="title" class="w-full h-10 px-3 rounded-md border border-border bg-background text-foreground placeholder:text-muted-foreground shadow-sm focus:outline-none focus:ring-2 focus:ring-primary transition" required oninput="generateSlug(this.value)" value="{{ old('title') }}">
                                @error('title')<p class="text-destructive text-sm mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="label">Slug (URL)</label>
                                <input type="text" name="slug" id="slug" class="w-full h-10 px-3 rounded-md border border-border bg-background text-foreground placeholder:text-muted-foreground shadow-sm focus:outline-none focus:ring-2 focus:ring-primary transition" required value="{{ old('slug') }}">
                                @error('slug')<p class="text-destructive text-sm mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="label">Template</label>
                                <select name="template" class="w-full h-10 px-3 rounded-md border border-border bg-background text-foreground placeholder:text-muted-foreground shadow-sm focus:outline-none focus:ring-2 focus:ring-primary transition">
                                    @foreach($templates as $tpl)
                                        <option value="{{ $tpl['value'] }}" @selected(old('template')==$tpl['value'])>{{ $tpl['label'] }}</option>
                                    @endforeach
                                </select>
                                @error('template')<p class="text-destructive text-sm mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="label">Statut</label>
                                <select name="status" class="w-full h-10 px-3 rounded-md border border-border bg-background text-foreground placeholder:text-muted-foreground shadow-sm focus:outline-none focus:ring-2 focus:ring-primary transition">
                                    <option value="draft" @selected(old('status')=='draft')>Brouillon</option>
                                    <option value="published" @selected(old('status')=='published')>Publié</option>
                                    <option value="archived" @selected(old('status')=='archived')>Archivé</option>
                                </select>
                                @error('status')<p class="text-destructive text-sm mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>
                        <div class="flex items-center justify-between border rounded p-4">
                            <span>Définir comme page d'accueil</span>
                            <label class="switch">
                                <input type="checkbox" name="is_home" value="1" @checked(old('is_home')) class="h-4 w-4 text-primary bg-background border-border rounded focus:ring-primary focus:ring-2 focus:ring-offset-0 transition">
                                <span class="slider"></span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
                    <div class="flex flex-col space-y-1.5 p-6"><h2 class="text-2xl font-semibold leading-none tracking-tight">SEO</h2></div>
                    <div class="p-6 pt-0 space-y-4">
                        <div>
                            <label class="label">Méta Description</label>
                            <textarea name="meta_description" class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50" rows="4" placeholder="Description pour les moteurs de recherche (max 160 caractères)" required>{{ old('meta_description') }}</textarea>
                            @error('meta_description')<p class="text-destructive text-sm mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
                    <div class="flex flex-col space-y-1.5 p-6"><h2 class="text-2xl font-semibold leading-none tracking-tight">Contenu</h2></div>
                    <div class="p-6 pt-0 space-y-4">
                        <textarea name="content" id="tinymce" class="tinymce">{{ old('content') }}</textarea>
                        @error('content')<p class="text-destructive text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('admin.pages') }}" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-11 px-8">Annuler</a>
                    <button type="submit" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-primary-foreground text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 bg-primary hover:bg-primary/90 hover-glow-purple h-11 px-8">
                        <i class="fas fa-save h-4 w-4 mr-2"></i> Créer la page
                    </button>
                </div>

            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.tiny.cloud/1/gqsyll5b5xddmg9blp1h6i27e56ntb06o1tzzb2cbkd80jfd/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>

    <script>
        tinymce.init({
            selector: 'textarea#tinymce',
            height: 400,
            menubar: false,
            plugins: 'link lists code fullscreen table',
            toolbar: 'undo redo | formatselect | bold italic underline | bullist numlist | link table | code fullscreen',
            branding: false
        });

        function generateSlug(title) {
            let s = '/' + title.toLowerCase()
                .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
                .replace(/[^a-z0-9\s-]/g, '').trim()
                .replace(/\s+/g,'-').replace(/-+/g,'-');
            document.getElementById('slug').value = s;
        }
    </script>
@endpush
