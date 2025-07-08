@props(['totalPages', 'currentPage'])

@if ($totalPages > 1)
    <nav class="flex justify-center mt-6">
        <ul class="flex items-center space-x-2">
            @if ($currentPage > 1)
                <li>
                    <a href="{{ request()->fullUrlWithQuery(['page' => $currentPage - 1]) }}"
                       class="inline-flex items-center justify-center px-4 py-2 text-white rounded-md neon-btn bg-gradient-to-r from-indigo-500 to-purple-500 hover:brightness-110 transition">
                        Précédent
                    </a>
                </li>
            @else
                <li class="inline-flex items-center justify-center px-4 py-2 text-gray-500 bg-gray-800 rounded-md cursor-not-allowed">Précédent</li>
            @endif

            @for ($page = 1; $page <= $totalPages; $page++)
                @if ($page == $currentPage)
                    <li class="inline-flex items-center justify-center px-4 py-2 text-white rounded-md neon-btn bg-gradient-to-r from-indigo-500 to-purple-500 font-bold hover:brightness-110 transition">{{ $page }}</li>
                @else
                    <li>
                        <a href="{{ request()->fullUrlWithQuery(['page' => $page]) }}"
                           class="inline-flex items-center justify-center px-4 py-2 text-white rounded-md neon-btn bg-gradient-to-r from-indigo-500 to-purple-500 hover:brightness-110 transition">
                            {{ $page }}
                        </a>
                    </li>
                @endif
            @endfor

            @if ($currentPage < $totalPages)
                <li>
                    <a href="{{ request()->fullUrlWithQuery(['page' => $currentPage + 1]) }}"
                       class="inline-flex items-center justify-center px-4 py-2 text-white rounded-md neon-btn bg-gradient-to-r from-indigo-500 to-purple-500 hover:brightness-110 transition">
                        Suivant
                    </a>
                </li>
            @else
                <li class="inline-flex items-center justify-center px-4 py-2 text-gray-500 bg-gray-800 rounded-md cursor-not-allowed">Suivant</li>
            @endif
        </ul>
    </nav>
@endif
