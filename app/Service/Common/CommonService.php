<?php

declare (strict_types = 1);
/**
 * 服务层
 */
namespace App\Service\Common;

use App\Model\Common\CommonModel;
use Hyperf\Di\Annotation\Inject;

class CommonService
{
    /**
     * @Inject
     * @var CommonModel
     */
    protected $CommonModel;

    /**
     * [getList description]
     * ------------------------------------------------------------------------------
     * @author  github
     * ------------------------------------------------------------------------------
     * @version date:2100-01-01
     * ------------------------------------------------------------------------------
     * @param   string          $table       [description]
     * @param   array           $param       [description]
     * @param   [type]          $dbDatabase [description]
     * @return  [type]                       [description]
     */
    public function getList($table = '', $param = [], $dbDatabase = null)
    {
        $field   = !empty($param['field']) ? $param['field'] : '*';
        $join    = !empty($param['join']) ? $param['join'] : [];
        $where   = !empty($param['whereInfo']) ? $param['whereInfo'] : [];
        $orwhere = !empty($param['orwhereInfo']) ? $param['orwhereInfo'] : [];
        $orderBy = !empty($param['orderBy']) ? $param['orderBy'] : [];
        $groupBy = !empty($param['groupBy']) ? $param['groupBy'] : null;

        if (empty($param['pageInfo'])) {
            $page = [];

            if (!isset($param['pageInfo'])) {
                $page = ['page' => 1, 'pagesize' => 10];
            }
        } else {
            $page             = [];
            $page['page']     = !empty($param['pageInfo']['page']) ? $param['pageInfo']['page'] : 1;
            $page['pagesize'] = !empty($param['pageInfo']['pagesize']) ? $param['pageInfo']['pagesize'] : 10;
        }

        if (!$table) {
            return ['code' => 5000, 'msg' => '服务异常,表名称错误', 'data' => []];
        }

        return $this->CommonModel->getList($table, $field, $join, $where, $orwhere, $orderBy, $groupBy, $page, $dbDatabase);
    }

    /**
     * [getCount 获取总数量(此方法不适用分页的长度获取,getList中自带长度获取)]
     * ------------------------------------------------------------------------------
     * @author  github
     * ------------------------------------------------------------------------------
     * @version date:2100-01-01
     * ------------------------------------------------------------------------------
     * @param   string          $table       [description]
     * @param   array           $param       [description]
     * @param   [type]          $DB_DATABASE [description]
     * @return  [type]                       [description]
     */
    public function getCount($table = '', $param = [], $dbDatabase = null)
    {
        $field   = !empty($param['field']) ? $param['field'] : '*';
        $join    = !empty($param['join']) ? $param['join'] : [];
        $where   = !empty($param['whereInfo']) ? $param['whereInfo'] : [];
        $orwhere = !empty($param['orwhereInfo']) ? $param['orwhereInfo'] : [];
        $orderBy = !empty($param['orderBy']) ? $param['orderBy'] : [];
        $groupBy = !empty($param['groupBy']) ? $param['groupBy'] : null;
        $page    = [];

        if (!$table) {
            return ['code' => 5000, 'msg' => '服务异常,表名称错误', 'data' => []];
        }
        return $this->CommonModel->getCount($table, $field, $join, $where, $orwhere, $orderBy, $groupBy, $page, $dbDatabase);
    }

    /**
     * [saveUuid 主键uuid保存编辑]
     * ------------------------------------------------------------------------------
     * @author  github
     * ------------------------------------------------------------------------------
     * @version date:2100-01-01
     * ------------------------------------------------------------------------------
     * @param   string          $table [description]
     * @param   array           $param [description]
     * @return  [type]                 [description]
     */
    public function saveUuid($table = '', $param = [], $dbDatabase = null)
    {
        if (!$table) {
            return ['code' => 5000, 'msg' => '服务异常,表名称错误', 'data' => []];
        }

        if (!$param) {
            return ['code' => 3000, 'msg' => '参数异常', 'data' => []];
        }

        return $this->CommonModel->saveUuid($table, $param, $dbDatabase);
    }

    /**
     * [saveId 主键id保存编辑]
     * ------------------------------------------------------------------------------
     * @author  github
     * ------------------------------------------------------------------------------
     * @version date:2100-01-01
     * ------------------------------------------------------------------------------
     * @param   string          $table [description]
     * @param   array           $param [description]
     * @return  [type]                 [description]
     */
    public function saveId($table = '', $param = [], $dbDatabase = null)
    {
        if (!$table) {
            return ['code' => 5000, 'msg' => '服务异常,表名称错误', 'data' => []];
        }

        if (!$param) {
            return ['code' => 3000, 'msg' => '参数异常', 'data' => []];
        }

        return $this->CommonModel->saveId($table, $param, $dbDatabase);
    }

