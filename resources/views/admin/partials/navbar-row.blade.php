<tr data-id="{{ $item->id }}" class="sortable-item hover:bg-muted/20 transition-colors {{ $isChild ? 'bg-muted/10' : '' }}">
    <td class="px-4 py-3">
        <div class="sortable-handle cursor-grab active:cursor-grabbing text-muted-foreground hover:text-foreground transition-colors p-1 rounded">
            <i class="fas fa-grip-lines"></i>
        </div>
    </td>

    <td class="px-4 py-3">
        <div class="flex items-center space-x-3 min-w-0 {{ $isChild ? 'ml-6' : '' }}">
            @if($isChild)
                <i class="fas fa-level-up-alt rotate-90 text-muted-foreground/60 text-xs"></i>
            @endif
            <div class="flex items-center space-x-2 min-w-0 flex-1">
                @if($item->icon)
                    <i class="{{ $item->icon }} text-muted-foreground w-4 h-4 flex-shrink-0"></i>
                @endif
                <span class="font-medium text-foreground truncate" title="{{ $item->name }}">
                    {{ $item->name }}
                </span>
            </div>
        </div>
    </td>

    <td class="px-4 py-3">
        @if($item->type === 'dropdown')
            <span class="inline-flex items-center rounded-full bg-purple-500/10 text-purple-600 px-2.5 py-0.5 text-xs font-medium">
                <i class="fas fa-caret-down mr-1.5 w-3 h-3"></i>
                Dropdown
            </span>
        @else
            <span class="inline-flex items-center rounded-full bg-green-500/10 text-green-600 px-2.5 py-0-5 text-xs font-medium">
                <i class="fas fa-link mr-1.5 w-3 h-3"></i>
                Lien
            </span>
        @endif
    </td>

    <td class="px-4 py-3">
        <div class="flex items-center space-x-2 min-w-0">
            @if($item->type === 'link')
                <i class="fas fa-external-link-alt text-muted-foreground w-4 h-4 flex-shrink-0"></i>
                <span class="text-sm text-muted-foreground font-mono truncate" title="{{ $item->url }}">
                    {{ $item->url ?? '-' }}
                </span>
            @else
                <span class="text-sm text-muted-foreground italic">
                    {{ $item->children->count() }} sous-élément(s)
                </span>
            @endif
        </div>
    </td>

    <td class="px-4 py-3">
        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-primary/10 text-primary text-xs font-medium">
            {{ $item->position }}
        </span>
    </td>

    <td class="px-4 py-3">
        <div class="flex items-center space-x-1">
            <a href="{{ route('navbar.edit', $item) }}"
               class="inline-flex items-center justify-center rounded-lg p-2 text-muted-foreground hover:text-foreground hover:bg-accent transition-colors focus:outline-none focus:ring-2 focus:ring-ring"
               title="Modifier">
                <i class="fas fa-edit w-4 h-4"></i>
            </a>

            <button @click="openDeleteModal('{{ $item->id }}', '{{ $item->name }}')"
                    class="inline-flex items-center justify-center rounded-lg p-2 text-muted-foreground hover:text-destructive hover:bg-destructive/10 transition-colors focus:outline-none focus:ring-2 focus:ring-ring"
                    title="Supprimer">
                <i class="fas fa-trash-alt w-4 h-4"></i>
            </button>
        </div>
    </td>
</tr>

@if($item->type === 'dropdown' && $item->children->isNotEmpty())
    @foreach($item->children as $child)
        @include('admin.partials.navbar-row', ['item' => $child, 'isChild' => true])
    @endforeach
@endif
