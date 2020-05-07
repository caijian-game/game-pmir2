<?php
declare (strict_types = 1);

namespace App\Controller\World;

/**
 *
 */
class Handler
{
    public function walk($fd, $param)
    {
        $PlayerObject = getObject('PlayerObject')->getPlayer($fd);

        $Point = getObject('Point')->NextPoint($PlayerObject['CurrentLocation'], $param['res']['Direction'], 1);

        $data = [
            'Location'  => $Point,
            'Direction' => $param['res']['Direction'],
        ];

        $PlayerObject['CurrentLocation'] = $Point;

        getObject('PlayerObject')->setPlayer($fd, $PlayerObject);

        return ['USER_LOCATION', $data];
    }

    public function run($fd, $param)
    {
        $steps = 2;

        $PlayerObject = getObject('PlayerObject')->getPlayer($fd);

        for ($i = 1; $i <= $steps; $i++) {

            $Point = getObject('Point')->NextPoint($PlayerObject['CurrentLocation'], $param['res']['Direction'], $i);
            $data  = [
                'Location'  => $Point,
                'Direction' => $param['res']['Direction'],
            ];
        }

        $PlayerObject['CurrentLocation'] = $Point;

        getObject('SendMsg')->send($fd, ['USER_LOCATION', $data]);

        getObject('PlayerObject')->setPlayer($fd, $PlayerObject);
    }

    public function turn($fd, $param)
    {
        $p = getObject('PlayerObject')->getPlayer($fd);

        $data = [
            'Location'  => $p['CurrentLocation'],
            'Direction' => $param['res']['Direction'],
        ];

        getObject('SendMsg')->send($fd, ['USER_LOCATION', $data]);

        // getObject('PlayerObject')->setPlayer($fd, $p);

        // p.Broadcast(ServerMessage{}.ObjectTurn(p))

    }

    public function logOut($fd, $param)
    {
        $p    = getObject('PlayerObject')->getPlayer($fd);
        $Enum = getObject('Enum');

        if (!$p || !isset($p['GameStage']) || $p['GameStage'] != $Enum::GAME) {
            return false;
        }

        $Characters = getObject('Character')->getAccountCharacters($p['account']);

        co(function () use ($fd, $p, $Enum) {

            $p['GameStage'] = $Enum::SELECT;

            getObject('PlayerObject')->setPlayer($fd, $p);

            //从列表里删除玩家 TODO   

            //保存玩家属性 TODO   
        });

        getObject('PlayerObject')->stopGame($p);

        return ['LOG_OUT_SUCCESS', ['Count' => count($Characters),'Characters' => $Characters]];
    }

}
