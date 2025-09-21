@php
    $columns = max(1, min(4, (int) (theme_config('layout.footer.columns') ?? 3)));
    $items   = get_navigation_items();
    $year    = now()->year;
@endphp

<footer class="mt-16 border-t border-slate-200 bg-slate-50 dark:border-slate-800 dark:bg-slate-900/60">
    <div class="container max-w-7xl mx-auto px-4">
        <div class="grid gap-10 py-12" style="grid-template-columns: repeat({{ $columns }}, minmax(0,1fr));">
            <div>
                <a href="{{ url('/') }}" class="inline-flex items-center gap-3">
                    @if(theme_config('brand.logo') ?? site_logo())
                        <img src="{{ theme_config('brand.logo') ?? site_logo() }}" alt="{{ site_name() }}" class="h-8 w-auto" loading="lazy" decoding="async">
                    @endif
                    <span class="text-lg font-semibold text-slate-900 dark:text-white">{{ site_name() }}</span>
                </a>
                @if(theme_config('seo.meta_description_default'))
                    <p class="mt-3 max-w-prose text-sm text-slate-600 dark:text-slate-400">
                        {{ theme_config('seo.meta_description_default') }}
                    </p>
                @endif
            </div>

            <div>
                <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-900 dark:text-slate-100">Navigation</h2>
                <ul class="mt-4 grid gap-2 text-sm">
                    @if($items && count($items))
                        @foreach($items as $item)
                            <li>
                                <a href="{{ $item->url ?? $item->value ?? '#' }}" class="text-slate-700 hover:text-slate-900 hover:underline dark:text-slate-300 dark:hover:text-white">
                                    {{ $item->label ?? $item->name }}
                                </a>
                            </li>
                        @endforeach
                    @else
                        <li><a href="{{ url('/') }}" class="hover:underline">Accueil</a></li>
                        <li><a href="{{ url('/blog') }}" class="hover:underline">Blog</a></li>
                        <li><a href="{{ url('/contact') }}" class="hover:underline">Contact</a></li>
                    @endif
                </ul>
            </div>

            @if($columns >= 3)
                <div>
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-900 dark:text-slate-100">Newsletter</h2>
                    <form class="mt-4 flex flex-col sm:flex-row gap-2" action="{{ url('/newsletter') }}" method="post">
                        @csrf
                        <label for="nl-email" class="sr-only">Votre e-mail</label>
                        <input id="nl-email" name="email" type="email" required
                               class="w-full rounded-md border-slate-300 focus:border-indigo-600 focus:ring-indigo-600 dark:border-slate-700 dark:bg-slate-950 px-3 py-2 text-sm"
                               placeholder="vous@exemple.com">
                        <button class="inline-flex items-center justify-center gap-2 rounded-md px-4 py-2 text-sm font-medium bg-indigo-600 text-white hover:bg-indigo-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-600" type="submit">
                            S’inscrire
                        </button>
                    </form>
                    <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Pas de spam. Désinscription en un clic.</p>
                </div>
            @endif

            @if($columns >= 4)
                <div>
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-900 dark:text-slate-100">Suivez-nous</h2>
                    <div class="mt-4 flex flex-wrap gap-2">
                        @php
                            $socials = [
                              'Twitter' => theme_config('brand.social.twitter') ?? null,
                              'GitHub'  => theme_config('brand.social.github') ?? null,
                              'LinkedIn'=> theme_config('brand.social.linkedin') ?? null,
                            ];
                        @endphp
                        @forelse($socials as $label => $url)
                            @if($url)
                                <a class="inline-flex items-center justify-center gap-2 rounded-md px-3 py-1.5 text-sm border border-slate-200 hover:bg-slate-100 dark:border-slate-700 dark:hover:bg-slate-800" href="{{ $url }}" target="_blank" rel="noopener noreferrer">{{ $label }}</a>
                            @endif
                        @empty
                            <p class="text-sm text-slate-600 dark:text-slate-400">Ajoutez vos liens sociaux via la config du thème.</p>
                        @endforelse
                    </div>
                </div>
            @endif
        </div>

        <div class="border-t border-slate-200 py-6 text-sm text-slate-600 dark:border-slate-800 dark:text-slate-400 flex flex-col sm:flex-row items-center justify-between gap-2">
            <p>&copy; {{ $year }} {{ site_name() }} — Tous droits réservés.</p>
            <div class="flex items-center gap-4">
                <a href="{{ url('/mentions-legales') }}" class="hover:underline">Mentions légales</a>
                <a href="{{ url('/contact') }}" class="hover:underline">Contact</a>
            </div>
        </div>
    </div>
</footer>
