<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => env('DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => [

        'sqlite_testing' => [
            'driver' => 'sqlite',
            'database' => env('DB_DATABASE', database_path('testing.sqlite')),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
        ],

        'mysql' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer body of commands than a typical key-value system
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

    'redis' => [

        'client' => env('REDIS_CLIENT', 'predis'),

        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'predis'),
        ],

        'csp' => [
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', 6379),
            'database' => env('REDIS_CSP_DB', 4),
        ],

        'queue' => [
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', 6379),
            'database' => env('REDIS_QUEUE_DB', 3),
        ],

        'sessions' => [
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', 6379),
            'database' => env('REDIS_SESSIONS_DB', 2),
        ],

        'cache' => [
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', 6379),
            'database' => env('REDIS_CACHE_DB', 1),
        ],

        'indexes' => [
            'ipa' => [
                'name' => 'IPAIndex',
                'host' => env('REDIS_IPA_INDEX_HOST', '127.0.0.1'),
                'password' => env('REDIS_IPA_INDEX_PASSWORD'),
                'port' => env('REDIS_IPA_INDEX_PORT', 6379),
                'database' => 0,
            ],
            'websites' => [
                'name' => 'WebsitesIndex',
                'host' => env('REDIS_HOST', '127.0.0.1'),
                'password' => env('REDIS_PASSWORD'),
                'port' => env('REDIS_PORT', 6379),
                'database' => 0,
            ],
            'users' => [
                'name' => 'UsersIndex',
                'host' => env('REDIS_HOST', '127.0.0.1'),
                'password' => env('REDIS_PASSWORD'),
                'port' => env('REDIS_PORT', 6379),
                'database' => 0,
            ],
        ],

        'queue-sentinel' => array_merge(
            explode(',', env('REDIS_SENTINELS', '')),
            [
                'options' => [
                    'replication' => 'sentinel',
                    'service' => env('REDIS_SENTINEL_SET', 'mymaster'),
                    'parameters' => [
                        'password' => env('REDIS_PASSWORD'),
                        'database' => env('REDIS_QUEUE_DB', 3),
                        'timeout' => 0.5,
                    ],
                ],
            ]
        ),

        'sessions-sentinel' => array_merge(
            explode(',', env('REDIS_SENTINELS', '')),
            [
                'options' => [
                    'replication' => 'sentinel',
                    'service' => env('REDIS_SENTINEL_SET', 'mymaster'),
                    'parameters' => [
                        'password' => env('REDIS_PASSWORD'),
                        'database' => env('REDIS_SESSIONS_DB', 2),
                        'timeout' => 0.5,
                    ],
                ],
            ]
        ),

        'cache-sentinel' => array_merge(
            explode(',', env('REDIS_SENTINELS', '')),
            [
                'options' => [
                    'replication' => 'sentinel',
                    'service' => env('REDIS_SENTINEL_SET', 'mymaster'),
                    'parameters' => [
                        'password' => env('REDIS_PASSWORD'),
                        'database' => env('REDIS_CACHE_DB', 1),
                        'timeout' => 0.5,
                    ],
                ],
            ]
        ),

        'ipa-sentinel' => array_merge(
            explode(',', env('REDIS_IPA_INDEX_SENTINELS', '')),
            [
                'options' => [
                    'replication' => 'sentinel',
                    'service' => env('REDIS_IPA_INDEX_SENTINEL_SET', 'mymaster'),
                    'parameters' => [
                        'password' => env('REDIS_IPA_INDEX_PASSWORD'),
                        'database' => 0,
                        'timeout' => 0.5,
                    ],
                ],
            ]
        ),

        'websites-sentinel' => array_merge(
            explode(',', env('REDIS_SENTINELS', '')),
            [
                'options' => [
                    'replication' => 'sentinel',
                    'service' => env('REDIS_SENTINEL_SET', 'mymaster'),
                    'parameters' => [
                        'password' => env('REDIS_PASSWORD'),
                        'database' => 0,
                        'timeout' => 0.5,
                    ],
                ],
            ]
        ),

        'users-sentinel' => array_merge(
            explode(',', env('REDIS_SENTINELS', '')),
            [
                'options' => [
                    'replication' => 'sentinel',
                    'service' => env('REDIS_SENTINEL_SET', 'mymaster'),
                    'parameters' => [
                        'password' => env('REDIS_PASSWORD'),
                        'database' => 0,
                        'timeout' => 0.5,
                    ],
                ],
            ]
        ),
    ],

];
