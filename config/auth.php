<?php

return [
    'defaults' => [
        'guard' => 'api',
        'passwords' => 'users',
    ],

    'guards' => [
        'api' => [
            'driver' => 'token',
            'provider' => 'users',
            'input_key' => 'access_token',
            'storage_key' => 'access_token',
            'hash' => true
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model'  =>  App\Services\Users\UserModel::class,
        ]
    ],
];
