<?php

return [
    // Général
    'custom_text'          => ['nullable', 'string', 'max:255'],
    'display_banner'       => ['nullable', 'boolean'],
    'about_url'            => ['nullable', 'url'],

    // Homepage — Hero
    'home.hero.title'            => ['nullable', 'string', 'max:255'],
    'home.hero.subtitle'         => ['nullable', 'string'],
    'home.hero.cta.label'        => ['nullable', 'string', 'max:100'],
    'home.hero.cta.url'          => ['nullable', 'url'],
    'home.hero.cta2.label'       => ['nullable', 'string', 'max:100'],
    'home.hero.cta2.url'         => ['nullable', 'url'],

    // Articles
    'articles.heading'     => ['nullable', 'string', 'max:255'],
    'articles.lead'        => ['nullable', 'string', 'max:500'],
    'articles.index_url'   => ['nullable', 'url'],
    'articles.count'       => ['nullable', 'integer', 'min:1', 'max:12'],

    // Couleurs & Branding
    'accent_color'         => ['nullable', 'regex:/^#([A-Fa-f0-9]{3}){1,2}$/'],
];
