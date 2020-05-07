<?php
declare (strict_types = 1);

namespace App\Controller;

use App\Controller\AbstractController;

/**
 *
 */
class Server extends AbstractController
{

    public function onStart()
    {
        //18 00 00 00 10 00 00 00 3d de 31 5a 3f d2 d1 d5 39 29 a8 a3 e3 62 bc 79
        //12 00 05 00 06 66 61 6e 66 61 6e 06 66 61 6e 66 61 6e
        //08 00 08 00 01 00 00 00
        //05 00 0b 00 02
        // var_dump(BigInteger(bytesToString([24,0,0,0,16,0,0,0,61,222,49,90,63,210,209,213,57,41,168,163,227,98,188,121]), 256)->toHex());
        // var_dump(BigInteger(bytesToString([18,0,5,0,6,102,97,110,102,97,110,6,102,97,110,102,97,110]), 256)->toHex());
        // var_dump(BigInteger(bytesToString([8,0,8,0,1,0,0,0]), 256)->toHex());
        // var_dump(BigInteger(bytesToString([5,0,11,0,2]), 256)->toHex());

        getObject('Checksystem')->check();

        $str = "

        MMMMMMMM       MMMM    MMMM     MMM     MMMMMMMMM        MMMMM
        MMM   MMM      MMMM   MMMMM     MMM     MMM   MMMM      MMM MMM
        MMM    MMM     MMMM   MMMMM     MMM     MMM    MMM     MMM   MMM
        MMM    MMM     MMMMM  MMMMM     MMM     MMM    MMM           MMM
        MMM    MMM     MMMMM MMMMMM     MMM     MMM    MMM          MMMM
        MMM   MMM      MMMMM MMMMMM     MMM     MMM   MMMM          MMM
        MMMMMMMM       MMMMMMMM MMM     MMM     MMMMMMMM           MMMM
        MMM            MMMMMMMM MMM     MMM     MMM  MMMM         MMMM
        MMM            MMM MMMM MMM     MMM     MMM   MMM        MMMM
        MMM            MMM MMM  MMM     MMM     MMM    MMM      MMM
        MMM            MMM      MMM     MMM     MMM    MMM     MMMM
        MMM            MMM      MMM     MMM     MMM    MMMM    MMMMMMMMM
            ";
        EchoLog($str, null, true);
        EchoLog('Server version ' . env('VERSION', '1.0.1'), null, true);
        EchoLog('author by.fan <fan3750060@163.com>', null, true);

        if (\Hyperf\Utils\Coroutine::inCoroutine()) {
            getObject('GameData')->loadGameData();
        } else {
            co(function () {
                getObject('GameData')->loadGameData();
            });
        }
    }

    public function onConnect($server, $fd, $reactorId)
    {
        EchoLog(sprintf('Client: [%s] connect IP: [%s]', $fd, $server->getClientInfo($fd)['remote_ip']), 'i');

        $this->setClientInfo($fd, $server->getClientInfo($fd));

        getObject('SendMsg')->sendPacketData($fd, bytesToString([4, 0, 0, 0]));
    }

    public function onReceive($server, $fd, $reactorId, $data)
    {
        //处理黏包(这里只需要将黏在一起的同一批tcp消息进行处理,底层已经实现单次tcp的收发)
        $strlen    = strlen($data);
        $dataArray = [];
        $i         = 0;
        while ($i < $strlen) {
            $size        = unpack('s', substr($data, $i))[1];
            $dataArray[] = substr($data, $i, $size);
            $i += $size;
        }

        co(function () use ($dataArray, $fd) {
            foreach ($dataArray as $k => $v) {
                co(function () use ($fd, $v) {
                    $data = getObject('SendMsg')->unPacketData($v);

                    EchoLog(sprintf('Client: [%s] serverReceive: %s', $fd, json_encode($data, JSON_UNESCAPED_UNICODE)), 'i');

                    if ($data['cmdName']) {
                        $this->handler($data['cmdName'], $fd, $data);
                    }
                });
            }
        });
    }

    public function onClose($server, $fd, $reactorId)
    {
        EchoLog(sprintf('Client: [%s] close IP: [%s]', $fd, $server->getClientInfo($fd)['remote_ip']), 'w');
        $this->delClientInfo($fd);
    }

    public function onShutdown()
    {
        EchoLog("onShutdown");
    }

    public function getClientInfo(int $fd = null): array
    {
        $key = getClientId($fd);
        return json_decode(getObject('Redis')->get($key), true);
    }

    public function setClientInfo(int $fd = null, array $data = [])
    {
        $key = getClientId($fd);

        getObject('Redis')->set($key, json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    public function delClientInfo(int $fd = null)
    {
        $key   = getClientId($fd);
        $Redis = getObject('Redis');
        $Redis->del($key);
        $Redis->del('SessionIDPlayerMap_' . $key);
    }
}
