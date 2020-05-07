<?php

declare (strict_types = 1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf-cloud/hyperf/blob/master/LICENSE
 */

return [
    'default' => [
        'driver'    => env('DB_DRIVER', 'mysql'),
        'host'      => env('DB_HOST', 'localhost'),

        'read'      => [
            'host' => [env('DB_READ_HOST', '127.0.0.1')],
        ],
        'write'     => [
            'host' => [env('DB_WRITE_HOST', '127.0.0.1')],
        ],

        'database'  => env('DB_DATABASE', 'hyperf'),
        'username'  => env('DB_USERNAME', 'root'),
        'password'  => env('DB_PASSWORD', ''),
        'charset'   => env('DB_CHARSET', 'utf8'),
        'collation' => env('DB_COLLATION', 'utf8_unicode_ci'),
        'prefix'    => env('DB_PREFIX', ''),

        #连接池配置
        'pool'      => [
            'min_connections' => 1,
            'max_connections' => 10,
            'connect_timeout' => 10.0,
            'wait_timeout'    => 3.0,
            'heartbeat'       => -1,
            'max_idle_time'   => (float) env('DB_MAX_IDLE_TIME', 60),
        ],
        'cache'     => [
            'handler'         => Hyperf\ModelCache\Handler\RedisHandler::class,
            'cache_key'       => 'mc:%s:m:%s:%s:%s',
            'prefix'          => 'default',
            'ttl'             => 3600 * 24,
            'empty_model_ttl' => 600,
            'load_script'     => true,
        ],
        'commands'  => [
            'db:model' => [
                'path'        => 'app/Model',
                'force_casts' => true,
                'inheritance' => 'Model',
            ],
        ],
    ],
];
