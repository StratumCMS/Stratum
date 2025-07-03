@extends('admin.layouts.admin')

@section('title', 'Médias')

@section('content')
    <div class="space-y-8 max-w-6xl mx-auto" x-data="{ openUpload: false, fileName: '', filePath: '', file: null }">
        {{-- Success message --}}
        @if(session('success'))
            <div class="p-4 bg-green-100 text-green-800 rounded-lg border border-green-300 shadow">
                <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            </div>
        @endif

        {{-- Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <x-dashboard.card title="Total Fichiers" icon="fas fa-file-alt" color="primary" :value="$mediaCount" />
            <x-dashboard.card title="Images" icon="fas fa-image" color="success" :value="$imageCount" />
            <x-dashboard.card title="Vidéos" icon="fas fa-video" color="purple-600" :value="$videoCount" />
        </div>

        {{-- Upload Button --}}
        <div class="text-right">
            <button @click="openUpload = true"
                    class="bg-primary text-white px-4 py-2 rounded hover:bg-primary/90 transition shadow inline-flex items-center">
                <i class="fas fa-upload mr-2"></i> Ajouter un fichier
            </button>
        </div>

        {{-- Upload Modal --}}
        <div x-show="openUpload" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="bg-card border rounded-2xl shadow-xl w-full max-w-lg p-6 relative text-card-foreground">
                <button @click="openUpload = false"
                        class="absolute top-3 right-3 text-muted-foreground hover:text-primary transition text-xl">&times;</button>

                <h2 class="text-lg font-semibold mb-4">Uploader un fichier</h2>

                <form action="{{ route('admin.media.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div>
                        <label class="text-sm font-medium text-muted-foreground block mb-1">Fichier</label>
                        <input type="file" name="media"
                               @change="file = $event.target.files[0]; fileName = file.name; filePath = '/storage/uploads/' + file.name"
                               class="w-full px-3 py-2 border border-input rounded-md bg-background text-sm focus:outline-none focus:ring-2 focus:ring-ring focus:border-ring" required>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-muted-foreground block mb-1">Nom personnalisé</label>
                        <input type="text" name="name" x-model="fileName"
                               class="w-full px-3 py-2 border border-input rounded-md bg-background text-sm focus:outline-none focus:ring-2 focus:ring-ring focus:border-ring" />
                    </div>

                    <div>
                        <label class="text-sm font-medium text-muted-foreground block mb-1">Chemin</label>
                        <input type="text" name="path" x-model="filePath"
                               class="w-full px-3 py-2 border border-input rounded-md bg-background text-sm focus:outline-none focus:ring-2 focus:ring-ring focus:border-ring" />
                    </div>

                    <div class="text-right">
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-md text-sm font-medium bg-primary text-white hover:bg-primary/90 transition shadow">
                            <i class="fas fa-upload"></i> Upload
                        </button>
                    </div>
                </form>
            </div>
        </div>


        {{-- Media Table --}}
        <div class="bg-card border rounded-2xl shadow p-6 text-card-foreground">
            <h2 class="text-xl font-semibold mb-4">Fichiers Médias</h2>

            <div class="overflow-x-auto rounded-lg">
                <table class="w-full text-sm text-left border-collapse">
                    <thead>
                    <tr class="text-xs uppercase text-muted-foreground border-b">
                        <th class="py-2 px-3">Type</th>
                        <th class="py-2 px-3">Nom</th>
                        <th class="py-2 px-3">Taille</th>
                        <th class="py-2 px-3">Chemin</th>
                        <th class="py-2 px-3">Date</th>
                        <th class="py-2 px-3">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($mediaItems as $file)
                        <tr class="border-b hover:bg-muted/50 transition">
                            <td class="py-2 px-3">
                                <div class="flex items-center gap-2">
                                    @if($file->file_type === 'image')
                                        <i class="fas fa-image text-primary"></i>
                                    @elseif($file->file_type === 'video')
                                        <i class="fas fa-video text-purple-600"></i>
                                    @else
                                        <i class="fas fa-file text-muted-foreground"></i>
                                    @endif
                                    <span class="text-xs px-2 py-0.5 bg-muted rounded capitalize">{{ $file->file_type }}</span>
                                </div>
                            </td>
                            <td class="py-2 px-3">{{ $file->name }}</td>
                            <td class="py-2 px-3">{{ number_format($file->file_size / 1024, 1) }} KB</td>
                            <td class="py-2 px-3 font-mono text-xs truncate max-w-[200px]">{{ $file->file_path }}</td>
                            <td class="py-2 px-3">
                                @if($file->uploaded_at)
                                    {{ $file->uploaded_at->format('d/m/Y') }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="py-2 px-3">
                                <div class="flex items-center gap-2">
                                    <a href="{{ $file->file_path }}" target="_blank" class="text-primary hover:underline text-xs">Voir</a>
                                    <form action="{{ route('admin.media.delete', $file) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-destructive hover:underline text-xs"
                                                onclick="return confirm('Supprimer ce fichier ?')">
                                            Supprimer
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted-foreground py-4">Aucun fichier média</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/alpinejs" defer></script>
@endpush
