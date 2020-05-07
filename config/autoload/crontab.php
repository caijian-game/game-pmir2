<?php
use Hyperf\Crontab\Crontab;

return [
    // 是否开启定时任务
    'enable' => true,

    // 通过配置文件定义的定时任务
    'crontab' => [

        // (new Crontab())->setName('UserVIP')->setRule('0 0 * * *')->setCallback([App\Task\UserVIP::class, 'execute'])->setMemo('用户VIP过期定时器'),

    ],
];