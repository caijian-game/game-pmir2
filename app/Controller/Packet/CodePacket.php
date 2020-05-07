<?php
declare (strict_types = 1);

namespace App\Controller\Packet;

/**
 *
 */
class CodePacket
{
    private $clientPacketStruct = [
        'CLIENT_VERSION'   => [
            'VersionHash' => '[]int8',
        ],
        'KEEP_ALIVE'       => [
            'Time' => 'int64',
        ],
        'NEW_ACCOUNT'      => [
            'account'      => 'string',
            'Password'       => 'string',
            'DateTime'       => 'int64',
            'UserName'       => 'string',
            'SecretQuestion' => 'string',
            'SecretAnswer'   => 'string',
            'EMailAddress'   => 'string',
        ],
        'CHANGE_PASSWORD'  => [
            'account'       => 'string',
            'CurrentPassword' => 'string',
            'NewPassword'     => 'string',
        ],
        'LOGIN'            => [
            'account'  => 'string',
            'Password' => 'string',
        ],
        'NEW_CHARACTER'    => [
            'Name'   => 'string',
            'Gender' => 'int8',
            'Class'  => 'int8',
        ],
        'DELETE_CHARACTER' => [
            'CharacterIndex' => 'int32',
        ],
        'START_GAME'       => [
            'CharacterIndex' => 'int16',
        ],
        'WALK'             => [
            'Direction' => 'uint8',
        ],
        'RUN'              => [
            'Direction' => 'uint8',
        ],
        'TURN'             => [
            'Direction' => 'uint8',
        ],
        'LOG_OUT'          => [

        ],
    ];

