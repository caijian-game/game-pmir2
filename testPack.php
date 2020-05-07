#!/usr/bin/env php
<?php

ini_set('display_errors', 'on');
ini_set('display_startup_errors', 'on');

error_reporting(E_ALL);
date_default_timezone_set('Asia/Shanghai');

!defined('BASE_PATH') && define('BASE_PATH', __DIR__ . '/');
!defined('SWOOLE_HOOK_FLAGS') && define('SWOOLE_HOOK_FLAGS', SWOOLE_HOOK_ALL);

require BASE_PATH . '/vendor/autoload.php';

//测试重写env
function env($conf, $default)
{
    $config = [
        'DEBUG' => true,
    ];
    return $config[$conf] ?? $default;
}
use App\Controller\Packet\CodeMap;
use Hyperf\Di\Annotation\Inject;

/**
 *
 */
class Client
{
    /**
     * @Inject
     * @var CodeMap
     */
    protected $CodeMap;

    protected $size = '';

    public function __construct()
    {
        $this->CodeMap = new CodeMap;
        $this->run();
    }

    public function run()
    {

        // var_dump(String2Hex(bytesToString([24, 0, 0, 0, 16, 0, 0, 0, 170, 124, 11, 209, 41, 241, 81, 142, 137, 41, 214, 160, 138, 169, 152, 239])));

        $client = new Swoole\Client(SWOOLE_SOCK_TCP);
        if (!$client->connect('127.0.0.1', 7000, -1)) {
            EchoLog("connect failed. Error: {$client->errCode}");
        }

        //CLIENT_VERSION
        //{"len":24,"cmd":1000,"packet":[24,0,0,0,16,0,0,0,170,124,11,209,41,241,81,142,137,41,214,160,138,169,152,239],"cmdName":"CLIENT_VERSION","res":{"VersionHash":[16,0,0,0,-86,124,11,-47,41,-15,81,-114,-119,41,-42,-96,-118,-87,-104,-17]}}
        $client->send(bytesToString([24,0,0,0,16,0,0,0,61,222,49,90,63,210,209,213,57,41,168,163,227,98,188,121]));

        while ($packet = $client->recv()) {
            if ($packet) {

                $strlen = strlen($packet);
                $data = [];
                $i    = 0;
                while ($i < $strlen) {
                    $size   = unpack('s', substr($packet, $i))[1];
                    $data[] = substr($packet, $i, $size);
                    $i += $size;
                }

                foreach ($data as $k => $v) {
                    $cmd = $this->unPacketData($v);
                    switch ($cmd) {
                        case 'CONNECTED':
                            // //NEW_ACCOUNT
                            // //{"len":30,"packet":[30,0,3,0,6,102,97,110,102,97,110,6,102,97,110,102,97,110,0,0,0,0,0,0,0,0,0,0,0,0],"cmdName":"NEW_ACCOUNT","res":{"AccountID":"fanfan","Password":"fanfan","DateTime":0,"UserName":"","SecretQuestion":"","SecretAnswer":"","EMailAddress":""}}
                            // $client->send(bytesToString([30, 0, 3, 0, 6, 102, 97, 110, 102, 97, 110, 6, 102, 97, 110, 102, 97, 110, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]));

                            //LOGIN
                            //{"len":18,"cmd":1005,"packet":[18,0,5,0,6,102,97,110,102,97,110,6,102,97,110,102,97,110],"cmdName":"LOGIN","res":{"AccountID":"fanfan","Password":"fanfan"}}
                            $client->send(bytesToString([18,0,5,0,6,102,97,110,102,97,110,6,102,97,110,102,97,110]));
                            break;

                        case 'LOGIN_SUCCESS':
                            // //NEW_CHARACTER
                            // // {"len":13,"cmd":1006,"packet":[13,0,6,0,6,97,115,100,97,115,100,0,0],"cmdName":"NEW_CHARACTER","res":{"Name":"asdasd","Gender":0,"Class":0}}
                            // $client->send(bytesToString([13, 0, 6, 0, 6, 97, 115, 100, 97, 115, 100, 0, 0]));
                            // $this->unPacketData($client->recv());

                            //START_game
                            // {"len":8,"cmd":1008,"packet":[8,0,8,0,14,0,0,0],"cmdName":"START_GAME","res":{"CharacterIndex":1}}
                            $client->send(bytesToString([8,0,8,0,2,0,0,0]));
                            break;
                        default:
                            # code...
                            break;
                    }
                }
            }
        }
    }

    public function unPacketData(string $packet)
    {
    	sleep(1);

        $packetBytes = stringToBytes($packet);

        $cmdInfo   = bytesToString(array_slice($packetBytes, 0, 4));
        $paramInfo = bytesToString(array_slice($packetBytes, 4));

        $param = unpack('slen/scmd/', $cmdInfo);

        $param['cmd'] += 2000;

        $param['packet'] = $packetBytes;

        $param['cmdName'] = $this->CodeMap->getCmdName($param['cmd']);

        EchoLog(sprintf('收到服务端信息: [%s] ', json_encode($param, JSON_UNESCAPED_UNICODE)), 'i');

        return $param['cmdName'];
    }
}

new Client();
