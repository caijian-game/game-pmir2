<?php
namespace App\Controller\Game;

/**
 *
 */
class GameData
{
    public function loadGameData()
    {
        getObject('Redis')->flushDB(); //清空当前库全部缓存

        $gameShopItems = $this->gameShopItems();
        $itemInfos     = $this->itemInfos();
        $magicInfos    = $this->magicInfos();
        $mapInfos      = $this->mapInfos();
        $monsterInfos  = $this->monsterInfos();
        $movementInfos = $this->movementInfos();
        $npcInfos      = $this->npcInfos();
        $questInfos    = $this->questInfos();
        $respawnInfos  = $this->respawnInfos();
        $safeZoneInfos = $this->safeZoneInfos();

        EchoLog(sprintf('数据初始化加载 商品:%s 物品:%s 技能:%s 地图:%s 怪物:%s 怪物巡逻:%s NPC:%s 任务:%s 重新生成:%s 安全区:%s',
            $gameShopItems['total'],
            $itemInfos['total'],
            $magicInfos['total'],
            $mapInfos['total'],
            $monsterInfos['total'],
            $movementInfos['total'],
            $npcInfos['total'],
            $questInfos['total'],
            $respawnInfos['total'],
            $safeZoneInfos['total']
        ), null, true);

        $this->loadMonsterDrop($monsterInfos['list']); //怪物掉落

        $this->loadExpList(); //升级经验
    }

    public function gameShopItems()
    {
        $where = [
            'pageInfo' => false,
        ];
        $res = getObject('CommonService')->getList('game_shop_item', $where);

        getObject('Redis')->set('gameShopItems', json_encode($res['list'], JSON_UNESCAPED_UNICODE));
        return $res;
    }

    public function itemInfos()
    {
        $where = [
            'pageInfo' => false,
        ];
        $res = getObject('CommonService')->getList('item', $where);

        $startItems      = [];
        $itemIDInfoMap   = [];
        $itemNameInfoMap = [];

        if ($res['list']) {
            foreach ($res['list'] as $k => $v) {

                $v['is_tool_tip'] = false;

                if (($v['bools'] & 0x04) == 0x04) {
                    $v['class_based'] = true;
                } else {
                    $v['class_based'] = false;
                }

                if (($v['bools'] & 0x08) == 0x08) {
                    $v['level_based'] = true;
                } else {
                    $v['level_based'] = false;
                }

                if ($v['start_item']) {
                    $startItems[] = $v;
                }

                $itemIDInfoMap[$v['id']]     = $v;
                $itemNameInfoMap[$v['name']] = $v;
            }
        }

        $Redis = getObject('Redis');
        $Redis->set('itemInfos', json_encode($res['list'], JSON_UNESCAPED_UNICODE));

        $Redis->set('startItems', json_encode($startItems, JSON_UNESCAPED_UNICODE));

        $Redis->set('itemIDInfoMap', json_encode($itemIDInfoMap, JSON_UNESCAPED_UNICODE));

        $Redis->set('itemNameInfoMap', json_encode($itemNameInfoMap, JSON_UNESCAPED_UNICODE));

        return $res;
    }

    public function magicInfos()
    {
        $where = [
            'pageInfo' => false,
        ];
        $res = getObject('CommonService')->getList('magic', $where);

        $magicIDInfoMap = [];

        if ($res['list']) {
            foreach ($res['list'] as $k => $v) {
                $magicIDInfoMap[$v['id']] = $v;
            }
        }
        $Redis = getObject('Redis');
        $Redis->set('magicInfos', json_encode($res['list'], JSON_UNESCAPED_UNICODE));

        $Redis->set('magicIDInfoMap', json_encode($magicIDInfoMap, JSON_UNESCAPED_UNICODE));

        return $res;
    }

