@extends('admin.layouts.admin')

@section('title', 'Médias')

@section('content')
    <div x-data="mediaManager()" x-init="init()" class="space-y-4 sm:space-y-6 max-w-7xl mx-auto">

        <div x-show="showDeleteModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
            <div x-show="showDeleteModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="bg-card border border-border rounded-xl shadow-2xl max-w-md w-full p-6">

                <div class="flex items-center justify-center w-16 h-16 rounded-full bg-destructive/10 mx-auto mb-4">
                    <i class="fas fa-exclamation-triangle text-destructive text-2xl"></i>
                </div>

                <div class="text-center mb-6">
                    <h3 class="text-lg font-semibold text-foreground mb-2">
                        Confirmer la suppression
                    </h3>
                    <p class="text-muted-foreground" x-text="`Êtes-vous sûr de vouloir supprimer « ${deleteItemName} » ?`"></p>
                    <p class="text-sm text-muted-foreground mt-2">
                        Cette action est irréversible et supprimera définitivement le fichier.
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <button @click="showDeleteModal = false"
                            class="flex-1 inline-flex items-center justify-center gap-2 rounded-lg border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2 text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-ring">
                        <i class="fas fa-times w-4 h-4"></i>
                        Annuler
                    </button>
                    <button @click="confirmDelete()"
                            :disabled="isDeleting"
                            class="flex-1 inline-flex items-center justify-center gap-2 rounded-lg bg-destructive text-destructive-foreground hover:bg-destructive/90 h-10 px-4 py-2 text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-ring disabled:opacity-50 disabled:cursor-not-allowed"
                            :class="{'opacity-50 cursor-not-allowed': isDeleting}">
                        <i class="fas" :class="isDeleting ? 'fa-spinner fa-spin' : 'fa-trash-alt'"></i>
                        <span x-text="isDeleting ? 'Suppression...' : 'Supprimer'"></span>
                    </button>
                </div>
            </div>
        </div>

        <div x-show="showSyncModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
            <div x-show="showSyncModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="bg-card border border-border rounded-xl shadow-2xl max-w-md w-full p-6">

                <div class="flex items-center justify-center w-16 h-16 rounded-full bg-blue-500/10 mx-auto mb-4">
                    <i class="fas fa-sync-alt text-blue-500 text-2xl"></i>
                </div>

                <div class="text-center mb-6">
                    <h3 class="text-lg font-semibold text-foreground mb-2">
                        Resynchroniser Storage
                    </h3>
                    <p class="text-muted-foreground">
                        Cette opération va recréer le lien symbolique entre le dossier storage et le dossier public.
                    </p>
                    <div class="mt-4 p-4 bg-muted/50 rounded-lg text-left">
                        <div class="flex items-start space-x-2">
                            <i class="fas fa-info-circle text-blue-500 mt-0.5 flex-shrink-0"></i>
                            <div class="text-sm">
                                <p class="font-medium text-foreground mb-1">À savoir :</p>
                                <ul class="text-muted-foreground space-y-1 text-xs">
                                    <li>• L'opération peut prendre quelques secondes</li>
                                    <li>• Aucune donnée ne sera perdue</li>
                                    <li>• Les fichiers restent accessibles</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <button @click="showSyncModal = false"
                            :disabled="isSyncing"
                            class="flex-1 inline-flex items-center justify-center gap-2 rounded-lg border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2 text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-ring disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-times w-4 h-4"></i>
                        Annuler
                    </button>
                    <button @click="confirmSync()"
                            :disabled="isSyncing"
                            class="flex-1 inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 h-10 px-4 py-2 text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                            :class="{'opacity-50 cursor-not-allowed': isSyncing}">
                        <i class="fas" :class="isSyncing ? 'fa-spinner fa-spin' : 'fa-sync-alt'"></i>
                        <span x-text="isSyncing ? 'Synchronisation...' : 'Confirmer'"></span>
                    </button>
                </div>
            </div>
        </div>

        <div x-show="uploadModalOpen"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
            <div x-show="uploadModalOpen"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="bg-card border border-border rounded-xl shadow-2xl max-w-lg w-full p-6">

                <!-- Header -->
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center">
                            <i class="fas fa-upload text-primary text-sm"></i>
                        </div>
                        <h2 class="text-lg font-semibold text-foreground">Uploader un fichier</h2>
                    </div>
                    <button @click="uploadModalOpen = false"
                            class="inline-flex items-center justify-center rounded-lg p-2 text-muted-foreground hover:text-foreground hover:bg-accent transition-colors">
                        <i class="fas fa-times w-5 h-5"></i>
                    </button>
                </div>

                <form action="{{ route('admin.media.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div class="space-y-2">
                        <label class="text-sm font-medium text-foreground flex items-center justify-between">
                            <span>Fichier</span>
                            <span class="text-xs text-muted-foreground font-normal">Requis</span>
                        </label>
                        <div class="border-2 border-dashed border-border rounded-lg p-6 text-center transition-colors hover:border-primary/50"
                             @drop.prevent="handleFileDrop($event)"
                             @dragover.prevent="dragOver = true"
                             @dragleave="dragOver = false"
                             :class="{'border-primary bg-primary/5': dragOver}">
                            <i class="fas fa-cloud-upload-alt text-3xl text-muted-foreground mb-3"></i>
                            <p class="text-sm text-muted-foreground mb-2" x-text="uploadForm.file ? uploadForm.file.name : 'Glissez un fichier ici ou cliquez pour choisir'"></p>
                            <p class="text-xs text-muted-foreground mb-4">
                                PNG, JPG, WEBP, MP4, PDF jusqu'à 10MB
                            </p>
                            <input type="file"
                                   name="media"
                                   @change="uploadForm.file = $event.target.files[0]; generateFileName()"
                                   class="hidden"
                                   x-ref="fileInput"
                                   required>
                            <button type="button"
                                    @click="$refs.fileInput.click()"
                                    class="inline-flex items-center justify-center gap-2 rounded-lg border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2 text-sm font-medium transition-colors">
                                <i class="fas fa-folder-open w-4 h-4"></i>
                                Choisir un fichier
                            </button>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium text-foreground">Nom personnalisé</label>
                        <input type="text"
                               name="name"
                               x-model="uploadForm.name"
                               class="flex h-10 w-full rounded-lg border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 transition-colors"
                               placeholder="Nom d'affichage du fichier">
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium text-foreground">Chemin de stockage</label>
                        <input type="text"
                               name="path"
                               x-model="uploadForm.path"
                               class="flex h-10 w-full rounded-lg border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 transition-colors font-mono text-xs"
                               placeholder="/storage/uploads/">
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3 pt-4">
                        <button type="button"
                                @click="uploadModalOpen = false"
                                class="flex-1 inline-flex items-center justify-center gap-2 rounded-lg border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2 text-sm font-medium transition-colors">
                            <i class="fas fa-times w-4 h-4"></i>
                            Annuler
                        </button>
                        <button type="submit"
                                :disabled="!uploadForm.file"
                                class="flex-1 inline-flex items-center justify-center gap-2 rounded-lg bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2 text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-ring disabled:opacity-50 disabled:cursor-not-allowed"
                                :class="{'opacity-50 cursor-not-allowed': !uploadForm.file}">
                            <i class="fas fa-upload w-4 h-4"></i>
                            Uploader
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center space-x-4">
                <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
                    <i class="fas fa-images text-primary text-sm"></i>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-semibold text-foreground">Gestion des Médias</h1>
                    <p class="text-sm text-muted-foreground hidden sm:block">
                        Gérez tous vos fichiers multimédias
                    </p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
            <div class="rounded-xl border bg-card text-card-foreground shadow-sm p-4 sm:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs sm:text-sm font-medium text-muted-foreground mb-1">Total Fichiers</p>
                        <h3 class="text-xl sm:text-2xl font-bold text-foreground">{{ $mediaCount }}</h3>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-blue-500/10 flex items-center justify-center">
                        <i class="fas fa-file-alt text-blue-500 text-sm"></i>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border bg-card text-card-foreground shadow-sm p-4 sm:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs sm:text-sm font-medium text-muted-foreground mb-1">Images</p>
                        <h3 class="text-xl sm:text-2xl font-bold text-foreground">{{ $imageCount }}</h3>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-green-500/10 flex items-center justify-center">
                        <i class="fas fa-image text-green-500 text-sm"></i>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border bg-card text-card-foreground shadow-sm p-4 sm:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs sm:text-sm font-medium text-muted-foreground mb-1">Vidéos & Autres</p>
                        <h3 class="text-xl sm:text-2xl font-bold text-foreground">{{ $videoCount }}</h3>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-purple-500/10 flex items-center justify-center">
                        <i class="fas fa-video text-purple-500 text-sm"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 p-6 rounded-xl border bg-card text-card-foreground shadow-sm">
            <div class="flex items-center space-x-2 text-sm text-muted-foreground">
                <i class="fas fa-info-circle w-4 h-4"></i>
                <span>{{ $mediaItems->count() }} fichier(s) média</span>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                <button @click="openSyncModal()"
                        class="inline-flex items-center justify-center gap-2 rounded-lg border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2 text-sm font-medium transition-colors w-full sm:w-auto">
                    <i class="fas fa-sync-alt w-4 h-4"></i>
                    <span>Sync Storage</span>
                </button>

                <button @click="uploadModalOpen = true"
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2 text-sm font-medium transition-colors w-full sm:w-auto">
                    <i class="fas fa-upload w-4 h-4"></i>
                    <span>Ajouter un fichier</span>
                </button>
            </div>
        </div>

        <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
            <div class="p-4 sm:p-6 border-b border-border">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center">
                        <i class="fas fa-list text-primary text-sm"></i>
                    </div>
                    <h2 class="text-lg sm:text-xl font-semibold text-foreground">Fichiers Médias</h2>
                </div>
            </div>

            <div class="block sm:hidden">
                <div class="divide-y divide-border">
                    @forelse ($mediaItems as $file)
                        <div class="p-4 hover:bg-muted/20 transition-colors">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center space-x-3 flex-1 min-w-0">
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0"
                                         :class="{
                                            'bg-blue-500/10': '{{ $file->file_type }}' === 'image',
                                            'bg-purple-500/10': '{{ $file->file_type }}' === 'video',
                                            'bg-gray-500/10': !['image', 'video'].includes('{{ $file->file_type }}')
                                         }">
                                        <i class="text-sm
                                            {{ $file->file_type === 'image' ? 'fas fa-image text-blue-500' : '' }}
                                            {{ $file->file_type === 'video' ? 'fas fa-video text-purple-500' : '' }}
                                            {{ !in_array($file->file_type, ['image', 'video']) ? 'fas fa-file text-gray-500' : '' }}"></i>
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-medium text-foreground truncate" title="{{ $file->name }}">
                                            {{ $file->name }}
                                        </h4>
                                        <p class="text-xs text-muted-foreground font-mono truncate mt-1" title="{{ $file->file_path }}">
                                            {{ $file->file_path }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between text-xs text-muted-foreground mb-3">
                                <div class="flex items-center space-x-4">
                                    <span class="inline-flex items-center rounded-full bg-muted px-2 py-1 text-xs font-medium capitalize">
                                        {{ $file->file_type }}
                                    </span>
                                    <span>{{ number_format($file->file_size / 1024, 1) }} KB</span>
                                </div>
                                <span>
                                    @if($file->uploaded_at)
                                        {{ $file->uploaded_at->format('d/m/Y') }}
                                    @else
                                        —
                                    @endif
                                </span>
                            </div>

                            <div class="flex items-center justify-between">
                                <a href="{{ $file->file_path }}"
                                   target="_blank"
                                   class="inline-flex items-center justify-center gap-1 rounded-lg p-2 text-muted-foreground hover:text-foreground hover:bg-accent transition-colors text-xs">
                                    <i class="fas fa-eye w-3 h-3"></i>
                                    Voir
                                </a>
                                <button @click="openDeleteModal('{{ $file->id }}', '{{ $file->name }}')"
                                        class="inline-flex items-center justify-center gap-1 rounded-lg p-2 text-muted-foreground hover:text-destructive hover:bg-destructive/10 transition-colors text-xs">
                                    <i class="fas fa-trash-alt w-3 h-3"></i>
                                    Supprimer
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center">
                            <div class="w-16 h-16 rounded-full bg-muted/50 flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-images text-2xl text-muted-foreground"></i>
                            </div>
                            <h3 class="text-lg font-medium text-foreground mb-2">Aucun fichier média</h3>
                            <p class="text-muted-foreground mb-4">Commencez par uploader votre premier fichier</p>
                            <button @click="uploadModalOpen = true"
                                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2 text-sm font-medium transition-colors">
                                <i class="fas fa-upload mr-2"></i>
                                Uploader un fichier
                            </button>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="hidden sm:block overflow-auto">
                <table class="w-full">
                    <thead>
                    <tr class="border-b border-border bg-muted/10">
                        <th class="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Nom</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Taille</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Chemin</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider w-24">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                    @forelse ($mediaItems as $file)
                        <tr class="hover:bg-muted/20 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center space-x-2">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                                         :class="{
                                                'bg-blue-500/10': '{{ $file->file_type }}' === 'image',
                                                'bg-purple-500/10': '{{ $file->file_type }}' === 'video',
                                                'bg-gray-500/10': !['image', 'video'].includes('{{ $file->file_type }}')
                                             }">
                                        <i class="text-sm
                                                {{ $file->file_type === 'image' ? 'fas fa-image text-blue-500' : '' }}
                                                {{ $file->file_type === 'video' ? 'fas fa-video text-purple-500' : '' }}
                                                {{ !in_array($file->file_type, ['image', 'video']) ? 'fas fa-file text-gray-500' : '' }}"></i>
                                    </div>
                                    <span class="inline-flex items-center rounded-full bg-muted px-2.5 py-0.5 text-xs font-medium capitalize">
                                            {{ $file->file_type }}
                                        </span>
                                </div>
                            </td>

                            <td class="px-4 py-3">
                                    <span class="font-medium text-foreground" title="{{ $file->name }}">
                                        {{ $file->name }}
                                    </span>
                            </td>

                            <td class="px-4 py-3 text-sm text-muted-foreground">
                                {{ number_format($file->file_size / 1024, 1) }} KB
                            </td>

                            <td class="px-4 py-3">
                                    <span class="text-sm text-muted-foreground font-mono truncate max-w-xs block" title="{{ $file->file_path }}">
                                        {{ $file->file_path }}
                                    </span>
                            </td>

                            <td class="px-4 py-3 text-sm text-muted-foreground">
                                @if($file->uploaded_at)
                                    {{ $file->uploaded_at->format('d/m/Y') }}
                                @else
                                    —
                                @endif
                            </td>

                            <td class="px-4 py-3">
                                <div class="flex items-center space-x-1">
                                    <a href="{{ $file->file_path }}"
                                       target="_blank"
                                       class="inline-flex items-center justify-center rounded-lg p-2 text-muted-foreground hover:text-foreground hover:bg-accent transition-colors focus:outline-none focus:ring-2 focus:ring-ring"
                                       title="Voir le fichier">
                                        <i class="fas fa-eye w-4 h-4"></i>
                                    </a>
                                    <button @click="openDeleteModal('{{ $file->id }}', '{{ $file->name }}')"
                                            class="inline-flex items-center justify-center rounded-lg p-2 text-muted-foreground hover:text-destructive hover:bg-destructive/10 transition-colors focus:outline-none focus:ring-2 focus:ring-ring"
                                            title="Supprimer">
                                        <i class="fas fa-trash-alt w-4 h-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center">
                                <div class="w-16 h-16 rounded-full bg-muted/50 flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-images text-2xl text-muted-foreground"></i>
                                </div>
                                <h3 class="text-lg font-medium text-foreground mb-2">Aucun fichier média</h3>
                                <p class="text-muted-foreground mb-4">Commencez par uploader votre premier fichier</p>
                                <button @click="uploadModalOpen = true"
                                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2 text-sm font-medium transition-colors">
                                    <i class="fas fa-upload mr-2"></i>
                                    Uploader un fichier
                                </button>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        function mediaManager() {
            return {
                uploadModalOpen: false,
                showDeleteModal: false,
                showSyncModal: false,
                isDeleting: false,
                isSyncing: false,
                deleteItemId: null,
                deleteItemName: '',
                dragOver: false,

                uploadForm: {
                    file: null,
                    name: '',
                    path: '/storage/uploads/'
                },

                handleFileDrop(event) {
                    this.dragOver = false;
                    const files = event.dataTransfer.files;
                    if (files.length > 0) {
                        this.uploadForm.file = files[0];
                        this.generateFileName();
                    }
                },

                generateFileName() {
                    if (this.uploadForm.file && !this.uploadForm.name) {
                        const originalName = this.uploadForm.file.name;
                        const nameWithoutExt = originalName.substring(0, originalName.lastIndexOf('.'));
                        this.uploadForm.name = nameWithoutExt.replace(/[^a-zA-Z0-9]/g, '_');
                    }
                },

                openDeleteModal(itemId, itemName) {
                    this.deleteItemId = itemId;
                    this.deleteItemName = itemName;
                    this.showDeleteModal = true;
                    this.isDeleting = false;
                },

                openSyncModal() {
                    this.showSyncModal = true;
                    this.isSyncing = false;
                },

                async confirmDelete() {
                    if (!this.deleteItemId) return;

                    this.isDeleting = true;

                    try {
                        const response = await fetch(`/admin/media/${this.deleteItemId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });

                        if (response.ok) {
                            window.location.reload();
                        } else {
                            throw new Error('Erreur lors de la suppression');
                        }
                    } catch (error) {
                        console.error('Error deleting media:', error);
                        this.showNotification('Erreur lors de la suppression', 'error');
                        this.isDeleting = false;
                        this.showDeleteModal = false;
                    }
                },

                async confirmSync() {
                    this.isSyncing = true;

                    try {
                        const response = await fetch('{{ route('admin.media.sync') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });

                        if (response.ok) {
                            this.showNotification('Synchronisation réussie !', 'success');
                            this.showSyncModal = false;
                            setTimeout(() => window.location.reload(), 1500);
                        } else {
                            throw new Error('Erreur lors de la synchronisation');
                        }
                    } catch (error) {
                        console.error('Sync error:', error);
                        this.showNotification('Erreur lors de la synchronisation', 'error');
                        this.isSyncing = false;
                    }
                },

                showNotification(message, type = 'info') {
                    const toast = document.createElement('div');
                    toast.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg border transition-all duration-300 transform translate-x-full ${
                        type === 'success' ? 'bg-green-500/10 border-green-500/20 text-green-600' :
                            type === 'error' ? 'bg-red-500/10 border-red-500/20 text-red-600' :
                                'bg-blue-500/10 border-blue-500/20 text-blue-600'
                    }`;
                    toast.innerHTML = `
                        <div class="flex items-center space-x-2">
                            <i class="fas ${
                        type === 'success' ? 'fa-check-circle' :
                            type === 'error' ? 'fa-exclamation-circle' :
                                'fa-info-circle'
                    }"></i>
                            <span class="text-sm font-medium">${message}</span>
                        </div>
                    `;

                    document.body.appendChild(toast);

                    setTimeout(() => toast.classList.remove('translate-x-full'), 100);

                    setTimeout(() => {
                        toast.classList.add('translate-x-full');
                        setTimeout(() => {
                            if (toast.parentNode) {
                                toast.parentNode.removeChild(toast);
                            }
                        }, 300);
                    }, 3000);
                }
            }
        }
    </script>
@endpush