    private $serverPacketStruct = [
        'CLIENT_VERSION'           => [
            'Result' => 'uint8',
        ],
        'KEEP_ALIVE'               => [
            'Time' => 'int64',
        ],
        'NEW_ACCOUNT'              => [
            'Result' => 'uint8',
        ],
        'CHANGE_PASSWORD'          => [
            'Result' => 'uint8',
        ],
        'LOGIN'                    => [
            'Result' => 'uint8',
        ],
        'LOGIN_BANNED'             => [

        ],
        'LOGIN_SUCCESS'            => [
            'Count'      => 'int32',
            'Characters' => [
                'Index'      => 'uint32',
                'Name'       => 'string',
                'Level'      => 'uint16',
                'Class'      => 'int8',
                'Gender'     => 'int8',
                'LastAccess' => 'int64',
            ],
        ],
        'NEW_CHARACTER'            => [
            'Result' => 'uint8',
        ],
        'NEW_CHARACTER_SUCCESS'    => [
            'CharInfo' => [
                'Index'      => 'uint32',
                'Name'       => 'string',
                'Level'      => 'uint16',
                'Class'      => 'int8',
                'Gender'     => 'int8',
                'LastAccess' => 'int64',
            ],
        ],
        'DELETE_CHARACTER'         => [
            'Result' => 'uint8',
        ],
        'DELETE_CHARACTER_SUCCESS' => [
            'CharacterIndex' => 'int32',
        ],
        'START_GAME'               => [
            'Result'     => 'uint8',
            'Resolution' => 'int32',
        ],
        'SET_CONCENTRATION'        => [
            'ObjectID'    => 'uint32',
            'Enabled'     => 'int8',
            'Interrupted' => 'int8',
        ],
        'GAINED_ITEM'              => [
            'Item' => [
                'ID'             => 'uint64',
                'ItemID'         => 'int32',
                'CurrentDura'    => 'uint16',
                'MaxDura'        => 'uint16',
                'Count'          => 'uint32',
                'AC'             => 'uint8',
                'MAC'            => 'uint8',
                'DC'             => 'uint8',
                'MC'             => 'uint8',
                'SC'             => 'uint8',
                'Accuracy'       => 'uint8',
                'Agility'        => 'uint8',
                'HP'             => 'uint8',
                'MP'             => 'uint8',
                'AttackSpeed'    => 'int8',
                'Luck'           => 'int8',
                'SoulBoundId'    => 'uint32',
                'Bools'          => 'uint8',
                'Strong'         => 'uint8',
                'MagicResist'    => 'uint8',
                'PoisonResist'   => 'uint8',
                'HealthRecovery' => 'uint8',
                'ManaRecovery'   => 'uint8',
                'PoisonRecovery' => 'uint8',
                'CriticalRate'   => 'uint8',
                'CriticalDamage' => 'uint8',
                'Freezing'       => 'uint8',
                'PoisonAttack'   => 'uint8',
            ],
        ],
        'NEW_ITEM_INFO'            => [
            'Info' => [
                'id'              => 'int32',
                'name'            => 'string',
                'type'            => 'uint8',
                'grade'           => 'uint8',
                'required_type'   => 'uint8',
                'required_class'  => 'uint8',
                'required_gender' => 'uint8',
                'item_set'        => 'uint8',
                'shape'           => 'int16',
                'weight'          => 'uint8',
                'light'           => 'uint8',
                'required_amount' => 'uint8',
                'image'           => 'uint16',
                'durability'      => 'uint16',
                'stack_size'      => 'uint32',
                'price'           => 'uint32',
                'min_ac'          => 'uint8',
                'max_ac'          => 'uint8',
                'min_mac'         => 'uint8',
                'max_mac'         => 'uint8',
                'min_dc'          => 'uint8',
                'max_dc'          => 'uint8',
                'min_mc'          => 'uint8',
                'max_mc'          => 'uint8',
                'min_sc'          => 'uint8',
                'max_sc'          => 'uint8',
                'hp'              => 'uint16',
                'mp'              => 'uint16',
                'accuracy'        => 'uint8',
                'agility'         => 'uint8',
                'luck'            => 'int8',
                'attack_speed'    => 'int8',
                'start_item'      => 'bool',
                'bag_weight'      => 'uint8',
                'hand_weight'     => 'uint8',
                'wear_weight'     => 'uint8',
                'effect'          => 'uint8',
                'strong'          => 'uint8',
                'magic_resist'    => 'uint8',
                'poison_resist'   => 'uint8',
                'health_recovery' => 'uint8',
                'spell_recovery'  => 'uint8',
                'poison_recovery' => 'uint8',
                'hp_rate'         => 'uint8',
                'mp_rate'         => 'uint8',
                'critical_rate'   => 'uint8',
                'critical_damage' => 'uint8',
                'bools'           => 'uint8',
                'max_ac_rate'     => 'uint8',
                'max_mac_rate'    => 'uint8',
                'holy'            => 'uint8',
                'freezing'        => 'uint8',
                'poison_attack'   => 'uint8',
                'bind'            => 'uint16',
                'reflect'         => 'uint8',
                'hp_drain_rate'   => 'uint8',
                'unique_item'     => 'int16',
                'random_stats_id' => 'uint8',
                'can_fast_run'    => 'bool',
                'can_awakening'   => 'bool',
                'is_tool_tip'     => 'bool',
                'tool_tip'        => 'string',
                // 'class_based'     => 'bool',
                // 'level_based'     => 'bool',
            ],
        ],
        'CHAT'                     => [
            'Message' => 'string',
            'Type'    => 'uint8',
        ],

        'OBJECT_WALK'              => [
            'ObjectID'  => 'uint32',
            'Location'  => [
                'X' => 'uint32',
                'Y' => 'uint32',
            ],
            'Direction' => 'uint8',
        ],
        'USER_LOCATION'            => [
            'Location'  => [
                'X' => 'uint32',
                'Y' => 'uint32',
            ],
            'Direction' => 'uint8',
        ],
        'PLAYER_UPDATE'            => [
            'ObjectID'     => 'uint32',
            'Weapon'       => 'int16',
            'WeaponEffect' => 'int16',
            'Armour'       => 'int16',
            'Light'        => 'uint8',
            'WingEffect'   => 'uint8',
        ],
        'MAP_INFORMATION'          => [
            'file_name'      => 'string',
            'title'          => 'string',
            'mini_map'       => 'uint16',
            'big_map'        => 'uint16',
            'light'          => 'uint8',
            'lightning'      => 'bool',
            'map_dark_light' => 'uint8',
            'music'          => 'uint16',
        ],
        'USER_INFORMATION'         => [
            'ObjectID'                  => 'uint32',
            'RealID'                    => 'uint32',
            'Name'                      => 'string',
            'GuildName'                 => 'string',
            'GuildRank'                 => 'string',
            'NameColor'                 => 'int32',
            'Class'                     => 'uint8',
            'Gender'                    => 'uint8',
            'Level'                     => 'uint16',
            'Location'                  => [
                'X' => 'uint32',
                'Y' => 'uint32',
            ],
            'Direction'                 => 'uint8',
            'Hair'                      => 'uint8',
            'HP'                        => 'uint16',
            'MP'                        => 'uint16',
            'Experience'                => 'int64',
            'MaxExperience'             => 'int64',
            'LevelEffect'               => 'uint8',
            'InventoryBool'             => 'bool',
            'InventoryCount'            => 'uint32',
            'Inventory'                 => [
                'isset'           => 'bool',
                'id'              => 'uint64',
                'item_id'         => 'int32',
                'current_dura'    => 'uint16',
                // 'dura_changed'    => 'bool', //???TODO
                'max_dura'        => 'uint16',
                'count'           => 'uint32',
                'ac'              => 'uint8',
                'mac'             => 'uint8',
                'dc'              => 'uint8',
                'mc'              => 'uint8',
                'sc'              => 'uint8',
                'accuracy'        => 'uint8',
                'agility'         => 'uint8',
                'hp'              => 'uint8',
                'mp'              => 'uint8',
                'attack_speed'    => 'int8',
                'luck'            => 'int8',
                'soul_bound_id'   => 'uint32',
                'bools'           => 'uint8',
                'strong'          => 'uint8',
                'magic_resist'    => 'uint8',
                'poison_resist'   => 'uint8',
                'health_recovery' => 'uint8',
                'mana_recovery'   => 'uint8',
                'poison_recovery' => 'uint8',
                'critical_rate'   => 'uint8',
                'critical_damage' => 'uint8',
                'freezing'        => 'uint8',
                'poison_attack'   => 'uint8',
            ],
            'EquipmentBool'             => 'bool',
            'EquipmentCount'            => 'uint32',
            'Equipment'                 => [
                'isset'           => 'bool',
                'id'              => 'uint64',
                'item_id'         => 'int32',
                'current_dura'    => 'uint16',
                // 'dura_changed'    => 'bool', //???TODO
                'max_dura'        => 'uint16',
                'count'           => 'uint32',
                'ac'              => 'uint8',
                'mac'             => 'uint8',
                'dc'              => 'uint8',
                'mc'              => 'uint8',
                'sc'              => 'uint8',
                'accuracy'        => 'uint8',
                'agility'         => 'uint8',
                'hp'              => 'uint8',
                'mp'              => 'uint8',
                'attack_speed'    => 'int8',
                'luck'            => 'int8',
                'soul_bound_id'   => 'uint32',
                'bools'           => 'uint8',
                'strong'          => 'uint8',
                'magic_resist'    => 'uint8',
                'poison_resist'   => 'uint8',
                'health_recovery' => 'uint8',
                'mana_recovery'   => 'uint8',
                'poison_recovery' => 'uint8',
                'critical_rate'   => 'uint8',
                'critical_damage' => 'uint8',
                'freezing'        => 'uint8',
                'poison_attack'   => 'uint8',
            ],
            'QuestInventoryBool'        => 'bool',
            'QuestInventoryCount'       => 'uint32',
            'QuestInventory'            => [
                'isset'           => 'bool',
                'id'              => 'uint64',
                'item_id'         => 'int32',
                'current_dura'    => 'uint16',
                // 'dura_changed'    => 'bool', //???TODO
                'max_dura'        => 'uint16',
                'count'           => 'uint32',
                'ac'              => 'uint8',
                'mac'             => 'uint8',
                'dc'              => 'uint8',
                'mc'              => 'uint8',
                'sc'              => 'uint8',
                'accuracy'        => 'uint8',
                'agility'         => 'uint8',
                'hp'              => 'uint8',
                'mp'              => 'uint8',
                'attack_speed'    => 'int8',
                'luck'            => 'int8',
                'soul_bound_id'   => 'uint32',
                'bools'           => 'uint8',
                'strong'          => 'uint8',
                'magic_resist'    => 'uint8',
                'poison_resist'   => 'uint8',
                'health_recovery' => 'uint8',
                'mana_recovery'   => 'uint8',
                'poison_recovery' => 'uint8',
                'critical_rate'   => 'uint8',
                'critical_damage' => 'uint8',
                'freezing'        => 'uint8',
                'poison_attack'   => 'uint8',
            ],
            'Gold'                      => 'uint32',
            'Credit'                    => 'uint32',
            'HasExpandedStorage'        => 'uint8',
            'ExpandedStorageExpiryTime' => 'int64',
            ////TODO
            // 'ClientMagics'              => [
            //     'name'       => 'string',
            //     'spell'      => 'int',
            //     'base_cost'  => 'uint8',
            //     'level_cost' => 'uint8',
            //     'icon'       => 'uint8',
            //     'level_1'    => 'uint8',
            //     'level_2'    => 'uint8',
            //     'level_3'    => 'uint8',
            //     'need_1'     => 'uint16',
            //     'need_2'     => 'uint16',
            //     'need_3'     => 'uint16',
            //     'Level'      => 'uint8',
            //     'Key'        => 'uint8',
            //     'Experience' => 'uint16',
            //     'Delay'      => 'int64',
            //     'Range'      => 'uint8',
            //     'CastTime'   => 'int64',
            // ],
        ],
        'TIME_OF_DAY'              => [
            'Lights' => 'uint8',
        ],
        'CHANGE_A_MODE'            => [
            'Mode' => 'uint8',
        ],
        'CHANGE_P_MODE'            => [
            'Mode' => 'uint8',
        ],
        'SWITCH_GROUP'             => [
            'AllowGroup' => 'bool',
        ],
        'LOG_OUT_SUCCESS'          => [
            'Count'      => 'int32',
            'Characters' => [
                'Index'      => 'uint32',
                'Name'       => 'string',
                'Level'      => 'uint16',
                'Class'      => 'int8',
                'Gender'     => 'int8',
                'LastAccess' => 'int64',
            ],
        ],
        'OBJECT_REMOVE'            => [
            'ObjectID' => 'uint32',
        ],
    ];

    public function readPacketData(string $cmd, string $packet): array
    {
        $struct = $this->clientPacketStruct[$cmd] ?? [];

        if ($struct) {
            return getObject('BinaryReader')->read($struct, $packet);
        } else {
            return $struct;
        }
    }

    public function writePacketData(string $cmd, array $packet): string
    {
        $struct = $this->serverPacketStruct[$cmd] ?? '';
        if ($struct) {
            return getObject('BinaryReader')->write($struct, $packet);
        } else {
            return $struct;
        }
    }
}