    public function mapInfos()
    {
        $where = [
            'pageInfo' => false,
        ];
        $res = getObject('CommonService')->getList('map', $where);

        $mapIDInfoMap = [];
        $Maps         = [];

        $path = config('settings_path');

        if ($res['list']) {
            $uppercaseNameRealNameMap = [];
            $file                     = new \FilesystemIterator($path . '/Maps/');

            foreach ($file as $fileinfo) {
                $filename                                        = $fileinfo->getFilename();
                $filepath                                        = $path . '/Maps/' . $filename;
                $uppercaseNameRealNameMap[strtoupper($filename)] = $filepath;
            }

            $stime = microtime(true);

            $num = 0;

            $MapLoader = getObject('MapLoader');
            $Redis     = getObject('Redis');

            foreach ($res['list'] as $k => $v) {

                $mapIDInfoMap[$v['id']] = $v;

                //加载地图数据
                $m              = $MapLoader->loadMap($uppercaseNameRealNameMap[strtoupper(trim($v['file_name']) . ".map")]);
                $v['file_name'] = strtoupper(trim($v['file_name']));
                $m['Info']      = $v;

                $Maps[$v['id']] = $m;

                //生成地图缓存
                $players_key  = 'map:players_' . $v['id'];
                $npcs_key     = 'map:npcs_' . $v['id'];
                $monsters_key = 'map:monsters_' . $v['id'];

                $Redis->set($players_key, json_encode([], JSON_UNESCAPED_UNICODE));
                $Redis->set($npcs_key, json_encode([], JSON_UNESCAPED_UNICODE));
                $Redis->set($monsters_key, json_encode([], JSON_UNESCAPED_UNICODE));
            }

            $etime = microtime(true);
            $total = $etime - $stime;
            EchoLog(sprintf(PHP_EOL . '加载完成 %s', $total));
        }

        // $Redis->set('mapInfos', json_encode($res['list'], JSON_UNESCAPED_UNICODE));

        $Redis->set('Maps', json_encode($Maps, JSON_UNESCAPED_UNICODE));

        $Redis->set('mapIDInfoMap', json_encode($mapIDInfoMap, JSON_UNESCAPED_UNICODE));

        return $res;
    }

    public function monsterInfos()
    {
        $where = [
            'pageInfo' => false,
        ];
        $res = getObject('CommonService')->getList('monster', $where);

        $monsterIDInfoMap   = [];
        $monsterNameInfoMap = [];

        if ($res['list']) {
            foreach ($res['list'] as $k => $v) {
                $monsterIDInfoMap[$v['id']]     = $v;
                $monsterNameInfoMap[$v['name']] = $v;
            }
        }

        $Redis = getObject('Redis');
        $Redis->set('monsterInfos', json_encode($res['list'], JSON_UNESCAPED_UNICODE));

        $Redis->set('monsterIDInfoMap', json_encode($monsterIDInfoMap, JSON_UNESCAPED_UNICODE));

        $Redis->set('monsterNameInfoMap', json_encode($monsterNameInfoMap, JSON_UNESCAPED_UNICODE));

        return $res;
    }

    public function movementInfos()
    {
        $where = [
            'pageInfo' => false,
        ];
        $res = getObject('CommonService')->getList('movement', $where);

        getObject('Redis')->set('movementInfos', json_encode($res['list'], JSON_UNESCAPED_UNICODE));

        return $res;
    }

    public function npcInfos()
    {
        $where = [
            'pageInfo' => false,
        ];
        $res = getObject('CommonService')->getList('npc', $where);

        getObject('Redis')->set('npcInfos', json_encode($res['list'], JSON_UNESCAPED_UNICODE));

        return $res;
    }

    public function questInfos()
    {
        $where = [
            'pageInfo' => false,
        ];
        $res = getObject('CommonService')->getList('quest', $where);

        getObject('Redis')->set('questInfos', json_encode($res['list'], JSON_UNESCAPED_UNICODE));

        return $res;
    }

    public function respawnInfos()
    {
        $where = [
            'pageInfo' => false,
        ];
        $res = getObject('CommonService')->getList('respawn', $where);

        getObject('Redis')->set('respawnInfos', json_encode($res['list'], JSON_UNESCAPED_UNICODE));

        return $res;
    }

