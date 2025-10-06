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
        'local' => 'boletos.local',
        'production' => 'boletos.com',
    ],

    'session' => [
        'domain' => [
            'local' => '.boletos.local',
            'production' => '.boletos.com',
        ],
    ],

    'cookie' => [
        'domain' => [
            'local' => '.boletos.local',
            'production' => '.boletos.com',
        ],
    ],
];