    /**
     * [save 保存 自定义主键保存]
     * ------------------------------------------------------------------------------
     * @author  github
     * ------------------------------------------------------------------------------
     * @version date:2100-01-01
     * ------------------------------------------------------------------------------
     * @param   string          $table [description]
     * @param   array           $param [description]
     * @return  [type]                 [description]
     */
    public function save($table = '', $param = [], $primaryKey = 'id', $dbDatabase = null)
    {
        if (!$table) {
            return ['code' => 5000, 'msg' => '服务异常,表名称错误', 'data' => []];
        }

        if (!$param) {
            return ['code' => 3000, 'msg' => '参数异常', 'data' => []];
        }

        return $this->CommonModel->addEdit($table, $param, $primaryKey, $dbDatabase);
    }

    /**
     * [saveAll 批量添加]
     * ------------------------------------------------------------------------------
     * @author github
     * ------------------------------------------------------------------------------
     * @version date:2100-01-01
     * ------------------------------------------------------------------------------
     * @param   string          $table [description]
     * @param   array           $param [description]
     * @return  [type]                 [description]
     */
    public function saveAll($table = '', $param = [], $dbDatabase = null)
    {
        if (!$table) {
            return ['code' => 5000, 'msg' => '服务异常,表名称错误', 'data' => []];
        }

        if (!$param) {
            return ['code' => 3000, 'msg' => '参数异常', 'data' => []];
        }

        return $this->CommonModel->saveAll($table, $param, $dbDatabase);
    }

    /**
     * [getOne 获取详情]
     * ------------------------------------------------------------------------------
     * @author github
     * ------------------------------------------------------------------------------
     * @version date:2100-01-01
     * ------------------------------------------------------------------------------
     * @param   array           $param [description]
     * @return  [type]                 [description]
     */
    public function getOne($table, $param = [], $dbDatabase = null)
    {
        $field   = !empty($param['field']) ? $param['field'] : '*';
        $join    = !empty($param['join']) ? $param['join'] : [];
        $where   = !empty($param['whereInfo']) ? $param['whereInfo'] : [];
        $orderBy = !empty($param['orderBy']) ? $param['orderBy'] : [];

        if (!$table) {
            return ['code' => 5000, 'msg' => '服务异常,表名称错误', 'data' => []];
        }

        return $this->CommonModel->getOne($table, $field, $join, $where, $orderBy, $dbDatabase);
    }

    /**
     * [del 假删除]
     * ------------------------------------------------------------------------------
     * @author github
     * ------------------------------------------------------------------------------
     * @version date:2100-01-01
     * ------------------------------------------------------------------------------
     * @param   array           $param [description]
     * @return  [type]                 [description]
     */
    public function del($table, $where = [], $param = [], $dbDatabase = null)
    {
        $where = !empty($where['whereInfo']) ? $where['whereInfo'] : [];

        if (!$table) {
            return ['code' => 5000, 'msg' => '服务异常,表名称错误', 'data' => []];
        }

        if (!$where) {
            return ['code' => 3000, 'msg' => '参数异常', 'data' => []];
        }

        if (!$param) {
            return ['code' => 3000, 'msg' => '参数异常', 'data' => []];
        }

        return $this->CommonModel->upField($table, $where, $param, $dbDatabase);
    }

    /**
     * [delTrue 真删除]
     * ------------------------------------------------------------------------------
     * @author github
     * ------------------------------------------------------------------------------
     * @version date:2100-01-01
     * ------------------------------------------------------------------------------
     * @return  [type]          [description]
     */
    public function delTrue($table, $where = [], $dbDatabase = null)
    {
        $where = !empty($where['whereInfo']) ? $where['whereInfo'] : [];

        if (!$table) {
            return ['code' => 5000, 'msg' => '服务异常,表名称错误', 'data' => []];
        }

        return $this->CommonModel->delTrue($table, $where, $dbDatabase);
    }

    /**
     * [upField 更新字段]
     * ------------------------------------------------------------------------------
     * @author github
     * ------------------------------------------------------------------------------
     * @version date:2100-01-01
     * ------------------------------------------------------------------------------
     * @param   array           $param [description]
     * @return  [type]                 [description]
     */
    public function upField($table, $where = [], $param = [], $dbDatabase = null)
    {
        $where = !empty($where['whereInfo']) ? $where['whereInfo'] : [];

        if (!$table) {
            return ['code' => 5000, 'msg' => '服务异常,表名称错误', 'data' => []];
        }

        if (!$where) {
            return ['code' => 3000, 'msg' => '参数异常', 'data' => []];
        }

        if (!$param) {
            return ['code' => 3000, 'msg' => '参数异常', 'data' => []];
        }

        return $this->CommonModel->upField($table, $where, $param, $dbDatabase);
    }

    /**
     * [execSql 执行原始sql]
     * ------------------------------------------------------------------------------
     * @author  github
     * ------------------------------------------------------------------------------
     * @version date:2100-01-01
     * ------------------------------------------------------------------------------
     * @param   [type]          $sql [description]
     * @return  [type]               [description]
     */
    public function execSql($sql, $dbDatabase = null)
    {

        return $this->CommonModel->execSql($sql, $dbDatabase);
    }
}
