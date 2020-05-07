<?php
namespace App\Controller;

/**
 * 
 */
class SendMsg
{
	public function packetData(array $packetInfo)
    {
        $data = getObject('CodePacket')->writePacketData($packetInfo[0], $packetInfo[1]);

        $cmd = pack('s', getObject('CodeMap')->getServerPackCmd($packetInfo[0]));

        $data = $cmd . $data;

        $len = pack('s', strlen($data) + 2);

        return $len . $data;
    }

    //服务主动发送
    public function send(int $fd, array $packetInfo)
    {
        $this->sendPacketData($fd, $this->packetData($packetInfo), $packetInfo);
    }

    public function sendPacketData(int $fd, string $data = '', array $packetInfo = [])
    {
        $log = [
            'len'    => strlen($data),
            'packet' => stringToBytes($data),
        ];

        if ($packetInfo) {
            list($log['cmdName'], $log['res']) = $packetInfo;
        }

        EchoLog(sprintf('Client: [%s] serverSend: %s', $fd, json_encode($log, JSON_UNESCAPED_UNICODE)), 's');

        getObject('Server')->send($fd, $data);
    }

    public function unPacketData(string $packet): array
    {
        $packetBytes = stringToBytes($packet);

        $cmdInfo   = bytesToString(array_slice($packetBytes, 0, 4));
        $paramInfo = bytesToString(array_slice($packetBytes, 4));

        $param = unpack('slen/scmd/', $cmdInfo);

        $param['cmd'] += 1000;

        $param['packet'] = $packetBytes;

        $param['cmdName'] = getObject('CodeMap')->getCmdName($param['cmd']);

        if ($param['cmdName']) {
            $param['res'] = getObject('CodePacket')->readPacketData($param['cmdName'], $paramInfo);
        }

        return $param;
    }
}