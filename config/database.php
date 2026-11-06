<?php

return [
    'default' => env('DB_CONNECTION', 'mariadb'),

    'connections' => [
        'mariadb' => [
            'driver'          => 'mariadb',
            'host'            => env('DB_HOST', '127.0.0.1'),
            'port'            => env('DB_PORT', '3306'),
            'database'        => env('DB_DATABASE', 'chikumi'),
            'username'        => env('DB_USERNAME', 'root'),
            'password'        => env('DB_PASSWORD', ''),
            'charset'         => 'utf8mb4',
            'collation'       => 'utf8mb4_unicode_ci',
            'timezone'        => '+07:00',
            'prefix'          => '',
            'prefix_indexes'  => true,
            'strict'          => true,
            'engine'          => 'InnoDB',
        ],
    ],
];
