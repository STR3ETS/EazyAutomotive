<?php

return [
    'marktplaats' => [
        'name' => 'Marktplaats',
        'description' => 'De grootste advertentiesite van Nederland',
        'icon' => 'fa-store',
        'color' => 'orange',
        'fields' => [
            ['name' => 'api_key', 'label' => 'API Key', 'type' => 'password', 'required' => true],
            ['name' => 'seller_id', 'label' => 'Verkoper ID', 'type' => 'text', 'required' => true],
        ],
        'help' => [
            'title' => 'Hoe verbind ik Marktplaats?',
            'steps' => [
                'Ga naar <a href="https://www.marktplaats.nl/account/mijn-marktplaats" target="_blank" class="text-eazy underline">Mijn Marktplaats</a> en log in met je account.',
                'Klik op <strong>Instellingen</strong> &rarr; <strong>API-toegang</strong> (of neem contact op met Marktplaats Pro voor zakelijke API-toegang).',
                'Kopieer je <strong>API Key</strong> en <strong>Verkoper ID</strong> en plak ze hieronder.',
            ],
            'note' => 'Je hebt een Marktplaats Pro (zakelijk) account nodig voor API-toegang. Heb je dit nog niet? Neem contact op met Marktplaats via pro.marktplaats.nl.',
        ],
    ],
    'facebook' => [
        'name' => 'Facebook Marketplace',
        'description' => 'Bereik miljoenen kopers via Facebook',
        'icon' => 'fa-brands fa-facebook',
        'color' => 'blue',
        'fields' => [
            ['name' => 'page_id', 'label' => 'Pagina ID', 'type' => 'text', 'required' => true],
            ['name' => 'access_token', 'label' => 'Access Token', 'type' => 'password', 'required' => true],
        ],
        'help' => [
            'title' => 'Hoe verbind ik Facebook Marketplace?',
            'steps' => [
                'Je hebt een <strong>Facebook Business-pagina</strong> nodig voor je autobedrijf. Maak er een aan via <a href="https://www.facebook.com/pages/create" target="_blank" class="text-eazy underline">facebook.com/pages/create</a> als je die nog niet hebt.',
                'Ga naar <a href="https://business.facebook.com/settings" target="_blank" class="text-eazy underline">Meta Business Suite</a> &rarr; <strong>Instellingen</strong> &rarr; <strong>Bedrijfsinformatie</strong> om je <strong>Pagina ID</strong> te vinden.',
                'Ga naar <a href="https://developers.facebook.com/tools/explorer/" target="_blank" class="text-eazy underline">Graph API Explorer</a>, selecteer je app en genereer een <strong>Access Token</strong> met de rechten <code class="bg-gray-100 px-1 py-0.5 rounded text-[11px]">pages_manage_posts</code> en <code class="bg-gray-100 px-1 py-0.5 rounded text-[11px]">pages_read_engagement</code>.',
            ],
            'note' => 'Zorg dat je pagina gepubliceerd is en dat je beheerdersrechten hebt. De access token verloopt periodiek — verleng deze via Meta Business Suite.',
        ],
    ],
    'autotrack' => [
        'name' => 'AutoTrack',
        'description' => 'Nederlands platform voor auto\'s kopen en verkopen',
        'icon' => 'fa-car-side',
        'color' => 'red',
        'fields' => [
            ['name' => 'api_key', 'label' => 'API Key', 'type' => 'password', 'required' => true],
            ['name' => 'dealer_id', 'label' => 'Dealer ID', 'type' => 'text', 'required' => true],
        ],
        'help' => [
            'title' => 'Hoe verbind ik AutoTrack?',
            'steps' => [
                'Log in op je <a href="https://www.autotrack.nl/dealer" target="_blank" class="text-eazy underline">AutoTrack Dealer Dashboard</a>.',
                'Ga naar <strong>Instellingen</strong> &rarr; <strong>API & Koppelingen</strong>.',
                'Kopieer je <strong>API Key</strong> en <strong>Dealer ID</strong> en plak ze hieronder.',
            ],
            'note' => 'Je hebt een AutoTrack dealer-abonnement nodig. Neem contact op met AutoTrack voor API-toegang als je dit nog niet hebt ingeschakeld.',
        ],
    ],
    'instagram' => [
        'name' => 'Instagram',
        'description' => 'Deel je auto\'s als posts op Instagram',
        'icon' => 'fa-brands fa-instagram',
        'color' => 'pink',
        'fields' => [
            ['name' => 'business_account_id', 'label' => 'Business Account ID', 'type' => 'text', 'required' => true],
            ['name' => 'access_token', 'label' => 'Access Token', 'type' => 'password', 'required' => true],
        ],
        'help' => [
            'title' => 'Hoe verbind ik Instagram?',
            'steps' => [
                'Schakel je Instagram-account om naar een <strong>Zakelijk account</strong> via Instellingen &rarr; Account &rarr; Overschakelen naar zakelijk account.',
                'Koppel je Instagram aan een <strong>Facebook-pagina</strong> via Instagram Instellingen &rarr; Account &rarr; Gedeelde accounts.',
                'Ga naar <a href="https://developers.facebook.com/tools/explorer/" target="_blank" class="text-eazy underline">Graph API Explorer</a> en genereer een token met <code class="bg-gray-100 px-1 py-0.5 rounded text-[11px]">instagram_basic</code> en <code class="bg-gray-100 px-1 py-0.5 rounded text-[11px]">instagram_content_publish</code> rechten.',
                'Je <strong>Business Account ID</strong> vind je via de Graph API: <code class="bg-gray-100 px-1 py-0.5 rounded text-[11px]">GET /me/accounts</code> &rarr; kies je pagina &rarr; <code class="bg-gray-100 px-1 py-0.5 rounded text-[11px]">GET /{page-id}?fields=instagram_business_account</code>.',
            ],
            'note' => 'Instagram posting via de API werkt alleen met een zakelijk account dat gekoppeld is aan een Facebook-pagina.',
        ],
    ],
];
