<?php
namespace App\Controller\Game;

/**
 *
 */
class PlayerObject
{
    public $fd;
    public $AccountID; //账户id
    public $GameStage; //游戏状态
    public $MapObject; //地图信息
    public $HP; //血量
    public $MP; //魔法值
    public $Level; //等级
    public $Experience; //经验值
    public $MaxExperience; //最大经验值
    public $Gold; //金币
    public $GuildName; //工会名称
    public $GuildRankName; //公会等级名称
    public $Class; //职业
    public $Gender; //性别
    public $Hair; //发型
    public $Light;
    public $Inventory;
    public $Equipment;
    public $QuestInventory; //任务清单
    public $Storage;
    public $Trade;
    public $Refine;
    public $LooksArmour; //衣服外观
    public $LooksWings; //翅膀外观
    public $LooksWeapon; //武器外观
    public $LooksWeaponEffect; //武器特效
    public $SendItemInfo;
    public $CurrentBagWeight;
    public $MaxHP; //最大血值
    public $MaxMP; //最大魔法值
    public $MinAC; // 物理防御力
    public $MaxAC;
    public $MinMAC; // 魔法防御力
    public $MaxMAC;
    public $MinDC; // 攻击力
    public $MaxDC;
    public $MinMC; // 魔法力
    public $MaxMC;
    public $MinSC; // 道术力
    public $MaxSC;
    public $Accuracy; //精准度
    public $Agility; //敏捷
    public $CriticalRate;
    public $CriticalDamage;
    public $MaxBagWeight; //Other Stats;
    public $MaxWearWeight;
    public $MaxHandWeight;
    public $ASpeed;
    public $Luck;
    public $LifeOnHit;
    public $HpDrainRate;
    public $Reflect; // TODO
    public $MagicResist;
    public $PoisonResist;
    public $HealthRecovery;
    public $SpellRecovery;
    public $PoisonRecovery;
    public $Holy;
    public $Freezing;
    public $PoisonAttack;
    public $ExpRateOffset;
    public $ItemDropRateOffset;
    public $MineRate;
    public $GemRate;
    public $FishRate;
    public $CraftRate;
    public $GoldDropRateOffset;
    public $AttackBonus;
    public $Magics;
    public $ActionList;
    public $PoisonList;
    public $BuffList;
    public $Health; // 状态恢复
    public $Pets;
    public $PKPoints;
    public $AMode;
    public $PMode;
    public $CallingNPC;
    public $CallingNPCPage;
    public $Slaying; // TODO
    public $FlamingSword; // TODO
    public $TwinDrakeBlade; // TODO
    public $BindMapIndex; // 绑定的地图 死亡时复活用
    public $BindLocation; // 绑定的坐标 死亡时复活用
    public $MagicShield; // TODO 是否有魔法盾
    public $MagicShieldLv; // TODO 魔法盾等级
    public $ArmourRate; // 防御
    public $DamageRate; // 伤害
    public $StruckTime; // 被攻击硬直时间
    public $AllowGroup; // 是否允许组队
    public $GroupMembers; // 小队成员
    public $GroupInvitation; // 组队邀请人
    public $CurrentDirection;
    public $CurrentLocation;
    public $Characters;

    public function getPlayer($fd)
    {
        return json_decode(getObject('Redis')->get('SessionIDPlayerMap_' . getClientId($fd)), true);
    }

    public function setPlayer($fd, $data = null)
    {
        if ($data) {
            foreach ($data as $k => $v) {
                $this->$k = $v;
            }
        }
        $array = json_decode(json_encode($this), true);
        getObject('Redis')->set('SessionIDPlayerMap_' . getClientId($fd), json_encode($array, JSON_UNESCAPED_UNICODE));
    }

