<?php
namespace App\Controller\Packet;

class BinaryReader
{
    private $packetString = [
        'string' => 'c',
        'int8'   => 'c',
        'int16'  => 's',
        'int32'  => 'l',
        'int64'  => 'q',
        'uint8'  => 'C',
        'uint16' => 'v',
        'uint32' => 'V',
        'uint64' => 'P',
        '[]int8' => 'c',
        'bool'   => 'c',
    ];

    public function unPackString(string $type, string $packet)
    {
        return unpack($this->packetString[$type], $packet)[1];
    }

    public function packString(string $type, string $packet)
    {
        return pack($this->packetString[$type], $packet);
    }

    public function read(array $struct, string $packet): array
    {
        $data = [];

        foreach ($struct as $k => $v) {

            switch ($v) {
                case 'string':
                    if ($packet) {
                        $len      = $this->unPackString($v, $packet);
                        $data[$k] = substr($packet, 1, $len);
                        $packet   = substr($packet, $len + 1);
                    } else {
                        $data[$k] = '';
                    }
                    break;

                case 'int8':
                    if ($packet) {
                        $data[$k] = $this->unPackString($v, $packet);
                        $packet   = substr($packet, 1);
                    } else {
                        $data[$k] = 0;
                    }
                    break;

                case 'int16':
                    if ($packet) {
                        $data[$k] = $this->unPackString($v, $packet);
                        $packet   = substr($packet, 2);
                    } else {
                        $data[$k] = 0;
                    }

                    break;

                case 'int32':
                    if ($packet) {
                        $data[$k] = $this->unPackString($v, $packet);
                        $packet   = substr($packet, 4);
                    } else {
                        $data[$k] = 0;
                    }
                    break;

                case 'int64':
                    if ($packet) {
                        $data[$k] = $this->unPackString($v, $packet);
                        $packet   = substr($packet, 8);
                    } else {
                        $data[$k] = 0;
                    }
                    break;

                case 'uint8':
                    if ($packet) {
                        $data[$k] = $this->unPackString($v, $packet);
                        $packet   = substr($packet, 1);
                    } else {
                        $data[$k] = 0;
                    }
                    break;

                case 'uint16':
                    if ($packet) {
                        $data[$k] = $this->unPackString($v, $packet);
                        $packet   = substr($packet, 2);
                    } else {
                        $data[$k] = 0;
                    }

                    break;

                case 'uint32':
                    if ($packet) {
                        $data[$k] = $this->unPackString($v, $packet);
                        $packet   = substr($packet, 4);
                    } else {
                        $data[$k] = 0;
                    }
                    break;

                case 'uint64':
                    if ($packet) {
                        $data[$k] = $this->unPackString($v, $packet);
                        $packet   = substr($packet, 8);
                    } else {
                        $data[$k] = 0;
                    }
                    break;

                case '[]int8':
                    if ($packet) {
                        $len  = strlen($packet);
                        $info = [];
                        for ($i = 0; $i < $len; $i++) {
                            $info[] = $this->unPackString($v, $packet);
                            $packet = substr($packet, 1);
                        }
                        $data[$k] = $info;
                    } else {
                        $data[$k] = 0;
                    }
                    break;
            }
        }

        return $data;
    }

    public function write(array $struct, array $packet)
    {
        $data = '';
        foreach ($struct as $k => $v) {
            if (isset($packet[$k]) && $packet[$k] !== null) {
                if (is_array($v)) {
                    if (!empty($packet[$k][0]) && is_array($packet[$k][0])) {
                        foreach ($packet[$k] as $k1 => $v1) {
                            $data .= $this->write($v, $v1);
                        }
                    } else {
                        $data .= $this->write($v, $packet[$k]);
                    }
                } else {
                    switch ($v) {
                        case 'string':
                            $len = $this->packString($v, strlen($packet[$k]));
                            $data .= $len . $packet[$k];
                            break;

                        case 'bool':
                            $packet[$k] = $packet[$k] ? 1 : 0;
                            $data .= $this->packString($v, $packet[$k]);
                            break;

                        default:
                            $data .= $this->packString($v, $packet[$k]);
                            break;
                    }
                }
            }
        }

        return $data;
    }

    // public function write(array $struct, array $packet)
    // {
    //     $data = '';

    //     foreach ($packet as $k => $v) {
    //         if (isset($struct[$k]) && $v !== null) {
    //             if (is_array($v) && $struct[$k]) {

    //                 if (!empty($v[0]) && is_array($v[0])) {
    //                     foreach ($v as $k1 => $v1) {
    //                         $data .= $this->write($struct[$k], $v1);
    //                     }
    //                 } else {
    //                     $data .= $this->write($struct[$k], $v);
    //                 }
    //             } else {
    //                 switch ($struct[$k]) {
    //                     case 'string':
    //                         $len = $this->packString($struct[$k], strlen($v));
    //                         $data .= $len . $v;
    //                         break;

    //                     case 'bool':
    //                         $v = $v ? 1 : 0;
    //                         $data .= $this->packString($struct[$k], $v);
    //                         break;

    //                     default:
    //                         $data .= $this->packString($struct[$k], $v);
    //                         break;
    //                 }
    //             }
    //         }
    //     }

    //     return $data;
    // }
}
