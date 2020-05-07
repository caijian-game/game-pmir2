<?php
namespace App\Controller\Game;

/**
 *
 */
class MsgFactory
{
    public function userInformation($p)
    {
        $data = [
            'ObjectID'                  => $p['ID'],
            'RealID'                    => $p['ID'],
            'Name'                      => $p['Name'],
            'GuildName'                 => $p['GuildName'],
            'GuildRank'                 => $p['GuildRankName'],
            'NameColor'                 => Int32(pack('c4', $p['NameColor']['R'], $p['NameColor']['G'], $p['NameColor']['B'], 255)),
            'Class'                     => $p['Class'],
            'Gender'                    => $p['Gender'],
            'Level'                     => $p['Level'],
            'Location'                  => $p['CurrentLocation'],
            'Direction'                 => $p['CurrentDirection'],
            'Hair'                      => $p['Hair'],
            'HP'                        => $p['HP'],
            'MP'                        => $p['MP'],
            'Experience'                => $p['Experience'],
            'MaxExperience'             => $p['MaxExperience'],
            'LevelEffect'               => getObject('Enum')::LevelEffectsNone,
            'InventoryBool'             => $p['Inventory']['Items'] ? true : false,
            'Inventory'                 => $p['Inventory']['Items'],
            'EquipmentBool'             => $p['Equipment']['Items'] ? true : false,
            'Equipment'                 => $p['Equipment']['Items'],
            'QuestInventoryBool'        => $p['QuestInventory']['Items'] ? true : false,
            'QuestInventory'            => $p['QuestInventory']['Items'],
            'Gold'                      => $p['Gold'] ?: 0,
            'Credit'                    => 100, // TODO
            'HasExpandedStorage'        => false, // TODO
            'ExpandedStorageExpiryTime' => 0, // TODO
            'ClientMagics'              => [], // TODO,
        ];

        if ($data['InventoryBool']) {
            $data['InventoryCount'] = count($p['Inventory']['Items']);
        }

        if ($data['EquipmentBool']) {
            $data['EquipmentCount'] = count($p['Equipment']['Items']);
        }

        if ($data['QuestInventoryBool']) {
            $data['QuestInventoryCount'] = count($p['QuestInventory']['Items']);
        }

        return $data;
    }
}
