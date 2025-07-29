@extends('admin.layouts.admin')

@section('title', 'Créer une page (Builder)')

<script>
    window.availableBlocks = @json($availableBlocks);
</script>

@section('content')
    <div class="flex flex-col h-screen overflow-hidden"
         x-data="pageBuilder()"
         x-init="initBuilder()"
         @keydown.window.g="toggleGrid"
         @keydown.window.ctrl="toggleShortcuts">

        <div class="flex flex-col md:flex-row justify-between items-center gap-4 p-4 border-b bg-background">
            <a href="{{ route('admin.pages') }}" class="border border-input bg-background hover:bg-accent hover:text-accent-foreground flex items-center gap-2 h-9 rounded-md px-3">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
            <div class="flex items-center gap-2">
                <button @click="toggleShortcuts" class="border border-input bg-background hover:bg-accent hover:text-accent-foreground flex items-center gap-2 h-9 rounded-md px-3">
                    <i class="fas fa-keyboard"></i> Raccourcis
                </button>
                <a href="{{ route('admin.pages.create.advanced') }}" class="border border-input bg-background hover:bg-accent hover:text-accent-foreground flex items-center gap-2 h-9 rounded-md px-3">
                    <i class="fas fa-object-group"></i> Mode Advanced
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.pages.store') }}" class="bg-card border-b p-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="label">Titre</label>
                    <input type="text" name="title" x-model="title" @input="generateSlug" class="w-full h-10 px-3 rounded-md border border-border bg-background text-foreground placeholder:text-muted-foreground shadow-sm focus:outline-none focus:ring-2 focus:ring-primary transition" placeholder="Titre de la page">
                </div>
                <div>
                    <label class="label">Slug</label>
                    <input type="text" name="slug" x-model="slug" class="w-full h-10 px-3 rounded-md border border-border bg-background text-foreground placeholder:text-muted-foreground shadow-sm focus:outline-none focus:ring-2 focus:ring-primary transition" placeholder="slug-exemple">
                </div>
                <div>
                    <label class="label">Méta description</label>
                    <textarea name="meta_description" class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50" rows="4" placeholder="Description pour les moteurs de recherche (max 160 caractères)" required>{{ old('meta_description') }}</textarea>
                    @error('meta_description')<p class="text-destructive text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </form>

        <div class="flex flex-1 overflow-hidden">
            <aside class="w-full sm:w-1/3 md:w-1/5 max-w-xs bg-muted p-4 border-r overflow-y-auto">
                <div class="mb-4">
                    <input type="text" x-model="searchQuery" placeholder="Rechercher un bloc..."
                           class="w-full h-9 px-3 rounded-md border border-border text-sm focus:outline-none focus:ring-primary transition" />
                </div>

                <div class="flex flex-wrap gap-2 mb-4">
                    <button @click="filteredCategory = 'all'"
                            :class="filteredCategory === 'all' ? 'bg-primary text-white' : 'bg-card text-foreground'"
                            class="text-sm px-3 py-1 rounded-md border border-border">Tous</button>
                    <template x-for="category in [...new Set(availableBlocks.map(b => b.category))]" :key="category">
                        <button @click="filteredCategory = category"
                                :class="filteredCategory === category ? 'bg-primary text-white' : 'bg-card text-foreground'"
                                class="text-sm px-3 py-1 rounded-md border border-border"
                                x-text="category"></button>
                    </template>
                </div>

                <template x-if="filteredBlocks.length === 0">
                    <p class="text-muted-foreground italic">Aucun bloc trouvé.</p>
                </template>

                <template x-for="(block, idx) in filteredBlocks" :key="idx">
                    <button @click.prevent="addBlock(block.type)"
                            class="w-full flex items-center gap-2 px-3 py-2 mb-2 rounded bg-background border hover:bg-accent hover:text-accent-foreground transition text-left">
                        <span x-text="block.icon" class="text-lg"></span>
                        <span class="text-sm font-medium" x-text="block.label"></span>
                    </button>
                </template>
            </aside>


            <main class="flex-1 relative bg-background overflow-auto">
                <div id="builder-preview-wrapper" class="relative h-full min-h-[calc(100vh-250px)]">
                    <div id="grid-overlay"
                         class="absolute inset-0 pointer-events-none z-40"
                         :class="{ 'hidden': !gridVisible }">
                        <div class="w-full h-full opacity-60"
                             style="background-image: url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='40' height='40'%3E%3Crect width='40' height='40' fill='none' stroke='%23ccc' stroke-width='1'/%3E%3C/svg%3E&quot;);"></div>
                    </div>

                    <div id="builder-preview" class="relative w-full h-full p-4 space-y-4 flex flex-col"></div>
                </div>

                <form method="POST" action="{{ route('admin.pages.store') }}" class="absolute bottom-4 right-4 z-50">
                    @csrf
                    <input type="hidden" name="title" :value="title">
                    <input type="hidden" name="slug" :value="slug">
                    <input type="hidden" name="meta_description" value="">

                    <input type="hidden" name="content" :value="serializeBlocks()">

                    <input type="hidden" name="status" value="published">
                    <input type="hidden" name="is_home" value="0">
                    <button class="border border-input bg-background hover:bg-accent hover:text-accent-foreground flex items-center gap-2 h-9 rounded-md px-3">Enregistrer la page</button>
                </form>
            </main>
        </div>

        @include('admin.partials.builder.edit-menu')
        @include('admin.partials.builder.context-menu')
        @include('admin.partials.builder.shortcuts-modal')

    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script type="module">
        import pageBuilder from '{{ asset('assets/js/builder/pageBuilder.js') }}';
        window.pageBuilder = pageBuilder;
    </script>
@endpush