    public function updatePlayerInfo(&$PlayerObject, $accountCharacter, $user_magic)
    {
        $Enum     = getObject('Enum');
        $Bag      = getObject('Bag');
        $GameData = getObject('GameData');

        $PlayerObject['GameStage']        = $Enum::GAME;
        $PlayerObject['ID']               = $accountCharacter['id'];
        $PlayerObject['Name']             = $accountCharacter['name'];
        $PlayerObject['NameColor']        = ['R' => 255, 'G' => 255, 'B' => 255];
        $PlayerObject['CurrentDirection'] = $accountCharacter['direction'];
        $PlayerObject['CurrentLocation']  = ['X' => $accountCharacter['current_location_x'], 'Y' => $accountCharacter['current_location_y']];
        $PlayerObject['BindLocation']     = ['X' => $accountCharacter['bind_location_x'], 'Y' => $accountCharacter['bind_location_y']];
        $PlayerObject['BindMapIndex']     = $accountCharacter['bind_map_id'];

        $PlayerObject['Inventory']      = $Bag->bagLoadFromDB($accountCharacter['id'], $Enum::UserItemTypeInventory, 46);
        $PlayerObject['Equipment']      = $Bag->bagLoadFromDB($accountCharacter['id'], $Enum::UserItemTypeEquipment, 14);
        $PlayerObject['QuestInventory'] = $Bag->bagLoadFromDB($accountCharacter['id'], $Enum::UserItemTypeQuestInventory, 40);
        $PlayerObject['Storage']        = $Bag->bagLoadFromDB($accountCharacter['id'], $Enum::UserItemTypeStorage, 80);

        $PlayerObject['Dead']          = 0;
        $PlayerObject['HP']            = $accountCharacter['hp'];
        $PlayerObject['MP']            = $accountCharacter['mp'];
        $PlayerObject['Level']         = $accountCharacter['level'];
        $PlayerObject['Experience']    = $accountCharacter['experience'];
        $PlayerObject['Gold']          = $accountCharacter['gold'];
        $PlayerObject['GuildName']     = ''; //TODO
        $PlayerObject['GuildRankName'] = ''; //TODO
        $PlayerObject['Class']         = $accountCharacter['class'];
        $PlayerObject['Gender']        = $accountCharacter['gender'];
        $PlayerObject['Hair']          = $accountCharacter['hair'];
        $PlayerObject['SendItemInfo']  = [];
        $PlayerObject['MaxExperience'] = $GameData->getExpList($accountCharacter['level']);

        if ($user_magic) {
            foreach ($user_magic as $k => $v) {
                $user_magic[$k]['Info'] = $GameData->getMagicInfoByID($v['magic_id']);
            }
        }

        $PlayerObject['Magics'] = $user_magic;

        $PlayerObject['ActionList'] = []; //TODO
        $PlayerObject['PoisonList'] = []; //TODO
        $PlayerObject['BuffList']   = [];
        $PlayerObject['Health']     = [
            'HPPotNextTime' => time(),
            'HPPotDuration' => 1,
            'MPPotNextTime' => time(),
            'MPPotDuration' => 1,
            'HealNextTime'  => time(),
            'HealDuration'  => 10,
        ];
        $PlayerObject['Pets']       = [];
        $PlayerObject['PKPoints']   = 0;
        $PlayerObject['AMode']      = $accountCharacter['attack_mode'];
        $PlayerObject['PMode']      = $accountCharacter['pet_mode'];
        $PlayerObject['CallingNPC'] = null;
        $PlayerObject['StruckTime'] = time();
        $PlayerObject['DamageRate'] = 1.0;
        $PlayerObject['ArmourRate'] = 1.0;
        $PlayerObject['AllowGroup'] = $accountCharacter['allow_group'];
        $PlayerObject['Pets']       = [];

        $StartItems = $GameData->getStartItems();

        if ($PlayerObject['Level'] < 1) {
            foreach ($StartItems as $k => $v) {
                $PlayerObject = $this->gainItem($PlayerObject, $v);
            }
        }
    }

    // GainItem 为玩家增加物品，增加成功返回 true
    public function gainItem($PlayerObject, $v)
    {
        $itemInfo                = $this->newUserItem($v, $PlayerObject['ID']);
        $itemInfo['SoulBoundId'] = $PlayerObject['ID'];

        $Bag     = getObject('Bag');
        $SendMsg = getObject('SendMsg');

        if ($itemInfo['Info']['stack_size'] > 1) {
            foreach ($PlayerObject['Inventory']['Items'] as $k1 => $v1) {
                if (!$v || $itemInfo['Info'] == $v1['Info'] || $v1['count'] > $itemInfo['Info']['stack_size']) {
                    continue;
                }

                if ($itemInfo['Count'] + $v1['count'] <= $v['stack_size']) {

                    $PlayerObject['Inventory'] = $Bag->setCount($PlayerObject['Inventory'], $k1, $v['Count'] + $itemInfo['Count']);

                    $SendMsg->send($PlayerObject['fd'], ['GAINED_ITEM', ['Item' => [$itemInfo]]]);

                    return $PlayerObject;
                }

                $PlayerObject['Inventory'] = $Bag->setCount($PlayerObject['Inventory'], $k1, $v['Count'] + $itemInfo['Count']);
                $itemInfo['Count'] -= $itemInfo['Info']['stack_size'] - $v1['count'];
            }
        }

        $i = 0;
        $j = 46;

        $Enum = getObject('Enum');

        if ($itemInfo['Info']['type'] == $Enum::ItemTypePotion
            || $itemInfo['Info']['type'] == $Enum::ItemTypeScroll
            || ($itemInfo['Info']['type'] == $Enum::ItemTypeScript && $itemInfo['Info']['effect'])
        ) {
            $i = 0;
            $j = 4;
        } elseif ($itemInfo['Info']['type'] == $Enum::ItemTypeAmulet) {
            $i = 4;
            $j = 6;
        } else {
            $i = 6;
            $j = 46;
        }

        for ($i = $i; $i < $j; $i++) {
            if (empty($PlayerObject['Inventory']['Items'][$i])) {
                continue;
            }

            $PlayerObject['Inventory'] = $Bag->setCount($PlayerObject['Inventory'], $i, $itemInfo);

            $this->enqueueItemInfo($PlayerObject, $itemInfo['ItemID']);
            $SendMsg->send($PlayerObject['fd'], ['GAINED_ITEM', ['Item' => [$itemInfo]]]);
            $this->refreshBagWeight($PlayerObject);

            return $PlayerObject;
        }

        for ($i = 0; $i < 46; $i++) {
            if (empty($PlayerObject['Inventory']['Items'][$i])) {
                continue;
            }

            $PlayerObject['Inventory'] = $Bag->set($PlayerObject['Inventory'], $i, $itemInfo);

            $this->enqueueItemInfo($PlayerObject, $itemInfo['ItemID']);
            $SendMsg->send($PlayerObject['fd'], ['GAINED_ITEM', ['Item' => [$itemInfo]]]);
            $this->refreshBagWeight($PlayerObject);

            return $PlayerObject;
        }

        $this->receiveChat($PlayerObject['fd'], '没有合适的格子放置物品', $Enum::ChatTypeSystem);

        return $PlayerObject;
    }

    public function receiveChat($fd, $msg, $type)
    {
        getObject('SendMsg')->send($fd, ['CHAT', ['Message' => $msg, 'Type' => $type]]);
    }

    public function newUserItem($itemInfo, $ID)
    {
        return [
            'ID'             => $ID,
            'ItemID'         => $itemInfo['id'],
            'CurrentDura'    => 100,
            'MaxDura'        => 100,
            'Count'          => 1,
            'AC'             => $itemInfo['min_ac'],
            'MAC'            => $itemInfo['max_ac'],
            'DC'             => $itemInfo['min_dc'],
            'MC'             => $itemInfo['min_mc'],
            'SC'             => $itemInfo['min_sc'],
            'Accuracy'       => $itemInfo['accuracy'],
            'Agility'        => $itemInfo['agility'],
            'HP'             => $itemInfo['hp'],
            'MP'             => $itemInfo['mp'],
            'AttackSpeed'    => $itemInfo['attack_speed'],
            'Luck'           => $itemInfo['luck'],
            'SoulBoundId'    => 0,
            'Bools'          => 0,
            'Strong'         => 0,
            'MagicResist'    => 0,
            'PoisonResist'   => 0,
            'HealthRecovery' => 0,
            'ManaRecovery'   => 0,
            'PoisonRecovery' => 0,
            'CriticalRate'   => 0,
            'CriticalDamage' => 0,
            'Freezing'       => 0,
            'PoisonAttack'   => 0,
            'Info'           => $itemInfo,
        ];
    }

    public function enqueueItemInfo(&$p, $ItemID)
    {
        if ($p['SendItemInfo']) {
            foreach ($p['SendItemInfo'] as $k => $v) {
                if ($v['id'] == $ItemID) {
                    return $p;
                }
            }
        }

        $item = getObject('GameData')->getItemInfoByID($ItemID);

        if (!$item) {
            return false;
        }

        getObject('SendMsg')->send($p['fd'], ['NEW_ITEM_INFO', ['Info' => $item]]);

        $p['SendItemInfo'][] = $item;

        return true;
    }

