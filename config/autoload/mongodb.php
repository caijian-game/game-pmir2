<?php
declare (strict_types = 1);
return [
    'default' => [
        'host'     => [env('MOGODB_READ_HOST', '127.0.0.1'), env('MOGODB_WRITE_HOST', '127.0.0.1')], //数据库服务器的ip
        'port'     => env('MOGODB_PORT', 27017), //数据库服务器上mongodb服务对应的端口
        'database' => env('MOGODB_DATABASE', 'task_manager'), //数据库名称
        'username' => env('MOGODB_USERNAME', 'forge'),
        'password' => env('MOGODB_PASSWORD', ''),
        'options'  => env('REPLICASETNAME', '') ? [
            'database'   => env('MOGODB_AUTHDB', 'rule_engine'), // 设置mongo 3所需的身份验证数据库
            'replicaSet' => env('REPLICASETNAME', ''),
        ] : [
            'database' => env('MOGODB_AUTHDB', 'rule_engine'), // 设置mongo 3所需的身份验证数据库
        ],
    ],
];
