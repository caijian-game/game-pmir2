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

use Hyperf\Server\Server;
use Hyperf\Server\SwooleEvent;

return [
    'mode'      => SWOOLE_PROCESS,
    'servers'   => [
        [
            'name'      => 'auth',
            'type'      => Server::SERVER_BASE,
            'host'      => '0.0.0.0',
            'port'      => (int) env('AUTH_PORT', 7000),
            'sock_type' => SWOOLE_SOCK_TCP,
            'callbacks' => [
                SwooleEvent::ON_START    => [App\Controller\Server::class, 'onStart'],
                SwooleEvent::ON_CONNECT  => [App\Controller\Server::class, 'onConnect'],
                SwooleEvent::ON_RECEIVE  => [App\Controller\Server::class, 'onReceive'],
                SwooleEvent::ON_CLOSE    => [App\Controller\Server::class, 'onClose'],
                SwooleEvent::ON_SHUTDOWN => [App\Controller\Server::class, 'onShutdown'],

            ],
            'settings'  => [
                'heartbeat_check_interval' => (int) env('HEARTABEAT_TIME', 60), //每隔多少秒检测一次，单位秒，Swoole会轮询所有TCP连接，将超过心跳时间的连接关闭掉
                'open_eof_check'           => false, //打开EOF检测
                // 'package_eof'              => "", //设置EOF
                'open_eof_split'           => false, //是否分包
            ],
        ],
        // [
        //     'name'      => 'world',
        //     'type'      => Server::SERVER_BASE,
        //     'host'      => '0.0.0.0',
        //     'port'      => (int) env('WORLD_PORT', 7200),
        //     'sock_type' => SWOOLE_SOCK_TCP,
        //     'callbacks' => [
        //         SwooleEvent::ON_CONNECT => [App\Controller\Server::class, 'onConnect'],
        //         SwooleEvent::ON_RECEIVE => [App\Controller\Server::class, 'onReceive'],
        //         SwooleEvent::ON_CLOSE   => [App\Controller\Server::class, 'onClose'],
        //     ],
        //     'settings'  => [
        //         'heartbeat_check_interval' => (int) env('HEARTABEAT_TIME', 60), //每隔多少秒检测一次，单位秒，Swoole会轮询所有TCP连接，将超过心跳时间的连接关闭掉
        //         'open_eof_check'           => false, //打开EOF检测
        //         // 'package_eof'              => "!", //设置EOF
        //         'open_eof_split'           => false, //是否分包
        //     ],
        // ],
    ],
    'settings'  => [
        'enable_coroutine'      => true, // 开启内置协程
        'worker_num'            => env('WORKER_NUM', function_exists('swoole_cpu_num') ? swoole_cpu_num() * 2 : 8), // 设置启动的 Worker 进程数
        'pid_file'              => BASE_PATH . '/runtime/hyperf.pid', // master 进程的 PID
        'open_tcp_nodelay'      => true, // TCP 连接发送数据时会关闭 Nagle 合并算法，立即发往客户端连接
        'max_coroutine'         => 100000, // 设置当前工作进程最大协程数量
        'open_http2_protocol'   => false, // 启用 HTTP2 协议解析
        'max_request'           => 100000, // 设置 worker 进程的最大任务数
        'socket_buffer_size'    => 10 * 1024 * 1024, // 配置客户端连接的缓存区长度
        'package_max_length'    => 10 * 1024 * 1024, //包大小
        'daemonize'             => env('SERVER_DAEMONIZE', 1), //守护进程

        // Task Worker 数量，根据您的服务器配置而配置适当的数量
        'task_worker_num'       => env('TASK_WORKER_NUM', function_exists('swoole_cpu_num') ? swoole_cpu_num() * 2 : 4),

        // 因为 `Task` 主要处理无法协程化的方法，所以这里推荐设为 `false`，避免协程下出现数据混淆的情况
        'task_enable_coroutine' => false,

    ],
    'callbacks' => [
        SwooleEvent::ON_BEFORE_START => [Hyperf\Framework\Bootstrap\ServerStartCallback::class, 'beforeStart'],
        SwooleEvent::ON_WORKER_START => [Hyperf\Framework\Bootstrap\WorkerStartCallback::class, 'onWorkerStart'],
        SwooleEvent::ON_PIPE_MESSAGE => [Hyperf\Framework\Bootstrap\PipeMessageCallback::class, 'onPipeMessage'],

        // Task callbacks
        SwooleEvent::ON_TASK         => [Hyperf\Framework\Bootstrap\TaskCallback::class, 'onTask'],
        SwooleEvent::ON_FINISH       => [Hyperf\Framework\Bootstrap\FinishCallback::class, 'onFinish'],
    ],
];
