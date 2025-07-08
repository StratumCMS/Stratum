@extends('admin.layouts.admin')

@section('title', 'Mon Profil')

@section('content')
    <div x-data="{ tab: 'infos' }" class="max-w-2xl mx-auto space-y-6">
        {{-- Onglets --}}
        <div class="flex space-x-4 border-b border-muted pb-2">
            <button @click="tab = 'infos'"
                    :class="tab === 'infos' ? 'border-b-2 border-primary text-primary' : 'text-muted-foreground hover:text-primary'"
                    class="px-3 py-1 font-semibold transition">
                <i class="fas fa-user mr-1"></i> Infos personnelles
            </button>
            <button @click="tab = 'password'"
                    :class="tab === 'password' ? 'border-b-2 border-primary text-primary' : 'text-muted-foreground hover:text-primary'"
                    class="px-3 py-1 font-semibold transition">
                <i class="fas fa-lock mr-1"></i> Mot de passe
            </button>
        </div>

        {{-- Formulaire Infos personnelles --}}
        <div x-show="tab === 'infos'" x-transition class="rounded-lg border bg-card text-card-foreground shadow-sm hover-glow-purple">
            <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="flex flex-col space-y-1.5 p-6">
                    <div class="text-2xl font-semibold leading-none tracking-tight flex items-center space-x-2">
                        <i class="fas fa-user w-5 h-5"></i>
                        <h2 class="text-xl font-bold mb-4">Informations personnelles</h2>
                    </div>
                    <div class="text-sm text-muted-foreground">
                        Gérez vos informations de profil
                    </div>
                </div>

                {{-- Avatar --}}
                <div class="p-6 pt-0 space-y-6">
                    <div class="flex items-center space-x-6">
                        <div class="w-20 h-20 relative flex shrink-0 overflow-hidden rounded-full">
                            <img src="{{ auth()->user()->avatar_url ?? 'https://placehold.co/80' }}" alt="Avatar"
                                 class="text-lg bg-primary text-primary-foreground">
                        </div>

                        <div class="space-y-2">
                            <label for="avatar" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70 cursor-pointer">
                                <div class="flex items-center space-x-2 text-sm font-medium text-primary hover:text-primary/80">
                                    <i class="fas fa-camera mr-2 w-4 h-4"></i> Changer l'avatar
                                </div>
                            </label>
                            <input type="file" name="avatar" id="avatar" accept="image/*" class="hidden">
                            @error('avatar')
                            <p class="text-xs text-destructive">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-muted-foreground">JPG, PNG, GIF. Max: 2MB.</p>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label for="name" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Nom complet</label>
                        <input type="text" name="name" id="name" value="{{ old('name', auth()->user()->name) }}"
                               class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm">
                        @error('name')
                        <p class="text-xs text-destructive mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="email" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Adresse email</label>

                        <input type="email" name="email" id="email" value="{{ old('email', auth()->user()->email) }}"
                               class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm">
                        @error('email')
                        <p class="text-xs text-destructive mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>


                <div class="flex justify-end">
                    <button type="submit" class="hover-glow-purple bg-primary hover:bg-primary/90 text-white px-4 py-2 rounded-md">
                        <i class="fas fa-save mr-2"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>

        {{-- Formulaire mot de passe --}}
        <div x-show="tab === 'password'" x-transition class="rounded-lg border bg-card text-card-foreground shadow-sm hover-glow-purple">
            <form action="{{ route('admin.profile.update') }}" method="POST">
                @csrf
                <div class="flex flex-col space-y-1.5 p-6">
                    <div class="text-2xl font-semibold leading-none tracking-tight flex items-center space-x-2">
                        <i class="fas fa-lock w-5 h-5"></i>
                        <h2 class="text-xl font-bold mb-4">Changer le mot de passe</h2>
                    </div>
                    <div class="text-sm text-muted-foreground">
                        Assurez-vous d'utiliser un mot de passe fort
                    </div>
                </div>

                <div class="p-6 pt-0 space-y-6">
                    <div class="space-y-2">
                        <label for="current_password" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Mot de passe actuel</label>
                        <div class="relative">
                            <input type="password" name="current_password" id="current_password"
                                   class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm">
                        </div>
                        @error('current_password')
                        <p class="text-xs text-destructive mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Nouveau --}}
                    <div class="space-y-2">
                        <label for="new_password" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Nouveau mot de passe</label>
                        <div class="relative">
                            <input type="password" name="new_password" id="new_password"
                                   class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm">
                        </div>
                        @error('new_password')
                        <p class="text-xs text-destructive mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Confirmation --}}
                    <div class="space-y-2">
                        <label for="new_password_confirmation" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Confirmer le nouveau mot de passe</label>
                        <div class="relative">
                            <input type="password" name="new_password_confirmation" id="new_password_confirmation"
                                   class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm">
                        </div>
                    </div>

                    <div class="p-4 bg-muted/50 rounded-lg">
                        <h4 class="font-medium mb-2">Critères du mot de passe :</h4>
                        <ul class="text-sm text-muted-foreground space-y-1">
                            <li>• Au moins 8 caractères</li>
                            <li>• Une majuscule et une minuscule</li>
                            <li>• Au moins un chiffre</li>
                            <li>• Un caractère spécial (!@#$%^&*)</li>
                        </ul>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 h-10 px-4 py-2 text-primary-foreground  hover-glow-purple bg-primary hover:bg-primary/90">
                            <i class="fas fa-lock mr-2"></i> Changer le mot de passe
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