    public function StartGame($p)
    {
        $Enum    = getObject('Enum');
        $Server  = getObject('Server');
        $SendMsg = getObject('SendMsg');

        $this->receiveChat($p['fd'], '[欢迎进入游戏,游戏目前处于测试模式]', $Enum::ChatTypeHint);
        $this->receiveChat($p['fd'], '[本模拟器为学习研究使用,禁止一切商业行为]', $Enum::ChatTypeHint);
        $this->receiveChat($p['fd'], '[模拟器已经开源,其他人员非法使用与本模拟器无关]', $Enum::ChatTypeHint);
        $this->receiveChat($p['fd'], '[有任何意见及建议欢迎加QQ群并联系管理员,群号186510932]', $Enum::ChatTypeHint);

        $this->enqueueItemInfos($p);

        $this->refreshStats($p);

        $this->enqueueQuestInfo($p);//任务

        $SendMsg->send($p['fd'], ['MAP_INFORMATION', $p['Map']['Info']]);

        $SendMsg->send($p['fd'], ['USER_INFORMATION', getObject('MsgFactory')->userInformation($p)]);

        $SendMsg->send($p['fd'], ['TIME_OF_DAY', ['Lights' => getObject('Settings')->lightSet()]]);

        $SendMsg->send($p['fd'], ['CHANGE_A_MODE', ['Mode' => $p['AMode']]]);

        $SendMsg->send($p['fd'], ['CHANGE_P_MODE', ['Mode' => $p['PMode']]]);

        $SendMsg->send($p['fd'], ['SWITCH_GROUP', ['AllowGroup' => $p['AllowGroup'] ?: 0]]);

        $this->enqueueAreaObjects($p, null, $this->getCell($p['Map'], $p['CurrentLocation']));

        // p.EnqueueAreaObjects(nil, p.GetCell())
        // p.Enqueue(ServerMessage{}.NPCResponse([]string{}))
        // p.Broadcast(ServerMessage{}.ObjectPlayer(p))

        // $Server->send($p['fd'], bytesToString([52, 0, 59, 0, 43, 144, 2, 0, 9, 231, 168, 187, 232, 141, 137, 228, 186, 186, 255, 255, 255, 255, 11, 1, 0, 0, 87, 2, 0, 0, 5, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]));
        // $Server->send($p['fd'], bytesToString([52, 0, 59, 0, 12, 144, 2, 0, 9, 231, 168, 187, 232, 141, 137, 228, 186, 186, 255, 255, 255, 255, 9, 1, 0, 0, 87, 2, 0, 0, 5, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]));
        // $Server->send($p['fd'], bytesToString([46, 0, 59, 0, 227, 143, 2, 0, 3, 233, 185, 191, 255, 255, 255, 255, 8, 1, 0, 0, 95, 2, 0, 0, 4, 0, 0, 0, 2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]));

        $Server->send($p['fd'], bytesToString([51, 0, 77, 0, 169, 161, 1, 0, 19, 228, 188, 160, 233, 128, 129, 229, 145, 152, 95, 231, 171, 160, 230, 165, 154, 232, 144, 147, 0, 255, 0, 255, 15, 0, 0, 0, 0, 0, 31, 1, 0, 0, 103, 2, 0, 0, 1, 0, 0, 0, 0]));
        $Server->send($p['fd'], bytesToString([52, 0, 59, 0, 174, 168, 1, 0, 9, 231, 168, 187, 232, 141, 137, 228, 186, 186, 255, 255, 255, 255, 33, 1, 0, 0, 106, 2, 0, 0, 5, 0, 4, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]));
        $Server->send($p['fd'], bytesToString([48, 0, 77, 0, 170, 161, 1, 0, 16, 232, 190, 185, 229, 162, 131, 230, 157, 145, 95, 229, 145, 138, 231, 164, 186, 0, 255, 0, 255, 45, 0, 0, 0, 0, 0, 28, 1, 0, 0, 103, 2, 0, 0, 0, 0, 0, 0, 0]));
        $Server->send($p['fd'], bytesToString([46, 0, 59, 0, 122, 168, 1, 0, 3, 233, 185, 191, 255, 255, 255, 255, 28, 1, 0, 0, 105, 2, 0, 0, 4, 0, 5, 0, 2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]));
        

        $this->setPlayer($p['fd'], $p);
    }

