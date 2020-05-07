<?php
namespace App\Controller\Game;

/**
 *
 */
class Character
{
    public function characterBase($name, $class, $gender)
    {
        $startPoint = getObject('GameData')->randomStartPoint();
        $Enum       = getObject('Enum');

        $characterInfo = [
            'name'               => $name,
            'level'              => 1,
            'class'              => $class,
            'gender'             => $gender,
            'hair'               => 1,

            'current_map_id'     => $startPoint['map_id'],
            'current_location_x' => $startPoint['location_x'],
            'current_location_y' => $startPoint['location_y'],
            'bind_map_id'        => $startPoint['map_id'],
            'bind_location_x'    => $startPoint['location_x'],
            'bind_location_y'    => $startPoint['location_y'],

            'direction'          => $Enum::MirDirectionDown,
            'hp'                 => 15,
            'mp'                 => 17,
            'experience'         => 0,
            'attack_mode'        => $Enum::AttackModeAll,
            'pet_mode'           => $Enum::PetModeBoth,
        ];

        return $characterInfo;
    }

    public function getAccountCharacters($account)
    {
        $where = [
            'whereInfo' => [
                'where' => [
                    ['b.account', '=', $account],
                    ['c.isdel', '=', 1],
                ],
            ],
            'field'     => [
                'a.character_id',
                'b.id',
                'b.login_date',
                'c.name',
                'c.level',
                'c.class',
                'c.gender',
            ],
            'join'      => [
                ['left', 'account as b', 'b.id', '=', 'a.account_id'],
                ['inner', 'character as c', 'c.id', '=', 'a.character_id'],
            ],
            'pageInfo'  => false,
        ];

        $res = getObject('CommonService')->getList('account_character as a', $where);
        $data = [];
        if ($res['list']) {
            foreach ($res['list'] as $k => $v) {
                $info = [
                    'Index'      => $v['character_id'],
                    'Name'       => $v['name'],
                    'Level'      => $v['level'],
                    'Class'      => $v['class'],
                    'Gender'     => $v['gender'],
                    'LastAccess' => $v['login_date'],
                ];

                $data[] = $info;
            }
        }

        return $data;
    }

    //保存玩家数据
    public function saveData($p)
    {
        
    }
}
