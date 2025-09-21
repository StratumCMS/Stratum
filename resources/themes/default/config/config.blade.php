@php
    $fields = $fields ?? [];
    $values = $values ?? [];

    $known = [
        'home.hero.title'       => ['label' => 'Hero · Titre', 'type' => 'text', 'default' => 'Bienvenue sur {{site_name}}', 'group' => 'HomePage'],
        'home.hero.subtitle'    => ['label' => 'Hero · Sous-titre', 'type' => 'textarea', 'rows' => 3, 'default' => 'Un thème Tailwind propre, mobile-first et SEO-ready pour StratumCMS.', 'group' => 'HomePage'],
        'home.hero.cta.label'   => ['label' => 'CTA #1 · Libellé', 'type' => 'text', 'default' => 'Explorer les articles', 'group' => 'HomePage'],
        'home.hero.cta.url'     => ['label' => 'CTA #1 · URL', 'type' => 'url',  'default' => '/blog', 'group' => 'HomePage', 'placeholder' => '/blog'],
        'home.hero.cta2.label'  => ['label' => 'CTA #2 · Libellé', 'type' => 'text', 'default' => 'À propos', 'group' => 'HomePage'],
        'home.hero.cta2.url'    => ['label' => 'CTA #2 · URL', 'type' => 'url',  'default' => '#', 'group' => 'HomePage', 'placeholder' => '#'],

        'articles.heading'   => ['label' => 'Articles · Titre de section', 'type' => 'text', 'default' => 'Derniers articles', 'group' => 'Articles'],
        'articles.lead'      => ['label' => 'Articles · Texte d’intro',   'type' => 'text', 'default' => 'Restez à jour avec nos dernières publications...', 'group' => 'Articles'],
        'articles.index_url' => ['label' => 'Articles · URL “Voir tous”', 'type' => 'url',  'default' => '/articles', 'group' => 'Articles', 'placeholder' => '/articles'],
        'articles.count'     => ['label' => 'Articles · Nombre à afficher', 'type' => 'number', 'default' => 3, 'min' => 1, 'max' => 12, 'step' => 1, 'group' => 'Articles', 'help' => 'Nombre d’articles listés sur la home.'],

        'about.title'         => ['label' => 'À propos · Titre', 'type' => 'text', 'default' => 'Notre mission', 'group' => 'À propos'],
        'about.p1'            => ['label' => 'À propos · Paragraphe 1', 'type' => 'textarea', 'rows' => 3, 'default' => "Nous partageons notre passion pour le développement web...", 'group' => 'À propos'],
        'about.p2'            => ['label' => 'À propos · Paragraphe 2', 'type' => 'textarea', 'rows' => 3, 'default' => "Notre équipe d'experts vous accompagne...", 'group' => 'À propos'],
        'about.button.label'  => ['label' => 'À propos · Bouton · Libellé', 'type' => 'text', 'default' => 'En savoir plus', 'group' => 'À propos'],
        'about.button.url'    => ['label' => 'À propos · Bouton · URL', 'type' => 'url', 'default' => '#', 'group' => 'À propos', 'placeholder' => '#'],
        'about.image'         => ['label' => 'À propos · Image (URL)', 'type' => 'image', 'default' => (function_exists('theme_asset') ? theme_asset('images/about.png') : '/images/about.png'), 'group' => 'À propos', 'help' => 'Collez une URL d’image. Un aperçu s’affiche.'],

        'about_url'           => ['label' => 'Alias historique · URL “À propos”', 'type' => 'url', 'default' => '#', 'group' => 'À propos', 'help' => 'Encore lu si “À propos · Bouton · URL” est vide.'],
    ];
    foreach ($known as $k => $def) if (!array_key_exists($k, $fields)) $fields[$k] = $def;

    $getVal = function (string $key, array $field) use ($values) {
        $def = $field['default'] ?? null;
        $ov  = old($key, null); if (!is_null($ov)) return $ov;
        $tree = data_get($values, $key); if (!is_null($tree)) return $tree;
        if (array_key_exists($key, $values)) return $values[$key];
        return $def;
    };

    $explain = [
        'custom_text'    => 'Non utilisé par la page d’accueil actuelle.',
        'display_banner' => 'Non utilisé par la page d’accueil actuelle.',
        'accent_color'   => 'Non utilisé par la page d’accueil, sauf si ton CSS y est branché.',
    ];

    $guessGroup = function (string $key): string {
        $root = str_contains($key, '.') ? explode('.', $key, 2)[0] : 'autres';
        return match ($root) {
            'home' => 'HomePage',
            'articles' => 'Articles',
            'about' => 'À propos',
            default => 'Autres',
        };
    };

    $groupOrder = ['HomePage', 'Articles', 'À propos', 'Autres'];
    $grouped = [];
    foreach ($fields as $name => $field) {
        $g = $field['group'] ?? $guessGroup($name);
        $grouped[$g] ??= [];
        $grouped[$g][$name] = $field;
    }
    uksort($grouped, function ($a, $b) use ($groupOrder) {
        $pa = array_search($a, $groupOrder, true);
        $pb = array_search($b, $groupOrder, true);
        if ($pa === false && $pb === false) return strnatcasecmp($a, $b);
        if ($pa === false) return 1;
        if ($pb === false) return -1;
        return $pa <=> $pb;
    });

    $renderField = function (string $name, array $field) use ($getVal, $explain) {
        $label       = $field['label'] ?? ucfirst(str_replace(['.', '_'], ' ', $name));
        $type        = $field['type']  ?? 'text';
        $placeholder = $field['placeholder'] ?? null;
        $help        = $field['help'] ?? ($explain[$name] ?? null);
        $options     = $field['options'] ?? [];
        $min         = $field['min'] ?? null;
        $max         = $field['max'] ?? null;
        $step        = $field['step'] ?? null;

        $value   = $getVal($name, $field);
        $checked = in_array($type, ['checkbox','switch'], true) && (bool) $value;

        $inputBase = 'w-full rounded-md border bg-background px-3 py-2';
        $labelCls  = 'block text-sm font-medium mb-1';
        $helpCls   = 'text-xs text-muted-foreground mt-1';

        ob_start();
@endphp
<div class="space-y-1">
    <label class="{{ $labelCls }}">{{ $label }}</label>

    @switch($type)
        @case('textarea')
            <textarea name="{{ $name }}" rows="{{ $field['rows'] ?? 3 }}" placeholder="{{ $placeholder }}" class="{{ $inputBase }}">{{ old($name, $value) }}</textarea>
            @break

        @case('color')
            <input type="color" name="{{ $name }}" value="{{ old($name, $value) }}" class="h-10 w-20 rounded-md border bg-background px-1 py-1">
            @break

        @case('checkbox')
        @case('switch')
            <label class="inline-flex items-center gap-2">
                <input type="hidden" name="{{ $name }}" value="0">
                <input type="checkbox" name="{{ $name }}" value="1" {{ $checked ? 'checked' : '' }} class="h-4 w-4 rounded border">
                <span class="text-sm text-muted-foreground">{{ $field['inlineLabel'] ?? 'Activer' }}</span>
            </label>
            @break

        @case('number')
            <input type="number" name="{{ $name }}"
                   value="{{ old($name, $value) }}"
                   @if(!is_null($min)) min="{{ $min }}" @endif
                   @if(!is_null($max)) max="{{ $max }}" @endif
                   @if(!is_null($step)) step="{{ $step }}" @endif
                   placeholder="{{ $placeholder }}" class="{{ $inputBase }}">
            @break

        @case('select')
            <select name="{{ $name }}" class="{{ $inputBase }}">
                @foreach($options as $optValue => $optLabel)
                    <option value="{{ $optValue }}" {{ (string) old($name, $value) === (string) $optValue ? 'selected' : '' }}>{{ $optLabel }}</option>
                @endforeach
            </select>
            @break

        @case('url')
            <input type="url" name="{{ $name }}" value="{{ old($name, $value) }}" placeholder="{{ $placeholder ?? 'https://...' }}" class="{{ $inputBase }}">
            @break

        @case('email')
            <input type="email" name="{{ $name }}" value="{{ old($name, $value) }}" placeholder="{{ $placeholder }}" class="{{ $inputBase }}">
            @break

        @case('image')
            <div class="space-y-2">
                <input type="url" name="{{ $name }}" value="{{ old($name, $value) }}" placeholder="https://exemple.com/image.jpg" class="{{ $inputBase }}">
                @php $src = old($name, $value); @endphp
                @if($src)
                    <div class="rounded-md border bg-muted/30 p-2">
                        <img src="{{ $src }}" alt="Aperçu" class="max-h-40 w-auto rounded-md object-contain mx-auto" loading="lazy" decoding="async">
                    </div>
                @endif
            </div>
            @break

        @default
            <input type="text" name="{{ $name }}" value="{{ old($name, $value) }}" placeholder="{{ $placeholder }}" class="{{ $inputBase }}">
    @endswitch

    @if($help)
        <p class="{{ $helpCls }}">{{ $help }}</p>
    @endif
</div>
@php
    return trim(ob_get_clean());
};

