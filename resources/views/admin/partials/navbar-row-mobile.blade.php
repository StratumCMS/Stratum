<div data-id="{{ $item->id }}" class="sortable-item p-4 hover:bg-muted/20 transition-colors {{ $isChild ? 'bg-muted/10 border-l-4 border-l-primary/50' : '' }}">
    <div class="flex items-start justify-between">
        <div class="flex items-start space-x-3 flex-1 min-w-0">
            <div class="sortable-handle cursor-grab active:cursor-grabbing text-muted-foreground hover:text-foreground transition-colors p-1 mt-1 rounded">
                <i class="fas fa-grip-lines"></i>
            </div>

            <div class="flex-1 min-w-0">
                <div class="flex items-center space-x-2 mb-2">
                    @if($item->icon)
                        <i class="{{ $item->icon }} text-muted-foreground w-4 h-4 flex-shrink-0"></i>
                    @endif
                    <h4 class="font-medium text-foreground truncate" title="{{ $item->name }}">
                        {{ $item->name }}
                    </h4>
                </div>

                <div class="space-y-2">
                    <div class="flex items-center space-x-2">
                        @if($item->type === 'dropdown')
                            <span class="inline-flex items-center rounded-full bg-purple-500/10 text-purple-600 px-2 py-1 text-xs font-medium">
                                <i class="fas fa-caret-down mr-1 w-3 h-3"></i>
                                Dropdown
                            </span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-green-500/10 text-green-600 px-2 py-1 text-xs font-medium">
                                <i class="fas fa-link mr-1 w-3 h-3"></i>
                                Lien
                            </span>
                        @endif

                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-primary/10 text-primary text-xs font-medium">
                            {{ $item->position }}
                        </span>
                    </div>

                    <div class="flex items-center space-x-2 text-xs text-muted-foreground">
                        @if($item->type === 'link')
                            <i class="fas fa-external-link-alt w-3 h-3"></i>
                            <span class="truncate font-mono" title="{{ $item->url }}">
                                {{ $item->url ?? '-' }}
                            </span>
                        @else
                            <i class="fas fa-folder w-3 h-3"></i>
                            <span>{{ $item->children->count() }} sous-élément(s)</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center space-x-1 ml-2 flex-shrink-0">
            <a href="{{ route('navbar.edit', $item) }}"
               class="inline-flex items-center justify-center rounded-lg p-2 text-muted-foreground hover:text-foreground hover:bg-accent transition-colors"
               title="Modifier">
                <i class="fas fa-edit w-4 h-4"></i>
            </a>

            <button @click="openDeleteModal('{{ $item->id }}', '{{ $item->name }}')"
                    class="inline-flex items-center justify-center rounded-lg p-2 text-muted-foreground hover:text-destructive hover:bg-destructive/10 transition-colors"
                    title="Supprimer">
                <i class="fas fa-trash-alt w-4 h-4"></i>
            </button>
        </div>
    </div>
</div>

@if($item->type === 'dropdown' && $item->children->isNotEmpty())
    <div class="ml-4 border-l border-border/50">
        @foreach($item->children as $child)
            @include('admin.partials.navbar-row-mobile', ['item' => $child, 'isChild' => true])
        @endforeach
    </div>
@endif
