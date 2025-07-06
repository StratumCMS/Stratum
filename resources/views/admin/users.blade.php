@extends('admin.layouts.admin')

@section('title', 'Utilisateurs')

@section('content')
    <div class="space-y-6">

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            @foreach ($roles as $role)
                <div class="rounded-lg border bg-card text-card-foreground shadow-sm hover-lift hover-glow-purple transition-all cursor-pointer">
                    <div class="flex flex-row items-center justify-between p-6 pb-2">
                        <div class="text-sm font-medium text-muted-foreground">{{ $role['name'] }}</div>
                        <div class="{{ $role['color'] }} text-white w-8 h-8 rounded-lg flex items-center justify-center">
                            <i class="fas fa-{{ $role['icon'] }} w-4 h-4"></i>
                        </div>
                    </div>
                    <div class="px-6 pb-6 pt-0">
                        <div class="text-2xl font-bold">{{ $role['count'] }}</div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="flex flex-col sm:flex-row gap-4 justify-between items-start sm:items-center">
            <div class="relative w-full sm:w-96">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground w-4 h-4"></i>
                <input
                    type="text"
                    placeholder="Rechercher par nom ou email..."
                    class="pl-10 pr-4 py-2 bg-background border border-border rounded-lg focus:ring-2 focus:ring-primary/20 focus:outline-none focus:border-primary w-full"
                />
            </div>
            <div class="flex gap-2">
                <button class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 h-9 px-3 {{ is_null($selectedRole ?? null) ? 'bg-primary text-primary-foreground hover:bg-primary/90' : 'border border-input bg-background hover:bg-accent hover:text-accent-foreground' }}">Tous</button>
                @foreach ($roles as $role)
                    <button class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 h-9 px-3 {{ ($selectedRole ?? null) === $role['name'] ? 'bg-primary text-primary-foreground hover:bg-primary/90' : 'border border-input bg-background hover:bg-accent hover:text-accent-foreground' }}">{{ $role['name'] }}</button>
                @endforeach
            </div>
        </div>

        <div class="rounded-lg border bg-card text-card-foreground shadow-sm overflow-hidden">
            <div class="p-6 border-b border-border">
                <h2 class="text-lg font-semibold">Liste des utilisateurs ({{ count($users) }})</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                    <tr class="border-b border-border bg-muted/50">
                        <th class="text-left p-4 font-medium">Utilisateur</th>
                        <th class="text-left p-4 font-medium">Rôle</th>
                        <th class="text-left p-4 font-medium">Statut</th>
                        <th class="text-left p-4 font-medium">Dernière connexion</th>
                        <th class="text-left p-4 font-medium">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($users as $user)
                        <tr class="border-b border-border hover:bg-muted/30 transition-colors">
                            <td class="p-4">
                                <div class="flex items-center space-x-3">
                                    <div class="h-10 w-10 flex items-center justify-center text-white text-sm font-bold uppercase">
                                        <img src="{{ $user->avatar_url ?? 'https://placehold.co/32' }}"
                                             alt="Avatar" class="w-8 h-8 rounded-full">
                                    </div>
                                    <div>
                                        <div class="font-medium">{{ $user->name }}</div>
                                        <div class="text-sm text-muted-foreground flex items-center">
                                            <i class="fas fa-envelope mr-1 w-3 h-3"></i> {{ $user->email }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4">
                                <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full text-white {{ $user->role_color }}">
                                    {{ $user->display_role }}
                                </span>
                            </td>
                            <td class="p-4">
                                <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full text-white {{ $user->status === 'Actif' ? 'bg-success text-white' : 'bg-muted' }}">
                                    {{ $user->status }}
                                </span>
                            </td>
                            <td class="p-4 text-sm text-muted-foreground">{{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Jamais connecté' }}</td>
                            <td class="p-4">
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-3">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Confirmer la suppression ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-3 text-destructive hover:text-destructive">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection
