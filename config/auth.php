<?php

return [
    'defaults' => [
        'guard' => 'api',
    ],

    'guards' => [
        'api' => [
            'driver' => 'jwt',
            'provider' => 'jwt-provider',
        ],
    ],

    'providers' => [
        'jwt-provider' => [
            'driver' => 'jwt-provider'
        ]
    ]
];
