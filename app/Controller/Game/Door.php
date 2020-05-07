<?php
namespace App\Controller\Game;

/**
 *
 */
class Door
{
    public $Map;
    public $Index;
    public $State = 0; //0: closed, 1: opening, 2: open, 3: closing
    public $LastTick;
    public $Location;

    public function NewGrid($w, $h)
    {
        return [
            'W'    => $w,
            'H'    => $h,
            'Grid' => [],
        ];
    }

    public function newDoor()
    {
        return [
        	'Map' => [],
        	'Index' => null,
        	'State' => 0,
        	'LastTick' => null,
        	'Location' => [],
        ];
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
}
