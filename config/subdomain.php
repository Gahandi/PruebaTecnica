<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Subdomain Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for subdomain handling in the application.
    |
    */

    'domain' => [
        'local' => config('app.url'),
        'production' => config('app.url'),
    ],

    'session' => [
        'domain' => [
            'local' => '.' . parse_url(config('app.url'), PHP_URL_HOST),
            'production' => '.' . parse_url(config('app.url'), PHP_URL_HOST),
        ],
    ],

    'cookie' => [
        'domain' => [
            'local' => '.' . parse_url(config('app.url'), PHP_URL_HOST),
            'production' => '.' . parse_url(config('app.url'), PHP_URL_HOST),
        ],
    ],
];