    public function stopGame($p)
    {
        $this->broadcast($p, ['OBJECT_REMOVE', ['ObjectID' => $p['ID']]]);
    }

    //物品
    public function enqueueItemInfos(&$p)
    {
        $itemInfos = [];

        if ($p['Inventory']['Items']) {
            foreach ($p['Inventory']['Items'] as $k => $v) {
                if ($v) {
                    $p['Inventory']['Items'][$k]['isset'] = true;
                    $itemInfos[]                          = getObject('GameData')->getItemInfoByID($v['item_id']);
                } else {
                    $p['Inventory']['Items'][$k]['isset'] = false;
                }

            }
        }

        if ($p['Equipment']['Items']) {
            foreach ($p['Equipment']['Items'] as $k => $v) {
                if ($v) {
                    $p['Equipment']['Items'][$k]['isset'] = true;
                    $itemInfos[]                          = getObject('GameData')->getItemInfoByID($v['item_id']);
                } else {
                    $p['Equipment']['Items'][$k]['isset'] = false;
                }
            }
        }

        if ($p['QuestInventory']['Items']) {
            foreach ($p['QuestInventory']['Items'] as $k => $v) {
                if ($v) {
                    $p['QuestInventory']['Items'][$k]['isset'] = true;
                    $itemInfos[]                               = getObject('GameData')->getItemInfoByID($v['item_id']);
                } else {
                    $p['QuestInventory']['Items'][$k]['isset'] = false;
                }
            }
        }

        if ($itemInfos) {
            foreach ($itemInfos as $k => $v) {
                $this->enqueueItemInfo($p, $v['id']);
            }
        }

        return $p;
    }

    //状态
    public function refreshStats(&$p)
    {
        $this->refreshLevelStats($p);
        $this->refreshBagWeight($p);
        $this->refreshEquipmentStats($p);
        $this->refreshItemSetStats($p);
        $this->refreshMirSetStats($p);
        $this->refreshBuffs($p);
        $this->refreshStatCaps($p);
        $this->refreshMountStats($p);
        $this->refreshGuildBuffs($p);
    }

