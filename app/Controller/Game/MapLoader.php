<?php
namespace App\Controller\Game;

/**
 *
 */
class MapLoader
{
    public function loadMap($filepath)
    {
        EchoLog(sprintf('加载地图 %s', $filepath), 'rf');

        if (!$contents = $this->getLine($filepath, 1)) {
            EchoLog(sprintf('打开文件失败 :%s', $filepath), 'e');
            return false;
        }

        $fileBytes = stringToBytes($contents); //头文件

        $version = $this->findType($fileBytes);

        $fileBytes = $this->getFileContent($filepath); //全部内容

        $Cell               = getObject('Cell');
        $Enum               = getObject('Enum');
        $this->LowWallCell  = $Cell->NewCell($Enum::CellAttributeLowWall);
        $this->HighWallCell = $Cell->NewCell($Enum::CellAttributeHighWall);

        $data = null;
        switch ($version) {
            case 0:
                $data = $this->GetMapV0($fileBytes);
                break;

            case 1:
                $data = $this->GetMapV1($fileBytes);
                break;

            case 2:
                $data = $this->GetMapV2($fileBytes);
                break;

            case 3:
                $data = $this->GetMapV3($fileBytes);
                break;

            case 4:
                $data = $this->GetMapV4($fileBytes);
                break;

            case 5:
                $data = $this->GetMapV5($fileBytes);
                break;

            case 6:
                $data = $this->GetMapV5($fileBytes);
                break;

            case 7:
                $data = $this->GetMapV5($fileBytes);
                break;

            case 100:
                $data = $this->GetMapV5($fileBytes);
                break;
            default:
                EchoLog(sprintf('地图版本不支持! 版本:%s %s', $version, $filepath), 'e');
                break;
        }

        return $data;
    }

    public function getFileContent($filepath, $length = 1024)
    {
        if (!file_exists($filepath)) {
            return false;
        }

        //快
        // $handle = fopen($filepath, "rb");
        // $fsize  = filesize($filepath);
        // return fread($handle, $fsize);

        //最快
        return file_get_contents($filepath);

        //慢的一批
        // $fp = fopen($filepath, 'r');
        // $content = '';
        // while (!feof($fp)) {
        //     $content .= stream_get_line($fp, $length);
        // }
        // fclose($fp);
        // return $content;

        //慢的一批
        // $fp = fopen($filepath, 'r');
        // $content = '';
        // while (!feof($fp)) {
        //     $content .= fgets($fp, $length);
        // }
        // fclose($fp);
        // return $content;
    }

    public function getLine($filepath, $line, $length = 4096)
    {
        if (!file_exists($filepath)) {
            return false;
        }

        $returnTxt = null; // 初始化返回
        $i         = 1; // 行数
        $handle    = fopen($filepath, 'r');
        if ($handle) {
            while (!feof($handle)) {
                $buffer = fgets($handle, $length);
                if ($line == $i) {
                    $returnTxt = $buffer;
                    break;
                }

                $i++;
            }
            fclose($handle);
        }
        return $returnTxt;
    }

    public function findType($input)
    {
        //c# custom map format
        if ($input[2] == 0x43 && $input[3] == 0x23) {
            return 100;
        }
        //wemade mir3 maps have no title they just start with blank bytes
        if ($input[0] == 0) {
            return 5;
        }
        //shanda mir3 maps start with title: (C) SNDA, MIR3.
        if ($input[0] == 0x0F && $input[5] == 0x53 && $input[14] == 0x33) {
            return 6;
        }

        //wemades antihack map (laby maps) title start with: Mir2 AntiHack
        if ($input[0] == 0x15 && $input[4] == 0x32 && $input[6] == 0x41 && $input[19] == 0x31) {
            return 4;
        }

        //wemades 2010 map format i guess title starts with: Map 2010 Ver 1.0
        if ($input[0] == 0x10 && $input[2] == 0x61 && $input[7] == 0x31 && $input[14] == 0x31) {
            return 1;
        }

        //shanda's 2012 format and one of shandas(wemades) older formats share same header info, only difference is the filesize
        if ($input[4] == 0x0F && $input[18] == 0x0D && $input[19] == 0x0A) {
            $W = intval($input[0] + ($input[1] << 8));
            $H = intval($input[2] + ($input[3] << 8));
            if (count($input) > (52 + $W * $H * 14)) {
                return 3;
            }
            return 2;
        }

        //3/4 heroes map format (myth/lifcos i guess)
        if ($input[0] == 0x0D && $input[1] == 0x4C && $input[7] == 0x20 && $input[11] == 0x6D) {
            return 7;
        }

        return 0;
    }

