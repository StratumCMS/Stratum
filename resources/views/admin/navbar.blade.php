@extends('admin.layouts.admin')

@section('title', 'Gestion Navigation')

@section('content')
    <div x-data="navbar()" x-init="init()" class="space-y-6">

        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-semibold leading-none tracking-tight">Gestion de la Navigation</h1>
            <a href="{{ route('navbar.create') }}"
               class="inline-flex items-center gap-2 rounded-md bg-primary text-primary-foreground text-sm font-medium px-4 py-2 hover:bg-primary/90">
                <i class="fas fa-plus"></i> Ajouter un élément
            </a>
        </div>

        <div class="space-y-6">
            <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
                <div class="flex flex-col space-y-1.5 p-6">
                    <h3 class="text-2xl font-semibold leading-none tracking-tight">Éléments de Navigation</h3>
                </div>
                <div class="p-6 pt-0">
                    <div class="rounded-md border relative w-full overflow-auto">
                        <table class="w-full caption-bottom text-sm">
                            <thead class="[&_tr]:border-b">
                            <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                                <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&:has([role=checkbox])]:pr-0 w-8"></th>
                                <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&:has([role=checkbox])]:pr-0">Nom</th>
                                <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&:has([role=checkbox])]:pr-0">Type</th>
                                <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&:has([role=checkbox])]:pr-0">URL</th>
                                <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&:has([role=checkbox])]:pr-0">Position</th>
                                <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&:has([role=checkbox])]:pr-0">Actions</th>
                            </tr>
                            </thead>
                            <tbody id="sortable">
                            @foreach($items as $item)
                                @include('admin.partials.navbar-row', ['item' => $item, 'isChild' => false])
                            @endforeach
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
            <div class="flex flex-col space-y-1.5 p-6">
                <h3 class="text-2xl font-semibold leading-none tracking-tight">Information</h3>
            </div>
            <div class="p-6 pt-0">
                <p class="text-muted-foreground">
                    Glissez-déposez les éléments pour réorganiser la navigation.
                    Les éléments de type "Dropdown" peuvent contenir des sous-éléments.
                </p>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script src="https://unpkg.com/alpinejs" defer></script>

    <script>
        function navbar() {
            return {
                init() {
                    new Sortable(document.getElementById('sortable'), {
                        animation: 150,
                        onEnd: () => {
                            const order = [...document.querySelectorAll('#sortable tr[data-id]')].map(el => el.dataset.id);
                            fetch("{{ route('navbar.reorder') }}", {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({ order })
                            });
                        }
                    });
                }
            }
        }
    </script>
@endpush
