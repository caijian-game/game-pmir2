<?php
declare (strict_types = 1);

namespace App\Controller\Auth;

/**
 *
 */
class AuthHandler
{
    public function keepAlive($fd, $param)
    {
        $data = [
            'Time' => $param['res']['Time'],
        ];

        return ['KEEP_ALIVE', $data];
    }

    /**
     * 0:版本错误, 请升级游戏客户端.\n游戏即将关闭
     * 1:版本验证成功
     */
    public function clientVersion($fd, $param = [])
    {
        $PlayerObject = getObject('PlayerObject');
        co(function () use ($fd, $PlayerObject) {
            $PlayerObject->GameStage = getObject('Enum')::LOGIN;
            $PlayerObject->fd        = $fd;
            $PlayerObject->setPlayer($fd);
        });

        return ['CLIENT_VERSION', ['Result' => 1]];
    }

    /**
     * 0：服务器暂时不允许创建新账号。
     * 1：账号错误。
     * 2：密码错误。
     * 3：邮件错误。
     * 4：用户名错误。
     * 5：密码提示问题错误。
     * 6：密码提示答案错误。
     * 7：这个账号已经存在。
     * 8：你的账号创建成功。
     */
    public function newAccount($fd, $param = [])
    {
        $PlayerObject = getObject('PlayerObject')->getPlayer($fd);
        if (!$PlayerObject || !isset($PlayerObject['GameStage']) || $PlayerObject['GameStage'] != getObject('Enum')::LOGIN) {
            return [];
        }

        $data = [
            'account'   => $param['res']['account'],
            'password'  => $param['res']['Password'],
            'birth_day' => $param['res']['DateTime'],
            'username'  => $param['res']['UserName'],
            'questions' => $param['res']['SecretQuestion'],
            'answers'   => $param['res']['SecretAnswer'],
            'mail'      => $param['res']['EMailAddress'],
        ];

        $where = [
            'whereInfo' => [
                'where' => [
                    ['account', '=', $param['res']['account']],
                ],
            ],
        ];

        $CommonService = getObject('CommonService');
        $res           = $CommonService->getOne('account', $where);

        $Result = 0;
        if ($res['code'] != 2000) {
            $res = $CommonService->save('account', $data);

            if ($res['code'] == 2000) {
                $Result = 8;
            }
        } else {
            $Result = 7;
        }

        return ['NEW_ACCOUNT', ['Result' => $Result]];
    }

    /*
     * 0：服务器暂时不允许修改密码。
     * 1：账号错误。
     * 2：当前密码错误。
     * 3：新密码错误。
     * 4：账号不存在。
     * 5：不正确的账号密码组合。
     * 6：你的密码修改成功。
     */
    public function changePassword($fd, $param = [])
    {
        $PlayerObject = getObject('PlayerObject')->getPlayer($fd);
        if (!$PlayerObject || !isset($PlayerObject['GameStage']) || $PlayerObject['GameStage'] != getObject('Enum')::LOGIN) {
            return [];
        }

        $where = [
            'whereInfo' => [
                'where' => [
                    ['account', '=', $param['res']['account']],
                ],
            ],
        ];

        $CommonService = getObject('CommonService');
        $res           = $CommonService->getOne('account', $where);

        if ($res['code'] == 2000) {
            if ($res['data']['password'] == $param['res']['CurrentPassword']) {
                $data = [
                    'password' => $param['res']['NewPassword'],
                ];

                $res = $CommonService->upField('account', $where, $data);

                if ($res['code'] == 2000) {
                    $Result = 6;
                }
            } else {
                $Result = 2;
            }
        } else {
            $Result = 4;
        }

        return ['CHANGE_PASSWORD', ['Result' => $Result]];
    }

    /*
     * 0：服务器暂时不允许登录。
     * 1：账号错误。
     * 2：密码错误。
     * 3：账号不存在。
     * 4：不正确的账号密码组合。
     */
    public function login($fd, $param = [])
    {
        $PlayerObject = getObject('PlayerObject')->getPlayer($fd);

        $Enum = getObject('Enum');

        if (!$PlayerObject || !isset($PlayerObject['GameStage']) || $PlayerObject['GameStage'] != $Enum::LOGIN) {
            return ['LOGIN', ['Result' => 0]];
        }

        $where = [
            'whereInfo' => [
                'where' => [
                    ['account', '=', $param['res']['account']],
                ],
            ],
        ];

        $CommonService = getObject('CommonService');
        $res           = $CommonService->getOne('account', $where);

        if ($res['code'] == 2000) {
            if ($res['data']['password'] == $param['res']['Password']) {

                $Characters = getObject('Character')->getAccountCharacters($param['res']['account']);

                co(function () use ($fd, $where, $res, $Characters, $PlayerObject, $CommonService, $Enum, $param) {
                    $data = [
                        'login_date' => date('Y-m-d H:i:s'),
                        'login_ip'   => getObject('Server')->getClientInfo($fd)['remote_ip'],
                    ];

                    $CommonService->upField('account', $where, $data);

                    $PlayerObject['AccountID']  = $res['data']['id'];
                    $PlayerObject['account']    = $param['res']['account'];
                    $PlayerObject['GameStage']  = $Enum::SELECT;
                    $PlayerObject['Characters'] = $Characters;

                    getObject('PlayerObject')->setPlayer($fd, $PlayerObject);
                });

                return ['LOGIN_SUCCESS', ['Count' => count($Characters), 'Characters' => $Characters]];
            } else {
                $Result = 2;
            }
        } else {
            $Result = 1;
        }

        return ['LOGIN', ['Result' => $Result]];
    }

