@extends('admin.layouts.admin')

@section('title', 'Créer un élément de navigation')

@section('content')
    <div x-data="navbarForm()" x-init="init()" class="max-w-2xl mx-auto">

        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
            <div class="flex flex-col space-y-1.5 p-6">
                <h3 class="text-2xl font-semibold leading-none tracking-tight">Nouvel élément de navigation</h3>
            </div>

            <div class="p-6 pt-0">
                <form method="POST" action="{{ route('navbar.store') }}" class="space-y-6">
                    @csrf

                    <div class="space-y-2">
                        <label for="name" class="text-sm font-medium text-muted-foreground">Nom de l'élément</label>
                        <input type="text" name="name" id="name" class="form-input" value="{{ old('name') }}" required placeholder="Ex: Accueil, Articles, Contact..." />
                        @error('name') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="type" class="text-sm font-medium text-muted-foreground">Type d'élément</label>
                        <select name="type" id="type" x-model="type" class="form-select" required>
                            <option disabled selected value="">Sélectionnez un type</option>
                            @foreach(['home', 'module', 'external_link', 'page', 'post', 'posts_list', 'dropdown'] as $t)
                                <option value="{{ $t }}" @selected(old('type') === $t)>
                                    {{ ucfirst(str_replace('_', ' ', $t)) }}
                                </option>
                            @endforeach
                        </select>
                        <template x-if="type">
                            <p class="text-sm text-muted-foreground mt-1" x-text="typeHint(type)"></p>
                        </template>
                        @error('type') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="icon" class="text-sm font-medium text-muted-foreground">Icône</label>
                        <input name="icon" id="icon" type="text" class="form-input bg-muted" placeholder="Sélectionner un favicon"/>
                        @error('icon') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="value" class="text-sm font-medium text-muted-foreground">URL / Cible</label>

                        <template x-if="type === 'home'">
                            <input type="text" disabled class="form-input bg-muted cursor-not-allowed" value="/" />
                        </template>

                        <template x-if="type === 'external_link'">
                            <input type="url" name="value" class="form-input" placeholder="https://..." value="{{ old('value') }}" />
                        </template>

                        <template x-if="type === 'module'">
                            <select name="value" class="form-select">
                                @foreach($modules as $module)
                                    <option value="{{ $module['name'] }}" @selected(old('value') === $module['name'])>
                                        {{ $module['name'] }} ({{ $module['uri'] }})
                                    </option>
                                @endforeach
                            </select>
                        </template>

                        <template x-if="type === 'page'">
                            <select name="value" class="form-select">
                                @foreach($pages as $slug => $title)
                                    <option value="{{ $slug }}" @selected(old('value') === $slug)>{{ $title }}</option>
                                @endforeach
                            </select>
                        </template>

                        <template x-if="type === 'post'">
                            <select name="value" class="form-select">
                                @foreach($articles as $id => $title)
                                    <option value="{{ $id }}" @selected(old('value') == $id)>{{ $title }}</option>
                                @endforeach
                            </select>
                        </template>

                        <template x-if="type === 'posts_list'">
                            <input type="text" disabled class="form-input bg-muted cursor-not-allowed" value="/articles" />
                        </template>

                        <template x-if="type === 'dropdown'">
                            <input type="text" disabled class="form-input bg-muted cursor-not-allowed" value="Conteneur pour sous-menus" />
                        </template>

                        @error('value') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                    </div>

                    <template x-if="type && type !== 'dropdown'">
                        <div class="space-y-2">
                            <label for="parent_id" class="text-sm font-medium text-muted-foreground">Menu parent</label>
                            <select name="parent_id" id="parent_id" class="form-select">
                                <option value="">Aucun</option>
                                @foreach($dropdowns as $dropdown)
                                    <option value="{{ $dropdown->id }}" @selected(old('parent_id') == $dropdown->id)>
                                        {{ $dropdown->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('parent_id') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                        </div>
                    </template>

                    <div class="flex justify-end space-x-4">
                        <a href="{{ route('navbar.index') }}" class="inline-flex items-center rounded-md border border-input bg-background px-4 py-2 text-sm font-medium hover:bg-accent hover:text-accent-foreground">
                            Annuler
                        </a>
                        <button type="submit" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2">
                            Créer l'élément
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/alpinejs" defer></script>
    <script>
        function navbarForm() {
            return {
                type: '{{ old('type') ?? 'home' }}',
                init() {},
                typeHint(t) {
                    const map = {
                        home: 'Redirige vers la page d’accueil (/)',
                        module: 'Génère une route vers un module Laravel',
                        external_link: 'Lien complet vers un site externe',
                        page: 'Slug d’une page CMS',
                        post: 'ID d’un article',
                        posts_list: 'Affiche la liste des articles (/articles)',
                        dropdown: 'Conteneur sans lien direct pour sous-éléments'
                    };
                    return map[t] ?? '';
                }
            }
        }
    </script>
@endpush