    public function GetMapV0($fileBytes)
    {

    }

    public function GetMapV1($fileBytes)
    {
        $offset = 21;
        $w      = uInt16(substr($fileBytes, $offset));

        $offset += 2;
        $xor = uInt16(substr($fileBytes, $offset));

        $offset += 2;
        $h = uInt16(substr($fileBytes, $offset));

        $width  = intval($w ^ $xor);
        $height = intval($h ^ $xor);

        $m = getObject('Map')->NewMap($width, $height, 1);

        $offset = 54;

        $CellAttributeWalk = getObject('Enum')::CellAttributeWalk;

        for ($x = 0; $x < $width; $x++) {
            // $star_memory = memory_get_usage();
            // EchoLog(sprintf(PHP_EOL . '开始内存：%s', ($star_memory / 1024 / 1024)));

            for ($y = 0; $y < $height; $y++) {

                $cell = null;
                // if ((uInt32(substr($fileBytes, $offset)) ^ 0xAA38AA38 & 0x20000000) != 0) {
                //     $cell = $this->HighWallCell;
                // }

                $offset += 6;
                // if ((uInt16(substr($fileBytes, $offset)) ^ $xor & 0x8000) != 0) {
                //     $cell = $this->LowWallCell;
                // }

                if ($cell == null) {
                    $cell = [
                        'Point'     => [],
                        'Attribute' => $CellAttributeWalk,
                        'objects'   => [],
                    ];
                }

                $point = [
                    'X' => $x,
                    'Y' => $y,
                ];

                if ($cell['Attribute'] == $CellAttributeWalk) {
                    $cell['Point'] = $point;
                    // $m['cells'][$point['X'] + $point['Y'] * $m['Width']] = $cell;
                }

                $offset += 2;
                $b = stringToBytes(substr($fileBytes, $offset, 1))[0];
                if ($b > 0) {
                    $this->AddDoor($m, $b, $point);
                }

                $offset += 5;

                $offset += 1 + 1;
            }

            // $end_memory = memory_get_usage();
            // EchoLog(sprintf(PHP_EOL . '运行后内存：%s', ($end_memory / 1024 / 1024)));
            // EchoLog(sprintf(PHP_EOL . '差值：%s',(($end_memory - $star_memory) / 1024 / 1024)));
        }

        return $m;
    }

    public function AddDoor($m, $doorindex, $loc)
    {
        if (!empty($m['doors'])) {
            foreach ($m['doors'] as $d) {
                if ($d['Index'] == $doorindex) {
                    return $d;
                }
            }
        } else {
            $m['doors'] = [];
        }

        $door = [
            'Map'      => $m,
            'Index'    => $doorindex,
            'State'    => 0,
            'LastTick' => null,
            'Location' => $loc,
        ];

        $m['doors'][$doorindex] = $door;

        $m['doorsMap'] = $this->Set($m, $loc, $door);

        return $m;
    }

    public function In($m, $loc)
    {
        return $loc['X'] < $m['doorsMap']['W'] && $loc['Y'] < $m['doorsMap']['H'];
    }

    public function Set($m, $loc, $d)
    {

        if ($this->In($m, $loc)) {

            if (empty($m['doorsMap']['Grid'][$loc['X']])) {
                $m['doorsMap']['Grid'][$loc['X']] = [];
            }

            $m['doorsMap']['Grid'][$loc['X']][$loc['Y']] = $d;

            return $m['doorsMap'];
        }
    }

    public function GetMapV2($fileBytes)
    {

    }

    public function GetMapV3($fileBytes)
    {

    }

    public function GetMapV4($fileBytes)
    {

    }

    public function GetMapV5($fileBytes)
    {

    }

    public function GetMapV6($fileBytes)
    {

    }

    public function GetMapV7($fileBytes)
    {

    }

    public function GetMapV100($fileBytes)
    {

    }
}