    /**
     * 0:服务器暂时不允许创建新角色。
     * 1:角色名不可用。
     * 2:你选择的性别不存在.\n 请联系GM处理。
     * 3:你选择的职业不存在.\n 请联系GM处理。
     * 4:你不能创建超过多少角色
     * 5:这个角色名已存在。
     */
    public function newCharacter($fd, $param = [])
    {
        $PlayerObject = getObject('PlayerObject')->getPlayer($fd);
        $Enum         = getObject('Enum');

        if (!$PlayerObject || !isset($PlayerObject['GameStage']) || $PlayerObject['GameStage'] != $Enum::SELECT) {
            return [];
        }

        if (count($PlayerObject['Characters']) >= $Enum::AccountCharacter) {
            return ['NEW_CHARACTER', ['Result' => 4]];
        }

        $where = [
            'whereInfo' => [
                'where' => [
                    ['name', '=', $param['res']['Name']],
                    ['isdel', '=', 1],
                ],
            ],
        ];

        $CommonService = getObject('CommonService');
        $res           = $CommonService->getOne('character', $where);

        if ($res['code'] != 2000) {
            $data = [
                'name'   => $param['res']['Name'],
                'gender' => $param['res']['Gender'],
                'class'  => $param['res']['Class'],
            ];

            //获取角色基础数据
            $characterBase = getObject('Character')->characterBase($param['res']['Name'], $param['res']['Class'], $param['res']['Gender']);

            $res = $CommonService->save('character', $characterBase);
            if ($res['code'] == 2000) {

                $data = [
                    'account_id'   => $PlayerObject['AccountID'],
                    'character_id' => $res['data']['id'],
                ];

                $res = $CommonService->save('account_character', $data);
                if ($res['code'] == 2000) {
                    $CharInfo = [
                        'Index'      => $data['character_id'],
                        'Name'       => $param['res']['Name'],
                        'Level'      => $characterBase['level'],
                        'Class'      => $param['res']['Class'],
                        'Gender'     => $param['res']['Gender'],
                        'LastAccess' => 0,
                    ];

                    $PlayerObject['Characters'][] = $CharInfo;

                    co(function () use ($fd, $PlayerObject, $CommonService, $data, $Enum) {
                        getObject('PlayerObject')->setPlayer($fd, $PlayerObject);

                        //初始化新手装备
                        $startItems = getObject('GameData')->getStartItems();

                        foreach ($startItems as $k => $v) {
                            $info = [
                                'item_id'         => $v['id'],
                                'current_dura'    => 100,
                                'max_dura'        => 100,
                                'count'           => 1,
                                'ac'              => $v['min_ac'],
                                'mac'             => $v['min_mac'],
                                'dc'              => $v['min_dc'],
                                'mc'              => $v['min_mc'],
                                'sc'              => $v['min_sc'],
                                'accuracy'        => $v['accuracy'],
                                'agility'         => $v['agility'],
                                'hp'              => $v['hp'],
                                'mp'              => $v['mp'],
                                'attack_speed'    => $v['attack_speed'],
                                'luck'            => $v['luck'],
                                'soul_bound_id'   => $data['character_id'],
                                'bools'           => $v['bools'],
                                'strong'          => $v['strong'],
                                'magic_resist'    => $v['magic_resist'],
                                'poison_resist'   => $v['poison_resist'],
                                'health_recovery' => $v['health_recovery'],
                                'mana_recovery'   => 0,
                                'poison_recovery' => $v['poison_recovery'],
                                'critical_rate'   => $v['critical_rate'],
                                'critical_damage' => $v['critical_damage'],
                                'freezing'        => $v['freezing'],
                                'poison_attack'   => $v['poison_attack'],
                            ];

                            $res = $CommonService->save('user_item', $info);

                            if ($res['code'] == 2000) {
                                $info = [
                                    'character_id' => $data['character_id'],
                                    'user_item_id' => $res['data']['id'],
                                    'type'         => $Enum::UserItemTypeInventory,
                                    'index'        => $k,
                                ];

                                $CommonService->save('character_user_item', $info);
                            }
                        }
                    });

                    return ['NEW_CHARACTER_SUCCESS', ['CharInfo' => [$CharInfo]]];
                }
            }
        } else {
            $Result = 5;
        }

        return ['NEW_CHARACTER', ['Result' => $Result]];
    }

