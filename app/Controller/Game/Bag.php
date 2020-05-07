<?php
namespace App\Controller\Game;

/**
 *
 */
class Bag
{
    public function bagLoadFromDB($character_id, $type, $num)
    {
        for ($i = 0; $i < $num; $i++) {
            $Items[$i] = [];
        }

        $bag = [
            'Player' => null,
            'Type'   => $type,
            'Items'  => $Items,
        ];

        $where = [
            'whereInfo' => [
                'where' => [
                    ['character_id', '=', $character_id],
                    ['type', '=', $type],
                ],
            ],
            'pageInfo'  => false,
        ];

        $CommonService = getObject('CommonService');
        $res           = $CommonService->getList('character_user_item', $where);
        if ($res['list']) {

            $ids                = [];
            $userItemIDIndexMap = [];
            foreach ($res['list'] as $k => $v) {
                $ids[]                                  = $v['user_item_id'];
                $userItemIDIndexMap[$v['user_item_id']] = $v['index'];
            }

            $where = [
                'whereInfo' => [
                    'whereIn' => [
                        ['id', $ids],
                    ],
                ],
                'pageInfo'  => false,
            ];

            $res = $CommonService->getList('user_item', $where);

            if ($res['list']) {
                foreach ($res['list'] as $k => $v) {
                    $v['Info']                                   = getObject('GameData')->getItemInfoByID($v['item_id']);
                    $v['dura_changed']                           = false;
                    $bag['Items'][$userItemIDIndexMap[$v['id']]] = $v;
                }
            }
        }

        return $bag;
    }

    public function setCount($Inventory, $i, $count)
    {
        if ($count == 0) {
            $Inventory = $this->set($Inventory, $i, null);
        } else {
            $where = [
                'whereInfo' => [
                    'where' => [
                        'id' => $item['id'],
                    ],
                ],
            ];

            $data = [
                'count' => $count,
            ];

            getObject('CommonService')->upfuild('user_item', $where, $data);

            $Inventory['Items'][$i]['count'] = $count;
        }

        return $Inventory;
    }

    public function set($Inventory, $i, $item = null)
    {
        if (!$item) {
            if (!$Inventory['Items'][$i]) {
                EchoLog('尝试删除空位置的物品', 'e');
            }

            return $Inventory;
            //TODO
        } else {
            $item = $Inventory['Items'][$i];

            if ($item) {
                $where = [
                    'whereInfo' => [
                        'where' => [
                            'id' => $item['id'],
                        ],
                    ],
                ];
                $CommonService = getObject('CommonService');
                $CommonService->delTrue('user_item', $where);

                $where = [
                    'whereInfo' => [
                        'where' => [
                            'user_item_id' => $item['id'],
                        ],
                    ],
                ];

                $CommonService->delTrue('character_user_item', $where);

            } else {
                EchoLog('尝试删除空位置的物品', 'e');
            }

            $Inventory['Items'][$i] = null;

            return $Inventory;
        }
    }
}
