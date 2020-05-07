<?php
namespace App\Controller\Game;

/**
 *
 */
class Point
{

    public function NextPoint($Point, $direction, $step)
    {
        $x    = $Point['X'];
        $y    = $Point['Y'];
        $Enum = getObject('Enum');

        switch ($direction) {
            case $Enum::MirDirectionUp:
                $y = $y - $step;
                break;

            case $Enum::MirDirectionUpRight:
                $x = $x + $step;
                $y = $y - $step;
                break;

            case $Enum::MirDirectionRight:
                $x = $x + $step;
                break;

            case $Enum::MirDirectionDownRight:
                $x = $x + $step;
                $y = $y + $step;
                break;

            case $Enum::MirDirectionDown:
                $y = $y + $step;
                break;

            case $Enum::MirDirectionDownLeft:
                $x = $x - $step;
                $y = $y + $step;
                break;

            case $Enum::MirDirectionLeft:
                $x = $x - $step;
                break;

            case $Enum::MirDirectionUpLeft:
                $x = $x - $step;
                $y = $y - $step;
                break;
        }

        return [
            'X' => $x,
            'Y' => $y,
        ];
    }

    public function NewPoint($x, $y)
    {
        return ['X' => $x, 'Y' => $y];
    }

    public function inRange($currentPoint, $point, $dataRange)
    {
        return AbsInt($currentPoint['X'] - intval($point['X'])) <= $dataRange && AbsInt(intval($currentPoint['Y']) - intval($point['Y'])) <= $dataRange;
    }
}