    //刷新级别状态
    public function refreshLevelStats(&$p)
    {
        $baseStats = getObject('Settings')->getBaseStats($p['Class']); //职业属性

        $p['Accuracy']       = $baseStats['StartAccuracy'];
        $p['Agility']        = $baseStats['StartAgility'];
        $p['CriticalRate']   = $baseStats['StartCriticalRate'];
        $p['CriticalDamage'] = $baseStats['StartCriticalDamage'];

        $ExpList = getObject('GameData')->getExpList();

        if ($p['Level'] < count($ExpList)) {
            $p['MaxExperience'] = $ExpList[$p['Level'] - 1];
        } else {
            $p['MaxExperience'] = 0;
        }

        $p['MaxHP'] = intval(14 + ($p['Level'] / $baseStats['HpGain'] + $baseStats['HpGainRate']) * $p['Level']);

        $p['MinAC'] = 0;
        if ($baseStats['MinAc'] > 0) {
            $p['MinAC'] = intval($p['Level'] / $baseStats['MinAc']);
        }

        $p['MaxAC'] = 0;
        if ($baseStats['MaxAc'] > 0) {
            $p['MaxAC'] = intval($p['Level'] / $baseStats['MaxAc']);
        }

        $p['MinMAC'] = 0;
        if ($baseStats['MinMac'] > 0) {
            $p['MinMAC'] = intval($p['Level'] / $baseStats['MinMac']);
        }

        $p['MaxMAC'] = 0;
        if ($baseStats['MaxMac'] > 0) {
            $p['MaxMAC'] = intval($p['Level'] / $baseStats['MaxMac']);
        }

        $p['MinDC'] = 0;
        if ($baseStats['MinDc'] > 0) {
            $p['MinDC'] = intval($p['Level'] / $baseStats['MinDc']);
        }

        $p['MaxDC'] = 0;
        if ($baseStats['MaxDc'] > 0) {
            $p['MaxDC'] = intval($p['Level'] / $baseStats['MaxDc']);
        }

        $p['MinMC'] = 0;
        if ($baseStats['MinMc'] > 0) {
            $p['MinMC'] = intval($p['Level'] / $baseStats['MinMc']);
        }

        $p['MaxMC'] = 0;
        if ($baseStats['MaxMc'] > 0) {
            $p['MaxMC'] = intval($p['Level'] / $baseStats['MaxMc']);
        }

        $p['MinSC'] = 0;
        if ($baseStats['MinSc'] > 0) {
            $p['MinSC'] = intval($p['Level'] / $baseStats['MinSc']);
        }

        $p['MaxSC'] = 0;
        if ($baseStats['MaxSc'] > 0) {
            $p['MaxSC'] = intval($p['Level'] / $baseStats['MaxSc']);
        }

        $p['CriticalRate'] = 0;
        if ($baseStats['CritialRateGain'] > 0) {
            $p['CriticalRate'] = intval($p['CriticalRate'] + ($p['Level'] / $baseStats['CritialRateGain']));
        }

        $p['CriticalDamage'] = 0;
        if ($baseStats['CriticalDamageGain'] > 0) {
            $p['CriticalDamage'] = intval($p['CriticalDamage'] + ($p['Level'] / $baseStats['CriticalDamageGain']));
        }

        $p['MaxBagWeight']  = intval(50.0 + $p['Level'] / $baseStats['BagWeightGain'] * $p['Level']);
        $p['MaxWearWeight'] = intval(15.0 + $p['Level'] / $baseStats['WearWeightGain'] * $p['Level']);
        $p['MaxHandWeight'] = intval(12.0 + $p['Level'] / $baseStats['HandWeightGain'] * $p['Level']);

        $Enum = getObject('Enum');

        switch ($p['Class']) {
            case $Enum::MirClassWarrior:
                $p['MaxHP'] = intval(14.0 + ($p['Level'] / $baseStats['HpGain'] + $baseStats['HpGainRate'] + $p['Level'] / 20.0) * $p['Level']);
                $p['MaxMP'] = intval(11.0 + ($p['Level'] * 3.5) + ($p['Level'] * $baseStats['MpGainRate']));
                break;

            case $Enum::MirClassWizard:
                $p['MaxMP'] = intval(13.0 + (($p['Level'] / 5.0 + 2.0) * 2.2 * $p['Level']) + ($p['Level'] * $baseStats['MpGainRate']));
                break;

            case $Enum::MirClassTaoist:
                $p['MaxMP'] = intval((13 + $p['Level'] / 8.0 * 2.2 * $p['Level']) + ($p['Level'] * $baseStats['MpGainRate']));
                break;
        }
    }

    public function refreshBagWeight(&$p)
    {
        $p['CurrentBagWeight'] = 0;

        foreach ($p['Inventory']['Items'] as $k => $v) {
            if ($v && $v['isset']) {
                $item = getObject('GameData')->getItemInfoByID($v['item_id']);
                $p['CurrentBagWeight'] += $item['weight'];
            }
        }
    }

