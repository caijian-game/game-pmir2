<?php

declare (strict_types = 1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf-cloud/hyperf/blob/master/LICENSE
 */

namespace App\Model\Common;

use Hyperf\DbConnection\Db;
use Hyperf\DbConnection\Model\Model;
use Hyperf\ModelCache\Cacheable;
use Hyperf\ModelCache\CacheableInterface;

class CommonModel extends Model implements CacheableInterface
{
    use Cacheable;

    /**
     * [dbConnection 兼容多库]
     * ------------------------------------------------------------------------------
     * @author  github
     * ------------------------------------------------------------------------------
     * @version date:2100-01-01
     * ------------------------------------------------------------------------------
     * @param   [type]          $dbDatabase [description]
     * @return  [type]                       [description]
     */
    public function dbConnection($table, $dbDatabase)
    {
        if ($dbDatabase) {
            $tableObject = Db::connection($dbDatabase)->table($table);
        } else {
            $tableObject = Db::table($table);
        }

        return $tableObject;
    }

    /**
     *
     * [getList 列表]
     * ------------------------------------------------------------------------------
     * @author github
     * ------------------------------------------------------------------------------
     * @version date:2100-01-01
     * ------------------------------------------------------------------------------
     * @param   [type]          $table        [表名称]
     * @param   [type]          $field        [字段]
     * @param   [type]          $join         [连表查询]
     * @param   [type]          $where        [条件查询]
     * @param   [type]          $orderBy      [排序]
     * @param   [type]          $page         [开始页]
     * @return  [type]                        [条数,数据]
     */
    public function getList($table, $field, $join, $where, $orwhere, $orderBy, $groupBy = null, $page, $dbDatabase = null)
    {
        try {
            $tableObject = $this->dbConnection($table, $dbDatabase);

            //条件查询
            $tableObject = $this->wherePackage($tableObject, $where);

            //或条件查询
            $tableObject = $this->orwherePackage($tableObject, $orwhere);

            //查询字段
            $tableObject->select($field);

            //连表查询
            $tableObject = $this->joinPackge($tableObject, $join);

            if ($groupBy) {
                $tableObject = $this->groupPackge($tableObject, $groupBy);
            }

            //列表,总量对象
            $listObject = $countObject = $tableObject;

            //排序构建
            foreach ($orderBy as $k => $v) {
                $listObject = $listObject->orderBy($k, $v);
            }

            //总量
            $total = $countObject->count('*');

            //分页
            $listObject = $this->pagePackge($listObject, $page);

            //列表
            $list = $listObject->get();
            $list = $this->objectToArray($list);

            return ['total' => $total, 'list' => $list];
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * [orwherePackage 或条件构造]
     * ------------------------------------------------------------------------------
     * @author github
     * ------------------------------------------------------------------------------
     * @version date:2100-01-01
     * ------------------------------------------------------------------------------
     * @param   [type]          $object  [description]
     * @param   [type]          $orwhere [description]
     * @param   array           $where   [description]
     * @return  [type]                   [description]
     */
    public function orwherePackage($object, $orwhere)
    {
        $object->orWhere(function ($query) use ($orwhere) {
            foreach ($orwhere as $k => $v) {
                switch ($k) {
                    case 'where':
                        $query->where($v);
                        break;

                    case 'whereIn':
                        foreach ($v as $k1 => $v1) {
                            $query->whereIn($v1[0], $v1[1]);
                        }
                        break;

                    case 'whereBetween':
                        //构建区间对象条件
                        foreach ($v as $k1 => $v1) {
                            $query = $this->objBetween($query, $v1);
                        }
                        break;
                }
            }
        });

        return $object;
    }

    /**
     * [getOne 获取详情]
     * ------------------------------------------------------------------------------
     * @author github
     * ------------------------------------------------------------------------------
     * @version date:2100-01-01
     * ------------------------------------------------------------------------------
     * @param   [type]          $table        [表名称]
     * @param   [type]          $field        [字段]
     * @param   [type]          $join         [连表查询]
     * @param   [type]          $where        [条件查询]
     * @return  [type]                 [description]
     */
    public function getOne($table, $field, $join, $where, $orderBy, $dbDatabase = null)
    {
        try {
            $tableObject = $this->dbConnection($table, $dbDatabase);

            //条件查询
            $tableObject = $this->wherePackage($tableObject, $where);

            //查询字段
            $tableObject->select($field);

            //连表查询
            $tableObject = $this->joinPackge($tableObject, $join);

            //排序构建
            foreach ($orderBy as $k => $v) {
                $tableObject = $tableObject->orderBy($k, $v);
            }

            $info = $tableObject->get();

            $info = $this->objectToArray($info);

            return $info ? ['code' => 2000, 'msg' => '获取成功', 'data' => $info[0]] : ['code' => 4000, 'msg' => '获取失败', 'data' => $info];
        } catch (Exception $e) {
            return ['code' => 5000, 'msg' => '服务器异常', 'data' => []];
        }
    }

    /**
     * [objectToArray 对象转数组]
     * ------------------------------------------------------------------------------
     * @author github
     * ------------------------------------------------------------------------------
     * @version date:2100-01-01
     * ------------------------------------------------------------------------------
     * @param   [type]          $resultObject [数据对象]
     * @return  [type]                        [description]
     */
    public function objectToArray($resultObject)
    {
        return json_decode(json_encode($resultObject), true);
    }

    /**
     * [joinPackge 连表查询构造器]
     * ------------------------------------------------------------------------------
     * @author github
     * ------------------------------------------------------------------------------
     * @version date:2100-01-01
     * ------------------------------------------------------------------------------
     * @return  [type]          [description]
     */
    public function joinPackge($object, $join)
    {
        //连表查询
        foreach ($join as $k => $v) {
            switch ($v[0]) {
                case 'left':
                    if (is_array($v[2])) 
                    {
                        $object->leftJoin($v[1], function ($query) use ($v) {
                            foreach ($v[2] as $k1 => $v1) 
                            {
                                $query->on($v1[0], $v1[1], $v1[2]);
                            }
                        });
                    } else {
                        $object->leftJoin($v[1], $v[2], $v[3], $v[4]);
                    }
                    break;

                case 'right':
                    if (is_array($v[2])) 
                    {
                        $object->rightJoin($v[1], function ($query) use ($v) {
                            foreach ($v[2] as $k1 => $v1) 
                            {
                                $query->on($v1[0], $v1[1], $v1[2]);
                            }
                        });
                    } else {
                        $object->rightJoin($v[1], $v[2], $v[3], $v[4]);
                    }
                    break;

                case 'inner':
                    if (is_array($v[2])) 
                    {
                        $object->join($v[1], function ($query) use ($v) {
                            foreach ($v[2] as $k1 => $v1) 
                            {
                                $query->on($v1[0], $v1[1], $v1[2]);
                            }
                        });
                    } else {
                        $object->join($v[1], $v[2], $v[3], $v[4]);
                    }
                    break;
            }
        }

        return $object;
    }

    /**
     * [wherePackage 条件构造器]
     * ------------------------------------------------------------------------------
     * @author github
     * ------------------------------------------------------------------------------
     * @version date:2100-01-01
     * ------------------------------------------------------------------------------
     * @return  [type]          [description]
     */
    public function wherePackage($object, $where)
    {
        foreach ($where as $k => $v) {
            switch ($k) {
                case 'where':
                    $object->where($v);
                    break;

                case 'whereIn':
                    foreach ($v as $k1 => $v1) {
                        $object->whereIn($v1[0], $v1[1]);
                    }
                    break;

                case 'whereBetween':
                    //构建区间对象条件
                    foreach ($v as $k1 => $v1) {
                        $object = $this->objBetween($object, $v1);
                    }
                    break;
            }
        }

        return $object;
    }

    /**
     * [objBetween 区间查询构建对象]
     * ------------------------------------------------------------------------------
     * @author github
     * ------------------------------------------------------------------------------
     * @version date:2100-01-01
     * ------------------------------------------------------------------------------
     * @param   [type]          $obj          [description]
     * @param   [type]          $whereBetween [description]
     * @return  [type]                        [description]
     */
    public function objBetween($object, $whereBetween)
    {
        if (empty($object)) {
            return $object;
        }

        if (isset($whereBetween[0]) && isset($whereBetween[1])) {
            $object = $object->whereBetween($whereBetween[0], $whereBetween[1]);
        }

        return $object;
    }

    /**
     * [pagePackge 分页构造器]
     * ------------------------------------------------------------------------------
     * @author github
     * ------------------------------------------------------------------------------
     * @version date:2100-01-01
     * ------------------------------------------------------------------------------
     * @param   [type]          $object [对象]
     * @param   [type]          $join   [分页信息]
     * @return  [type]                  [description]
     */
    public function pagePackge($object, $page)
    {
        if ($page) {
            $start = ($page['page'] - 1) * $page['pagesize'];
            $limit = $page['pagesize'];
            return $object->offset($start)->limit($limit);
        } else {
            return $object;
        }
    }

    /**
     * [upField 更新字段]
     * ------------------------------------------------------------------------------
     * @author github
     * ------------------------------------------------------------------------------
     * @version date:2100-01-01
     * ------------------------------------------------------------------------------
     * @param   [type]          $table [表名称]
     * @param   [type]          $where [条件]
     * @return  [type]                 [description]
     */
    public function upField($table, $where, $param = [], $dbDatabase = null)
    {
        try {
            $tableObject = $this->dbConnection($table, $dbDatabase);

            $tableObject = $this->wherePackage($tableObject, $where);

            $info = $tableObject->update($param);

            return $info !== false ? ['code' => 2000, 'msg' => '操作成功', 'data' => []] : ['code' => 4000, 'msg' => '操作失败', 'data' => []];
        } catch (Exception $e) {
            return ['code' => 5000, 'msg' => '服务器异常', 'data' => []];
        }
    }

    /**
     * [delTrue 删除数据]
     * ------------------------------------------------------------------------------
     * @author github
     * ------------------------------------------------------------------------------
     * @version date:2100-01-01
     * ------------------------------------------------------------------------------
     * @param   [type]          $table [description]
     * @param   [type]          $where [description]
     * @return  [type]                 [description]
     */
    public function delTrue($table, $where, $dbDatabase = null)
    {
        try {
            $tableObject = $this->dbConnection($table, $dbDatabase);

            $tableObject = $this->wherePackage($tableObject, $where);

            $info = $tableObject->delete();

            return $info !== false ? ['code' => 2000, 'msg' => '操作成功', 'data' => []] : ['code' => 4000, 'msg' => '操作失败', 'data' => []];
        } catch (Exception $e) {
            return ['code' => 5000, 'msg' => '服务器异常', 'data' => []];
        }
    }

    /**
     * [addEdit 自定义保存]
     * ------------------------------------------------------------------------------
     * @author  github
     * ------------------------------------------------------------------------------
     * @version date:2100-01-01
     * ------------------------------------------------------------------------------
     * @param   [type]          $table      [description]
     * @param   [type]          $param      [description]
     * @param   [type]          $primaryKey [description]
     * @param   [type]          $dbDatabase [description]
     * @return  [type]                      [description]
     */
    public function addEdit($table, $param, $primaryKey, $dbDatabase = null)
    {
        try {
            $tableObject = $this->dbConnection($table, $dbDatabase);

            if (!empty($param[$primaryKey])) {
                //更新
                $where   = [];
                $where[] = [$primaryKey, '=', $param[$primaryKey]];
                $info    = $tableObject->where($where)->update($param);
                $msg     = '更新';
            } else {
                $info               = $tableObject->insertGetId($param);
                $msg                = '添加';
                $param[$primaryKey] = $info;
            }

            return $info !== false ? ['code' => 2000, 'msg' => $msg . '成功', 'data' => [$primaryKey => $param[$primaryKey]]] : ['code' => 4000, 'msg' => $msg . '失败', 'data' => [$primaryKey => $param[$primaryKey]]];
        } catch (Exception $e) {
            return ['code' => 5000, 'msg' => '服务器异常', 'data' => []];
        }
    }

    /**
     * [saveId uuid字段不存在 保存]
     * ------------------------------------------------------------------------------
     * @author github
     * ------------------------------------------------------------------------------
     * @version date:2100-01-01
     * ------------------------------------------------------------------------------
     * @param   [type]          $table [表名称]
     * @param   [type]          $param [参数]
     * @return  [type]                 [description]
     */
    public function saveId($table, $param, $dbDatabase = null)
    {
        try {
            $tableObject = $this->dbConnection($table, $dbDatabase);

            if (!empty($param['id'])) {
                //更新
                $where   = [];
                $where[] = ['id', '=', $param['id']];
                $info    = $tableObject->where($where)->update($param);
                $msg     = '更新';
            } else {
                $info        = $tableObject->insertGetId($param);
                $msg         = '添加';
                $param['id'] = $info;
            }

            return $info !== false ? ['code' => 2000, 'msg' => $msg . '成功', 'data' => ['id' => $param['id']]] : ['code' => 4000, 'msg' => $msg . '失败', 'data' => ['id' => $param['id']]];
        } catch (Exception $e) {
            return ['code' => 5000, 'msg' => '服务器异常', 'data' => []];
        }
    }

    /**
     * [saveUuid 保存]
     * ------------------------------------------------------------------------------
     * @author github
     * ------------------------------------------------------------------------------
     * @version date:2100-01-01
     * ------------------------------------------------------------------------------
     * @param   [type]          $table [表名称]
     * @param   [type]          $param [参数]
     * @return  [type]                 [description]
     */
    public function saveUuid($table, $param, $dbDatabase = null)
    {
        try {

            $tableObject = $this->dbConnection($table, $dbDatabase);

            if (!empty($param['uuid'])) {
                //更新
                $where   = [];
                $where[] = ['uuid', '=', $param['uuid']];
                $info    = $tableObject->where($where)->update($param);
                $msg     = '更新';
            } else {
                $param['uuid'] = getuuid();
                $info          = $tableObject->insert($param);
                $msg           = '添加';
            }

            return $info !== false ? ['code' => 2000, 'msg' => $msg . '成功', 'data' => ['uuid' => $param['uuid']]] : ['code' => 4000, 'msg' => $msg . '失败', 'data' => ['uuid' => $param['uuid']]];
        } catch (Exception $e) {
            return ['code' => 5000, 'msg' => '服务器异常', 'data' => []];
        }
    }

    /**
     * [saveAll 批量添加]
     * ------------------------------------------------------------------------------
     * @author github
     * ------------------------------------------------------------------------------
     * @version date:2100-01-01
     * ------------------------------------------------------------------------------
     * @param   [type]          $table [description]
     * @param   [type]          $param [description]
     * @return  [type]                 [description]
     */
    public function saveAll($table, $param, $dbDatabase = null)
    {
        try {
            $tableObject = $this->dbConnection($table, $dbDatabase);

            $info = $tableObject->insert($param);

            return $info !== false ? ['code' => 2000, 'msg' => '添加成功', 'data' => []] : ['code' => 4000, 'msg' => '添加失败', 'data' => []];

        } catch (Exception $e) {
            return ['code' => 5000, 'msg' => '服务器异常', 'data' => []];
        }
    }

    /**
     * [getCount 列表]
     * ------------------------------------------------------------------------------
     * @author
     * ------------------------------------------------------------------------------
     * @version date:2019-06-10
     * ------------------------------------------------------------------------------
     * @param   [type]          $table        [表名称]
     * @param   [type]          $field        [字段]
     * @param   [type]          $join         [连表查询]
     * @param   [type]          $where        [条件查询]
     * @param   [type]          $orderBy      [排序]
     * @param   [type]          $page         [开始页]
     * @return  [type]                        [条数,数据]
     */
    public function getCount($table, $field, $join, $where, $orwhere, $orderBy, $groupBy = null, $page, $dbDatabase = null)
    {
        try {

            $tableObject = $this->dbConnection($table, $dbDatabase);

            //条件查询
            $tableObject = $this->wherePackage($tableObject, $where);

            //或条件查询
            $tableObject = $this->orwherePackage($tableObject, $orwhere);

            //查询字段
            $tableObject->select($field);

            //连表查询
            $tableObject = $this->joinPackge($tableObject, $join);

            if ($groupBy) {
                $tableObject = $this->groupPackge($tableObject, $groupBy);
            }

            //列表,总量对象
            $countObject = $tableObject;

            //总量
            $total = $countObject->count('*');

            return $total;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * [groupPackge 分组查询构造器]
     * ------------------------------------------------------------------------------
     * @author github
     * ------------------------------------------------------------------------------
     * @version date:2100-01-01
     * ------------------------------------------------------------------------------
     * @param   [type]          $object  [description]
     * @param   [type]          $groupBy [description]
     * @return  [type]                   [description]
     */
    public function groupPackge($object, $groupBy)
    {
        $object->groupBy($groupBy);

        return $object;
    }

    /**
     * [execSql 执行原始sql]
     * ------------------------------------------------------------------------------
     * @author  github
     * ------------------------------------------------------------------------------
     * @version date:2100-01-01
     * ------------------------------------------------------------------------------
     * @return  [type]          [description]
     */
    public function execSql($sql, $dbDatabase = null)
    {
        try {
            return $this->objectToArray(Db::connection($dbDatabase)->select($sql));
        } catch (Exception $e) {
            return false;
        }
    }

}
