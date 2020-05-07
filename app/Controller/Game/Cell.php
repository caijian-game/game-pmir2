<?php
namespace App\Controller\Game;

/**
 *
 */
class Cell
{
    public function NewCell($attr)
    {
        return [
            'Point'     => [],
            'Attribute' => $attr,
            'objects'   => [],
        ];
    }

    public function getCell()
    {
        # code...
    }
}