    public function refreshEquipmentStats(&$p)
    {
        $oldLooksWeapon       = $p['LooksWeapon'];
        $oldLooksWeaponEffect = $p['LooksWeaponEffect'];
        $oldLooksArmour       = $p['LooksArmour'];
        $oldLooksWings        = $p['LooksWings'];
        $oldLight             = $p['Light'];

        $p['LooksArmour']       = 0;
        $p['LooksWeapon']       = -1;
        $p['LooksWeaponEffect'] = 0;
        $p['LooksWings']        = 0;

        $GameData = getObject('GameData');
        $Enum     = getObject('Enum');

        $ItemInfos = $GameData->getItemInfos();
        foreach ($p['Equipment']['Items'] as $temp) {
            if (!$temp || !$temp['isset']) {
                continue;
            }

            $RealItem = $GameData->getRealItem($temp['Info'], $p['Level'], $p['Class'], $ItemInfos);

            $p['MinAC']  = toUint16(intval($p['MinAC']) + intval($RealItem['min_ac']));
            $p['MaxAC']  = toUint16(intval($p['MaxAC']) + intval($RealItem['max_ac']) + intval($temp['ac']));
            $p['MinMAC'] = toUint16(intval($p['MinMAC']) + intval($RealItem['min_mac']));
            $p['MaxMAC'] = toUint16(intval($p['MaxMAC']) + intval($RealItem['max_mac']) + intval($temp['mac']));
            $p['MinDC']  = toUint16(intval($p['MinDC']) + intval($RealItem['min_dc']));
            $p['MaxDC']  = toUint16(intval($p['MaxDC']) + intval($RealItem['max_dc']) + intval($temp['dc']));
            $p['MinMC']  = toUint16(intval($p['MinMC']) + intval($RealItem['min_mc']));
            $p['MaxMC']  = toUint16(intval($p['MaxMC']) + intval($RealItem['max_mc']) + intval($temp['mc']));
            $p['MinSC']  = toUint16(intval($p['MinSC']) + intval($RealItem['min_sc']));
            $p['MaxSC']  = toUint16(intval($p['MaxSC']) + intval($RealItem['max_sc']) + intval($temp['sc']));
            $p['MaxHP']  = toUint16(intval($p['MaxHP']) + intval($RealItem['hp']) + intval($temp['hp']));
            $p['MaxMP']  = toUint16(intval($p['MaxMP']) + intval($RealItem['mp']) + intval($temp['mp']));

            $p['MaxBagWeight']  = toUint16(intval($p['MaxBagWeight']) + intval($RealItem['bag_weight']));
            $p['MaxWearWeight'] = toUint16(intval($p['MaxWearWeight']) + intval($RealItem['wear_weight']));
            $p['MaxHandWeight'] = toUint16(intval($p['MaxHandWeight']) + intval($RealItem['hand_weight']));

            $p['ASpeed']   = toInt8(intval($p['ASpeed']) + intval($temp['attack_speed']) + intval($RealItem['attack_speed']));
            $p['Luck']     = toInt8(intval($p['Luck']) + intval($temp['luck']) + intval($RealItem['luck']));
            $p['Accuracy'] = toUint8(intval($p['Accuracy']) + intval($RealItem['accuracy']) + intval($temp['accuracy']));
            $p['Agility']  = toUint8(intval($p['Agility']) + intval($RealItem['agility']) + intval($temp['agility']));

            $p['MagicResist']    = toUint8(intval($p['MagicResist']) + intval($temp['magic_resist']) + intval($RealItem['magic_resist']));
            $p['PoisonResist']   = toUint8(intval($p['PoisonResist']) + intval($temp['poison_resist']) + intval($RealItem['poison_resist']));
            $p['HealthRecovery'] = toUint8(intval($p['HealthRecovery']) + intval($temp['health_recovery']) + intval($RealItem['health_recovery']));
            $p['SpellRecovery']  = toUint8(intval($p['SpellRecovery']) + intval($temp['mana_recovery']) + intval($RealItem['spell_recovery']));
            $p['PoisonRecovery'] = toUint8(intval($p['PoisonRecovery']) + intval($temp['poison_recovery']) + intval($RealItem['poison_recovery']));
            $p['CriticalRate']   = toUint8(intval($p['CriticalRate']) + intval($temp['critical_rate']) + intval($RealItem['critical_rate']));
            $p['CriticalDamage'] = toUint8(intval($p['CriticalDamage']) + intval($temp['critical_damage']) + intval($RealItem['critical_damage']));
            $p['Holy']           = toUint8(intval($p['Holy']) + intval($RealItem['holy']));
            $p['Freezing']       = toUint8(intval($p['Freezing']) + intval($temp['freezing']) + intval($RealItem['freezing']));
            $p['PoisonAttack']   = toUint8(intval($p['PoisonAttack']) + intval($temp['poison_attack']) + intval($RealItem['poison_attack']));
            $p['Reflect']        = toUint8(intval($p['Reflect']) + intval($RealItem['reflect']));
            $p['HpDrainRate']    = toUint8(intval($p['HpDrainRate']) + intval($RealItem['hp_drain_rate']));

            switch ($RealItem['type']) {
                case $Enum::ItemTypeArmour:
                    $p['LooksArmour'] = intval($RealItem['shape']);
                    $p['LooksWings']  = intval($RealItem['effect']);
                    break;

                case $Enum::ItemTypeWeapon:
                    $p['LooksWeapon']       = intval($RealItem['shape']);
                    $p['LooksWeaponEffect'] = intval($RealItem['effect']);
                    break;
            }
        }

        // /*
        //     MaxHP = (ushort)Math.Min(ushort.MaxValue, (((double)HPrate / 100) + 1) * MaxHP);
        //     MaxMP = (ushort)Math.Min(ushort.MaxValue, (((double)MPrate / 100) + 1) * MaxMP);
        //     MaxAC = (ushort)Math.Min(ushort.MaxValue, (((double)Acrate / 100) + 1) * MaxAC);
        //     MaxMAC = (ushort)Math.Min(ushort.MaxValue, (((double)Macrate / 100) + 1) * MaxMAC);

        //     AddTempSkills(skillsToAdd);
        //     RemoveTempSkills(skillsToRemove);

        //     if (HasMuscleRing)
        //     {
        //         MaxBagWeight = (ushort)(MaxBagWeight * 2);
        //         MaxWearWeight = Math.Min(ushort.MaxValue, (ushort)(MaxWearWeight * 2));
        //         MaxHandWeight = Math.Min(ushort.MaxValue, (ushort)(MaxHandWeight * 2));
        //     }
        // */

        if ($oldLooksArmour != $p['LooksArmour'] || $oldLooksWeapon != $p['LooksWeapon'] || $oldLooksWeaponEffect != $p['LooksWeaponEffect'] || $oldLooksWings != $p['LooksWings'] || $oldLight != $p['Light']) {
            $this->broadcast($p, $this->getUpdateInfo($p));
        }
    }

