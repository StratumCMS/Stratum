@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto" x-data="{ tab: 'profile' }">
        <div class="mb-8">
            <a href="{{ route('profile.show', auth()->user()->name) }}" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2 mb-4">
                <i class="fas fa-arrow-left h-4 w-4 mr-2"></i>
                Retour au profil
            </a>
            <h1 class="text-3xl font-bold text-foreground">Paramètres du compte</h1>
            <p class="text-muted-foreground mt-2">Gérez vos informations personnelles et la sécurité de votre compte</p>
        </div>

        <div class="space-y-6">
            <nav class="grid grid-cols-4 bg-muted/30 rounded overflow-hidden text-sm text-muted-foreground">
                <button @click="tab = 'profile'" :class="tab === 'profile' ? 'bg-primary text-white' : 'bg-muted text-muted-foreground'" class="py-2">Profil</button>
                <button @click="tab = 'password'" :class="tab === 'password' ? 'bg-primary text-white' : 'bg-muted text-muted-foreground'" class="py-2">Mot de passe</button>
                <button @click="tab = 'security'" :class="tab === 'security' ? 'bg-primary text-white' : 'bg-muted text-muted-foreground'" class="py-2">Sécurité</button>
                <button @click="tab = 'danger'" :class="tab === 'danger' ? 'bg-primary text-white' : 'bg-muted text-muted-foreground'" class="py-2">Danger</button>
            </nav>
        </div>

        <div>
            <div class="mt-2 ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2" x-show="tab === 'profile'">
                @include('profile.partials._profile-form')
            </div>
            <div class="mt-2 ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2" x-show="tab === 'password'">
                @include('profile.partials._password-form')
            </div>
            <div class="mt-2 ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2" x-show="tab === 'security'">
                @include('profile.partials._security-form')
            </div>
            <div class="mt-2 ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2" x-show="tab === 'danger'">
                @include('profile.partials._delete-account')
            </div>
        </div>
    </div>
@endsection
