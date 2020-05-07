<?php

declare (strict_types = 1);

return [
    // 这里的 http 对应默认的 server name，如您需要在其它 server 上使用 Session，需要对应的配置全局中间件
    'http' => [
        \Hyperf\Session\Middleware\SessionMiddleware::class,
    ],
    // 'auth' => [
    // 	App\Middleware\AuthMiddleware::class,
    // ],
    // 'world' => [
    // 	App\Middleware\WorldMiddleware::class,
    // ],
];
