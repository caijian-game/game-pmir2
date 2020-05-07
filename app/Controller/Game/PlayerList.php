<?php
namespace App\Controller\Game;

/**
 *
 */
class PlayerList
{
    protected $key = 'PlayersList';

    public function getPlayersList()
    {
        return json_decode(getObject('Redis')->get($this->key), true);
    }

    public function addPlayersList($PlayerObject)
    {
        $PlayersList   = $this->getPlayersList() ?: [];
        $PlayersList[] = $PlayerObject;

        getObject('Redis')->set($this->key, json_encode($PlayersList));
    }
}