$hasAny = collect($grouped)->flatten(2)->isNotEmpty();
@endphp

@if(!$hasAny)
    <div class="rounded-lg border bg-muted p-6 text-sm text-muted-foreground">
        Aucun champ de configuration n’a été fourni pour ce thème.
    </div>
@else
    <div class="mb-3 md:mb-4">
        <div class="flex items-center justify-between">
            <div class="space-y-1">
                <h2 class="text-base md:text-lg font-semibold">Personnalisation de la page d’accueil</h2>
                <p class="text-xs md:text-sm text-muted-foreground">Modifiez le Hero, les Articles et la section À propos.</p>
            </div>
        </div>
        <div class="mt-3 h-1 w-full rounded bg-muted">
            <div id="progress-bar" class="h-1 rounded bg-primary" style="width: 0%;"></div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,260px),1fr] gap-4 md:gap-6">
        <aside id="steps" class="rounded-lg border bg-card p-2 md:p-3 lg:sticky lg:top-4 h-max">
            <nav class="flex gap-2 overflow-x-auto lg:block lg:space-y-2" aria-label="Étapes de configuration">
                @foreach(array_keys($grouped) as $idx => $groupName)
                    <button type="button"
                            class="step-link shrink-0 px-3 py-2 rounded-md border lg:border-0 hover:bg-accent hover:text-accent-foreground flex items-center gap-2"
                            data-goto-step="{{ $idx }}">
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full border text-xs step-index">{{ $idx + 1 }}</span>
                        <span class="whitespace-nowrap">{{ $groupName }}</span>
                    </button>
                @endforeach
            </nav>
        </aside>

        <section class="rounded-lg border bg-card p-4 md:p-6">
            @foreach($grouped as $groupName => $groupFields)
                <div class="config-step space-y-6" data-step-index="{{ $loop->index }}" style="display: none;">
                    <div>
                        <h3 class="text-lg md:text-xl font-semibold">{{ $groupName }}</h3>
                        <p class="text-xs md:text-sm text-muted-foreground">
                            @switch($groupName)
                                @case('HomePage') Titre, sous-titre et boutons du hero. @break
                                @case('Articles') Titre/intro, URL d’index et quantité. @break
                                @case('À propos') Textes, bouton et image de la section. @break
                                @default Champs additionnels fournis par votre thème.
                            @endswitch
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                        @foreach($groupFields as $name => $field)
                            {!! $renderField($name, $field) !!}
                        @endforeach
                    </div>

                    <div class="mt-4 flex items-center justify-between lg:hidden">
                        <button type="button" class="cfg-prev-inline inline-flex items-center justify-center rounded-md border px-3 py-2 text-sm disabled:opacity-50">
                            ← Précédent
                        </button>
                        <button type="button" class="cfg-next-inline inline-flex items-center justify-center rounded-md border px-3 py-2 text-sm disabled:opacity-50">
                            Suivant →
                        </button>
                    </div>
                </div>
            @endforeach

            <div class="mt-6 flex items-center justify-between border-t pt-4">
                <div class="flex items-center gap-2">
                    <button type="button" id="cfg-prev-bottom"
                            class="inline-flex items-center justify-center rounded-md border px-3 py-2 text-sm disabled:opacity-50">
                        ← Précédent
                    </button>
                    <button type="button" id="cfg-next-bottom"
                            class="inline-flex items-center justify-center rounded-md border px-3 py-2 text-sm disabled:opacity-50">
                        Suivant →
                    </button>
                </div>
            </div>
        </section>
    </div>

    <script>
        (function () {
            const steps = Array.from(document.querySelectorAll('.config-step'));
            const links = Array.from(document.querySelectorAll('.step-link'));
            const prevTop = document.getElementById('cfg-prev');
            const nextTop = document.getElementById('cfg-next');
            const prevBottom = document.getElementById('cfg-prev-bottom');
            const nextBottom = document.getElementById('cfg-next-bottom');
            const progress = document.getElementById('progress-bar');

            let index = 0;

            function setActive(i) {
                if (steps.length === 0) return;
                index = Math.max(0, Math.min(i, steps.length - 1));

                steps.forEach((s, k) => s.style.display = (k === index ? '' : 'none'));

                links.forEach((l, k) => {
                    const badge = l.querySelector('.step-index');
                    if (k === index) {
                        l.classList.add('bg-primary/10','text-primary','font-semibold');
                        if (badge) badge.classList.add('bg-primary','text-primary-foreground','border-transparent');
                    } else {
                        l.classList.remove('bg-primary/10','text-primary','font-semibold');
                        if (badge) badge.classList.remove('bg-primary','text-primary-foreground','border-transparent');
                    }
                });

                const atFirst = index === 0;
                const atLast  = index === steps.length - 1;
                [prevTop, prevBottom].forEach(b => b && (b.disabled = atFirst));
                [nextTop, nextBottom].forEach(b => b && (b.disabled = atLast));

                document.querySelectorAll('.cfg-prev-inline').forEach(btn => btn.disabled = atFirst);
                document.querySelectorAll('.cfg-next-inline').forEach(btn => btn.disabled = atLast);

                if (progress) progress.style.width = (((index + 1) / steps.length) * 100) + '%';

                steps[index].scrollIntoView({ behavior: 'smooth', block: 'start' });
            }

            links.forEach((btn, k) => btn.addEventListener('click', () => setActive(k)));
            [prevTop, prevBottom].forEach(b => b && b.addEventListener('click', () => setActive(index - 1)));
            [nextTop, nextBottom].forEach(b => b && b.addEventListener('click', () => setActive(index + 1)));

            document.addEventListener('click', (e) => {
                if (e.target && e.target.classList.contains('cfg-prev-inline')) setActive(index - 1);
                if (e.target && e.target.classList.contains('cfg-next-inline')) setActive(index + 1);
            });

            setActive(0);
        })();
    </script>
@endif