    public function safeZoneInfos()
    {
        $where = [
            'pageInfo' => false,
        ];
        $res = getObject('CommonService')->getList('safe_zone', $where);

        $startPoints = [];

        if ($res['list']) {
            foreach ($res['list'] as $k => $v) {
                if ($v['start_point']) {
                    $startPoints[] = $v;
                }
            }
        }
        $Redis = getObject('Redis');
        $Redis->set('startPoints', json_encode($startPoints, JSON_UNESCAPED_UNICODE));

        $Redis->set('safeZoneInfos', json_encode($res['list'], JSON_UNESCAPED_UNICODE));

        return $res;
    }

    public function loadMonsterDrop($monsterInfos)
    {
        $dropInfoMap = [];
        $path        = config('settings_path');
        foreach ($monsterInfos as $k => $v) {
            $dropInfos = $this->loadDropFile($path . '/Envir/Drops/' . $v['name'] . ".txt");
            if ($dropInfos) {
                $dropInfoMap[$v['name']] = $dropInfos;
            }
        }

        getObject('Redis')->set('dropInfoMap', json_encode($dropInfoMap, JSON_UNESCAPED_UNICODE));
    }

    public function loadDropFile($file = '')
    {
        if (!$fp = fopen($file, 'r')) {
            EchoLog(sprintf('打开文件失败 :%s', $file), 'e');
            return false;
        }

        $data = [];

        $num = 1;
        while (!feof($fp)) {
            $content = trim(stream_get_line($fp, 1024, "\n"));
            // $content = trim(fgets($fp, 1024));
            if (!$content || $content == ' ' || strpos($content, ';') !== false) {
                continue;
            }

            $txt = preg_replace("/\s(?=\s)/", "\\1", $content);

            $content = explode(' ', $txt);

            if (count($content) != 3 && count($content) != 2) {
                EchoLog(sprintf('参数错误 content: %s; %s line: %s; ', $txt, $file, $num), 'e');
                return false;
            }

            $dropRate = explode('/', $content[0]);

            if (!intval($dropRate[0]) || $dropRate[0] <= 0) {
                EchoLog(sprintf('掉落分子错误 content: %s; %s line: %s; ', $txt, $file, $num), 'e');
                return false;
            }

            if (!intval($dropRate[1]) || $dropRate[1] <= 0) {
                EchoLog(sprintf('掉落分母错误 content: %s; %s line: %s; ', $txt, $file, $num), 'e');
                return false;
            }

            $info = [
                'Low'           => $dropRate[0],
                'High'          => $dropRate[1],
                'ItemName'      => $content[1],
                'QuestRequired' => false,
                'Count'         => 1,
            ];

            if (count($content) == 3) {
                if (strtoupper($content[2]) == 'Q') {
                    $info['QuestRequired'] = true;
                } else {
                    $info['Count'] = intval($content[2]);
                    if (!$info['Count']) {
                        EchoLog(sprintf('掉落数量错误 content: %s; %s line: %s; ', $txt, $file, $num), 'e');
                        return false;
                    }
                }
            }

            $data[] = $info;

            $num++;
        }

        fclose($fp);

        return $data;
    }

