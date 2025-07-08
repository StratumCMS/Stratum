@php
    $typeColors = [
        'home' => 'bg-blue-100 text-blue-800',
        'module' => 'bg-purple-100 text-purple-800',
        'external_link' => 'bg-green-100 text-green-800',
        'page' => 'bg-orange-100 text-orange-800',
        'post' => 'bg-red-100 text-red-800',
        'post_list' => 'bg-pink-100 text-pink-800',
        'dropdown' => 'bg-gray-100 text-gray-800',
    ];
@endphp

<tr
    data-id="{{ $item->id }}"
    class="border-b transition-colors hover:bg-muted/50 {{ $isChild ? 'bg-muted/30' : 'cursor-move' }}"
>
    <td class="p-4 align-middle [&:has([role=checkbox])]:pr-0 w-8 px-4">
        @if ($isChild)
            <i class="fas fa-chevron-right h-4 w-4 ml-4 text-muted-foreground"></i>
        @else
            <i class="fas fa-grip-vertical h-4 w-4 text-muted-foreground"></i>
        @endif
    </td>

    <td class="p-4 align-middle [&:has([role=checkbox])]:pr-0 font-medium">
        <div class="flex items-center gap-2">
            <span class="{{ $isChild ? 'ml-4' : '' }}">{{ $item->name }}</span>
        </div>
    </td>

    <td class="p-4 align-middle [&:has([role=checkbox])]:pr-0">
        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $typeColors[$item->type] ?? 'bg-gray-100 text-gray-800' }}">
            {{ ucfirst(str_replace('_', ' ', $item->type)) }}
        </span>
    </td>

    <td class="p-4 align-middle [&:has([role=checkbox])]:pr-0 text-muted-foreground">
        {{ $item->value ?? ($item->type === 'home' ? '/' : '—') }}
    </td>

    <td class="p-4 align-middle [&:has([role=checkbox])]:pr-0 text-center">
        {{ $item->position }}
    </td>

    <td class="p-4 align-middle [&:has([role=checkbox])]:pr-0">
        <div class="flex space-x-2">
            <a href="{{ route('navbar.edit', $item) }}"
               class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-8 w-8 p-0">
                <i class="fas fa-pen h-4 w-4"></i>
            </a>
            <form action="{{ route('navbar.destroy', $item) }}" method="POST"
                  onsubmit="return confirm('Supprimer cet élément ?')">
                @csrf @method('DELETE')
                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 border border-input bg-background hover:bg-accent h-8 w-8 p-0 text-destructive hover:text-destructive">
                    <i class="fas fa-trash h-4 w-4"></i>
                </button>
            </form>
        </div>
    </td>
</tr>

@if ($item->children)
    @foreach ($item->children as $child)
        @include('admin.partials.navbar-row', ['item' => $child, 'isChild' => true])
    @endforeach
@endif
