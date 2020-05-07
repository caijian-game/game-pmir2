<?php

declare (strict_types = 1);

namespace App\Libs;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Task\Annotation\Task;
use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\Command;
use MongoDB\Driver\Manager;
use MongoDB\Driver\Query;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\WriteConcern;

class MongoDB
{
    /**
     * @Inject()
     * @var ConfigInterface
     */
    protected $config;

    /**
     * @var Manager
     */
    public $manager;

    protected $dbname;

    public $param;

    protected function manager()
    {
        $this->param = $this->config->get('mongodb.default');

        if ($this->manager instanceof Manager) {
            return $this->manager;
        }

        //副本集
        $option = !empty($this->param['options']['replicaSet']) ? ['replicaSet' => $this->param['options']['replicaSet']] : [];

        return $this->manager = new Manager($this->getUri(), $option);
    }

    protected function getUri()
    {
        $uri = 'mongodb://';

        if (!empty($this->param['username']) && !empty($this->param['password'])) {
            $uri .= $this->param['username'] . ':' . $this->param['password'] . '@';
        } else {
            return false;
        }

        $port = 27017;

        if (!empty($this->param['port'])) {
            $port = $this->param['port'];
        }

        if (!empty($this->param['host'])) {
            if (is_array($this->param['host'])) {
                $host = implode(':' . $port . ',', $this->param['host']);
                $uri .= $host . ':' . $port . '/';
            } else {
                $uri .= $this->param['host'] . ':' . $port . '/';
            }
        } else {
            return false;
        }

        if (!empty($this->param['options']['database'])) {
            $uri .= '' . $this->param['options']['database'];
        } else {
            $uri .= 'admin';
        }

        return $uri;
    }

    /**
     * @Task
     */
    public function insert($table, $doc = [])
    {
        try {
            $bulk = new BulkWrite;

            $bulk->insert($doc);

            $result = $this->manager()->executeBulkWrite($this->param['database'] . "." . $table, $bulk, new WriteConcern(WriteConcern::MAJORITY, 1000));

            return $result->getUpsertedCount();

        } catch (\MongoException $e) {
            $this->throwError($e->getMessage());
            return false;
        }
    }

    /**
     * @Task
     */
    public function update($table, $where = [], $doc = [])
    {
        try {

            $bulk = new BulkWrite;

            $bulk->update($where, $doc);

            $result = $this->manager()->executeBulkWrite($this->param['database'] . "." . $table, $bulk);
            return $result->getModifiedCount();
        } catch (\MongoException $e) {
            $this->throwError($e->getMessage());
            return false;
        }
    }

    /**
     * @Task
     */
    public function delete($table, $where = [])
    {
        try {

            $bulk = new BulkWrite;

            $bulk->delete($where);

            $result = $this->manager()->executeBulkWrite($this->param['database'] . "." . $table, $bulk);

            return $result->getDeletedCount();
        } catch (\MongoException $e) {
            $this->throwError($e->getMessage());
            return false;
        }
    }

    /**
     * @Task
     */
    public function find($table, $where = [], $start = 0, $size = 10, $sort = [])
    {
        try {

            $options = [];

            //排序
            $options['sort'] = !empty($sort) ? $sort : ['id' => -1];

            //分页
            $options['skip']  = !empty($start) ? (int) $start : 0; //起始
            $options['limit'] = !empty($size) ? (int) $size : 10; //返回条数

            $query = new Query($where, $options);

            $result = $this->manager()->executeQuery($this->param['database'] . "." . $table, $query, new ReadPreference(ReadPreference::RP_PRIMARY_PREFERRED))->toArray();

            return self::object2array($result);

        } catch (\MongoException $e) {
            $this->throwError($e->getMessage());
            return false;
        }
    }

    /**
     * @Task
     */
    public function getCount($table, $where = [])
    {
        try {

            $command = new Command(['count' => $table, 'query' => $where]);

            $result = $this->manager()->executeCommand($this->param['database'], $command, new ReadPreference(ReadPreference::RP_SECONDARY_PREFERRED))->toArray();
            $result = self::object2array($result);

            $count = 0;
            if (!empty($result[0]['n'])) {
                $count = $result[0]['n'];
            }

            return $count;
        } catch (\MongoException $e) {
            $this->throwError($e->getMessage());
            return false;
        }
    }

    /**
     * 输出错误信息
     * @param $errorInfo 错误内容
     */
    public function throwError($errorInfo = '')
    {
        return $errorInfo;
    }

    public static function object2array($res)
    {
        return json_decode(json_encode($res), true);
    }
}