    /**
     * 0:服务器暂时不允许删除角色。
     * 1:你选择的角色不存在.\n 请联系GM处理
     */
    public function deleteCharacter($fd, $param = [])
    {
        $PlayerObject = getObject('PlayerObject')->getPlayer($fd);

        if (!$PlayerObject || !isset($PlayerObject['GameStage']) || $PlayerObject['GameStage'] != getObject('Enum')::SELECT) {
            return [];
        }

        $temp = false;

        foreach ($PlayerObject['Characters'] as $k => $v) {
            if ($v['Index'] == $param['res']['CharacterIndex']) {
                $temp = true;
                unset($PlayerObject['Characters'][$k]);
                break;
            }
        }

        if (!$temp) {
            return ['NEW_CHARACTER', ['Result' => $Result]];
        }

        $where = [
            'whereInfo' => [
                'where' => [
                    ['id', '=', $param['res']['CharacterIndex']],
                ],
            ],
        ];

        $data = [
            'isdel' => 2,
        ];

        $res = getObject('CommonService')->upField('character', $where, $data);

        if ($res['code'] == 2000) {

            co(function () use ($fd, $PlayerObject) {
                getObject('PlayerObject')->setPlayer($fd, $PlayerObject);
            });

            return ['DELETE_CHARACTER_SUCCESS', ['CharacterIndex' => $param['res']['CharacterIndex']]];
        }

        return ['NEW_CHARACTER', ['Result' => 0]];
    }

    /**
     * 0:服务器暂时不允许进入游戏。
     * 1:你没有登录。
     * 2:你的角色无法找到。
     * 3:找不到有效的地图/游戏起始点。
     * 4:成功
     */
    public function startGame($fd, $param = [])
    {
        $PlayerObject = getObject('PlayerObject')->getPlayer($fd);
        $Enum         = getObject('Enum');

        if (!$PlayerObject || !isset($PlayerObject['GameStage']) || $PlayerObject['GameStage'] != $Enum::SELECT) {
            return ['START_GAME', ['Result' => 0, 'Resolution' => $Enum::AllowedResolution]];
        }

        if (!$Enum::AllowStartGame) {
            return ['START_GAME', ['Result' => 0, 'Resolution' => $Enum::AllowedResolution]];
        }

        if (!$PlayerObject['AccountID'] || !$PlayerObject['Characters']) {
            return ['START_GAME', ['Result' => 1, 'Resolution' => $Enum::AllowedResolution]];
        }

        $temp = false;

        foreach ($PlayerObject['Characters'] as $k => $v) {
            if ($v['Index'] == $param['res']['CharacterIndex']) {
                $temp = true;
                break;
            }
        }

        if (!$temp) {
            return ['START_GAME', ['Result' => 2, 'Resolution' => $Enum::AllowedResolution]];
        }

        $where = [
            'whereInfo' => [
                'where' => [
                    ['a.account_id', '=', $PlayerObject['AccountID']],
                    ['b.id', '=', $param['res']['CharacterIndex']],
                    ['b.isdel', '=', 1],
                ],
            ],
            'field'     => [
                'b.*',
            ],
            'join'      => [
                ['inner', 'character as b', 'b.id', '=', 'a.character_id'],
            ],
            'pageInfo'  => false,
        ];

        $accountCharacter = getObject('CommonService')->getOne('account_character as a', $where);
        if ($accountCharacter['code'] != 2000) {
            return ['START_GAME', ['Result' => 2, 'Resolution' => $Enum::AllowedResolution]];
        }

        $where = [
            'whereInfo' => [
                'where' => [
                    ['character_id', '=', $param['res']['CharacterIndex']],
                ],
            ],
        ];

        $user_magic = getObject('CommonService')->getList('user_magic', $where);

        getObject('SendMsg')->send($fd, ['SET_CONCENTRATION', ['ObjectID' => $PlayerObject['AccountID'], 'Enabled' => 0, 'Interrupted' => 0]]);
        
        getObject('SendMsg')->send($fd, ['START_GAME', ['Result' => 4, 'Resolution' => $Enum::AllowedResolution]]);

        getObject('PlayerObject')->updatePlayerInfo($PlayerObject, $accountCharacter['data'], $user_magic['list']);

        EchoLog(sprintf('玩家登陆: 账户ID(%s) 角色名(%s)', $PlayerObject['AccountID'], $PlayerObject['Name']), 'i');

        $PlayerObject['Map'] = getObject('GameData')->getMap($accountCharacter['data']['current_map_id']);

        getObject('PlayersList')->addPlayersList($PlayerObject);

        getObject('Map')->addObject($PlayerObject);

        getObject('PlayerObject')->StartGame($PlayerObject);
    }
}