    public function loadExpList()
    {
        $file = config('settings_path') . '/Configs/ExpList.ini';

        if (!$fp = fopen($file, 'r')) {
            EchoLog(sprintf('打开文件失败 :%s', $file), 'e');
            return false;
        }

        $data = [];

        $num = 1;
        while (!feof($fp)) {
            $txt = trim(stream_get_line($fp, 1024, "\n"));

            if (!$txt || $txt == ' ' || strpos($txt, 'Exp') !== false) {
                continue;
            }

            $content = str_replace('Level', '', $txt);
            $content = explode('=', $content);

            if (!intval($content[0]) || $content[0] <= 0) {
                EchoLog(sprintf('等级设置错误 content: %s; %s line: %s; ', $txt, $file, $num), 'e');
                return false;
            }

            if (!intval($content[1]) || $content[1] <= 0) {
                EchoLog(sprintf('经验设置错误 content: %s; %s line: %s; ', $txt, $file, $num), 'e');
                return false;
            }

            $data[(int) $content[0] - 1] = $content[1];

            $num++;
        }

        getObject('Redis')->set('expList', json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    public function randomStartPoint()
    {
        $startPoints = json_decode(getObject('Redis')->get('startPoints'), true);

        return $startPoints[mt_rand(0, count($startPoints) - 1)];
    }

    public function getMagicInfoByID($magic_id)
    {
        $magicIDInfoMap = json_decode(getObject('Redis')->get('magicIDInfoMap'));

        return $magicIDInfoMap[$magic_id] ?: [];
    }

    public function getExpList($level = null)
    {
        $expList = json_decode(getObject('Redis')->get('expList'), true);
        return $level == null ? $expList : $expList[$level] ?: 1;
    }

    public function getStartItems()
    {
        return json_decode(getObject('Redis')->get('startItems'), true);
    }

    public function getItemInfoByID($ItemID)
    {
        $itemIDInfoMap = json_decode(getObject('Redis')->get('itemIDInfoMap'), true);
        return $itemIDInfoMap[$ItemID] ?: null;
    }

    public function getMap($mapId)
    {
        $Maps = json_decode(getObject('Redis')->get('Maps'), true);
        return $Maps[$mapId] ?: null;
    }

    public function getItemInfos()
    {
        return json_decode(getObject('Redis')->get('itemInfos'), true);
    }

    public function getRealItem($origin, $level, $job, $itemList)
    {
        if (!empty($origin['class_based']) && !empty($origin['level_based'])) {
            return $this->getClassAndLevelBasedItem($origin, $job, $level, $itemList);
        }
        if (!empty($origin['class_based'])) {
            return $this->getClassBasedItem($origin, $job, $itemList);
        }
        if (!empty($origin['level_based'])) {
            return $this->getLevelBasedItem($origin, $level, $itemList);
        }

        return $origin;
    }

    public function getClassAndLevelBasedItem($origin, $job, $level, $itemList)
    {
        $output = $origin;
        $Enum   = getObject('Enum');

        for ($i = 0; $i < count($itemList); $i++) {
            $info = $itemList[$i];
            if (strpos($info['Name'], $origin['Name']) === 0) {

                if ($info['required_class'] == (1 << $job)) {
                    if ($info['required_type'] == $Enum::RequiredTypeLevel && $info['required_amount'] <= $level && $output['required_amount'] <= $info['required_amount'] && $origin['required_gender'] == $info['required_gender']) {
                        $output = $info;
                    }
                }
            }
        }
        return $output;
    }

    public function getClassBasedItem($origin, $job, $itemList)
    {
        for ($i = 0; $i < count($itemList); $i++) {
            $info = $itemList[$i];
            if (strpos($info['Name'], $origin['Name']) === 0) {
                if ($info['required_class'] == (1 << $job) && $origin['required_gender'] == $info['required_gender']) {
                    return $info;
                }
            }
        }
        return $origin;
    }

    public function getLevelBasedItem($origin, $level, $itemList)
    {
        $output = $origin;
        $Enum   = getObject('Enum');

        for ($i = 0; $i < count($itemList); $i++) {
            $info = $itemList[$i];
            if (strpos($info['Name'], $origin['Name']) === 0) {
                if ($info['RequiredType'] == $Enum::RequiredTypeLevel && $info['RequiredAmount'] <= $level && $output['RequiredAmount'] < $info['RequiredAmount'] && $origin['RequiredGender'] == $info['RequiredGender']) {
                    $output = $info;
                }
            }
        }
        return $output;
    }

    public function getMapPlayers($map_id)
    {
        $key = 'map:players_' . $map_id;
        return json_decode(getObject('Redis')->get($key), true);
    }

    public function setMapPlayers($map_id, $p)
    {
        $key = 'map:players_' . $map_id;

        $mapPlayers   = $this->getMapPlayers($map_id);
        $mapPlayers[] = $p;

        getObject('Redis')->set($key, json_encode($mapPlayers, JSON_UNESCAPED_UNICODE));
    }
}