    public function getUpdateInfo($p)
    {
        $this->updateConcentration($p);

        return [
            'PLAYER_UPDATE',
            [
                'ObjectID'     => $p['ID'],
                'Weapon'       => $p['LooksWeapon'],
                'WeaponEffect' => $p['LooksWeaponEffect'],
                'Armour'       => $p['LooksArmour'],
                'Light'        => $p['Light'],
                'WingEffect'   => $p['LooksWings'],
            ],
        ];
    }

    public function updateConcentration($p)
    {
        getObject('SendMsg')->send($p['fd'], ['SET_CONCENTRATION', ['ObjectID' => $p['AccountID'], 'Enabled' => 0, 'Interrupted' => 0]]);
        $this->broadcast($p, ['SET_OBJECT_CONCENTRATION', ['ObjectID' => $p['AccountID'], 'Enabled' => 0, 'Interrupted' => 0]]);
    }

    public function broadcast($p, $msg)
    {
        getObject('Map')->broadcastP($p['CurrentLocation'], $msg, $p);
    }

    public function refreshItemSetStats(&$p)
    {
        # code...
    }

    public function refreshMirSetStats(&$p)
    {
        # code...
    }

    public function refreshSkills(&$p)
    {
        $Enum = getObject('Map');

        if (!empty($p['Magics'])) {
            foreach ($p['Magics'] as $k => $magic) {
                switch ($magic['Spell']) {
                    case $Enum::SpellFencing: // 基本剑术
                        $p['Accuracy'] = toUint8(intval($p['Accuracy']) + $magic['Level'] * 3);
                        $p['MaxAC']    = toUint16(intval($p['MaxAC']) + ($magic['Level'] + 1) * 3);
                        break;

                    case $Enum::SpellFatalSword: // 刺客的技能 忽略
                        break;

                    case $Enum::SpellSpiritSword: // 精神力战法
                        $p['Accuracy'] = toUint8(intval($p['Accuracy']) + $magic['Level']);
                        $p['MaxAC']    = toUint16(intval($p['MaxDC']) + intval($p['MaxSC'] * $magic['Level'] + 1 * 0.1));
                        break;

                }
            }
        }
    }

    //刷新玩家身上的 buff
    public function refreshBuffs(&$p)
    {
        # code...
    }

    //刷新各种状态
    public function refreshStatCaps(&$p)
    {
        # code...
    }

    //刷新装备嵌套的宝石属性
    public function refreshMountStats(&$p)
    {
        # code...
    }

    //刷新工会 buff
    public function refreshGuildBuffs(&$p)
    {
        # code...
    }

    public function enqueueQuestInfo(&$p)
    {
        # code...
    }

    public function enqueueAreaObjects($p, $oldCell, $newCell)
    {
        if ($oldCell == null) {
            getObject('Map')->rangeObject($p['CurrentLocation'], 20);
        }
    }

    public function getCell($map, $CurrentLocation)
    {
        return getObject('Map')->getCell($map, $CurrentLocation);
    }
}
