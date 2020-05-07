<?php
namespace App\Controller\Game;

/**
 *
 */
class Map
{
    public $DataRange = 20;

    public function NewMap($w, $h, $version)
    {
        return [
            'Width'          => $w,
            'Height'         => $h,
            'Version'        => $version,
            'Info'           => [], //地图想去
            'SafeZoneInfos'  => [], //安全区
            'Respawns'       => [], //怪物刷新
            'cells'          => [], //地图格子
            'doorsMap'       => getObject('Door')->NewGrid($w, $h),
            'doors'          => [],
            'players'        => [],
            'monsters'       => [],
            'npcs'           => [],
            'activedObjects' => [],
            'ActionList'     => [],
        ];
    }

    public function SetCell($m, $Point, $c)
    {
        $m = $this->SetCellXY($m, $Point['X'], $Point['Y'], $c);
        return $m;
    }

    public function SetCellXY($m, $x, $y, $c)
    {
        $m['cells'][$x + $y * $m['Width']] = $c;

        return $m;
    }

    public function AddDoor(&$m, $doorindex, $loc)
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

        $door                   = getObject('Door')->newDoor();
        $door['Map']            = $m;
        $door['Index']          = $doorindex;
        $door['Location']       = $loc;
        $m['doors'][$doorindex] = $door;

        $m['doorsMap'] = getObject('Door')->Set($m, $loc, $door);

        // return $m;
    }

    public function InitAll()
    {
        # code...
    }

    public function addObject($p)
    {
        if (empty($p['ID'])) {
            return false;
        }

        // $c = $this->getCell($p['Map'],$p['CurrentLocation']);
        // if(!empty($c))
        // {

        //     return fmt.Sprintf("pos: %s is not walkable\n", obj.GetPoint()), false
        // }
        // c.AddObject(obj)

        getObject('GameData')->setMapPlayers($p['Map']['Info']['id'], $p);
        // getObject('GameData')->setMapPlayers($p['Map']['Info']['id'], $p);
    }

    public function getCell($m, $point)
    {
        return $this->getCellXY($m, $point['X'], $point['Y']);
    }

    public function getCellXY($m, $x, $y)
    {
        if ($this->inMap($m, $x, $y)) {
            return $this->getCells($m, $x, $y);
        } else {
            return false;
        }
    }

    public function getCells($m, $x, $y)
    {
        $CellAttributeWalk = getObject('Enum')::CellAttributeWalk;

        $cell = [
            'Point'     => [
                'X' => $x,
                'Y' => $y,
            ],
            'Attribute' => $CellAttributeWalk,
            'objects'   => [],
        ];

        return $cell;
    }

    public function inMap($m, $x, $y)
    {
        return $x >= 0 && $x < $m['Width'] && $y >= 0 && $y < $m['Height'];
    }

    public function broadcastP($currentPoint, $msg, $me)
    {
        $players = getObject('GameData')->getMapPlayers($me['Map']['Info']['id']);

        $Point   = getObject('Point');
        $SendMsg = getObject('SendMsg');

        foreach ($players as $k => $player) {
            if ($Point->inRange($currentPoint, $player['CurrentLocation'], $this->DataRange)) {
                if ($player['ID'] != $me['ID']) {
                    $SendMsg->send($player['fd'], $msg);
                }
            }
        }
    }

    public function rangeObject()
    {
        # code...
    }
}
