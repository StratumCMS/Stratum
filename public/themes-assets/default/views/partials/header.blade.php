<div class="hidden md:flex items-center space-x-2">
    @foreach($navigationItems as $item)
        @if($item->isDropdown() && $item->children->isNotEmpty())
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open"
                        class="text-sm px-3 py-2 rounded-md font-medium text-gray-300 hover:text-purple-300 transition duration-300 flex items-center gap-1">
                    @if($item->icon)<i class="{{ $item->icon }} mr-1"></i>@endif
                    {{ $item->name }}
                    <svg class="h-4 w-4 transform transition-transform duration-200"
                         :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" stroke-width="2"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="open" x-transition @click.away="open = false"
                     class="absolute mt-2 w-48 bg-gray-800 rounded shadow-lg z-50 py-1">
                    @foreach($item->children as $child)
                        <a href="{{ $child->getLink() }}"
                           class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white transition"
                           @if($child->new_tab) target="_blank" rel="noopener noreferrer" @endif>
                            @if($child->icon)<i class="{{ $child->icon }} mr-1"></i>@endif
                            {{ $child->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        @else
            <a href="{{ $item->getLink() }}"
               class="text-sm px-3 py-2 rounded-md font-medium text-gray-300 hover:text-purple-300 transition-colors duration-300 @if($item->isCurrent()) text-white @endif"
               @if($item->new_tab) target="_blank" rel="noopener noreferrer" @endif>
                @if($item->icon)<i class="{{ $item->icon }} mr-1"></i>@endif
                {{ $item->name }}
            </a>
        @endif
    @endforeach
</div>
