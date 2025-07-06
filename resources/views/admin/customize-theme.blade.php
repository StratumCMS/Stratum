@extends('admin.layouts.admin')

@section('title', "Personnaliser le thème : $theme->name")

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('themes.index') }}" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-3">
                    <i class="fas fa-arrow-left w-4 h-4 mr-2"></i> Retour aux thèmes
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-foreground">Configuration du thème</h1>
                    <p class="text-sm text-muted-foreground">Personnalisez l'apparence de <strong>{{ $theme->name }}</strong>.</p>
                </div>
            </div>
        </div>

        <div class="rounded-xl border bg-card text-card-foreground shadow-sm p-6">
            <form action="{{ route('themes.customize.save', $theme->slug) }}" method="POST" class="space-y-6">
                @csrf

                {!! Blade::render($configViewContent, ['fields' => $fields, 'values' => $values]) !!}

                <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
                    <div class="flex items-center justify-between pt-6 p-6">
                        <div>
                            <p class="text-sm text-muted-foreground">
                                Les modifications sont sauvegardées automatiquement
                            </p>
                        </div>
                        <div class="flex space-x-2">
                            <button type="submit" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2 hover-glow-purple">
                                <i class="fas fa-save"></i> Enregistrer
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
