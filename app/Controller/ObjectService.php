<?php
namespace App\Controller;

use Hyperf\Utils\ApplicationContext;

/**
 *
 */
class ObjectService
{
    private static $objectRegister = [
        'Server'        => 'Swoole\Server',
        'Redis'         => 'Hyperf\Redis\Redis',
        'MsgRegister'   => 'App\Controller\MsgRegister',
        'Enum'          => 'App\Controller\Common\Enum',
        'CodeMap'       => 'App\Controller\Packet\CodeMap',
        'CodePacket'    => 'App\Controller\Packet\CodePacket',
        'BinaryReader'  => 'App\Controller\Packet\BinaryReader',
        'CommonService' => 'App\Service\Common\CommonService',
        'Character'     => 'App\Controller\Game\Character',
        'GameData'      => 'App\Controller\Game\GameData',
        'Map'           => 'App\Controller\Game\Map',
        'PlayerObject'  => 'App\Controller\Game\PlayerObject',
        'PlayersList'   => 'App\Controller\Game\PlayersList',
        'MapLoader'     => 'App\Controller\Game\MapLoader',
        'Point'         => 'App\Controller\Game\Point',
        'Door'          => 'App\Controller\Game\Door',
        'Cell'          => 'App\Controller\Game\Cell',
        'Bag'           => 'App\Controller\Game\Bag',
        'Checksystem'   => 'App\Libs\Checksystem',
        'Handler'       => 'App\Controller\World\Handler',
        'AuthHandler'   => 'App\Controller\Auth\AuthHandler',
        'Settings'      => 'App\Controller\Game\Settings',
        'SendMsg'       => 'App\Controller\SendMsg',
        'MsgFactory'    => 'App\Controller\Game\MsgFactory',
    ];

    public static function getObject($objectName = null)
    {
        return !empty(self::$objectRegister[$objectName]) ? ApplicationContext::getContainer()->get(self::$objectRegister[$objectName]) : null;
    }
}
