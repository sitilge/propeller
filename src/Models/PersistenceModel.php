<?php

namespace Propeller\Models;

use Models\Models\UsersQuery;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Map\ColumnMap;
use Propel\Runtime\ActiveRecord\ActiveRecordInterface;
use Propel\Runtime\Collection\ObjectCollection;

class PersistenceModel
{
    /**
     * @var string
     */
    public $table = '';

    /**
     * @var string
     */
    public $key = '';

    /**
     * @var string
     */
    public $schemaPath = __DIR__.'/../Config/Database/Config/schema.xml';

    /**
     * @var string
     */
    public $runtimePath = __DIR__.'/../Config/Database/Config/generated-conf/config.php';

    /**
     * @var string
     */
    public $modelNamespace = 'Models\\Models\\';

    /**
     * @var array
     */
    public $tables = [];

    /**
     * @var ModelCriteria
     */
    public $query;

    /**
     * @var TableMap
     */
    public $map;

    /**
     * @var ColumnMap
     */
    public $columns;

    /**
     * @var ActiveRecordInterface
     */
    public $model;

    /**
     * @var string
     */
    public $output = '';

    /**
     * PersistenceModel constructor.
     * @param null $table
     * @param null $key
     */
    public function __construct($table = null, $key = null)
    {
        $this->table = $table;
        $this->key = $key;
    }

    /**
     * Get schema tables.
     * @return array
     */
    public function getTables()
    {
        if (!empty($this->tables)) {
            return $this->tables;
        }

        $xml = simplexml_load_file($this->schemaPath);

        $tables = [];

        foreach ($xml->table as $table) {
            $name = (string) $table->attributes()->{'name'};
            $phpName = (string) $table->attributes()->{'phpName'};
            $tables[$name] = $phpName;
        }

        return $this->tables = $tables;
    }

    /**
     * Get the query.
     * @return ModelCriteria
     */
    public function getQuery()
    {
        if (!empty($this->query)) {
            return $this->query;
        }

        //load the config
        require $this->runtimePath;

        $tables = $this->getTables();

        $queryName = $this->modelNamespace.$tables[$this->table].'Query';
        $query = new $queryName;

        return $this->query = $query;
    }

    /**
     * Get the table map.
     * @return TableMapQuery
     */
    public function getMap()
    {
        if (!empty($this->map)) {
            return $this->map;
        }

        $query = $this->getQuery();

        return $this->map = $query->getTableMap();
    }

    /**
     * Get the table columns.
     * @return ColumnMap
     */
    public function getColumns()
    {
        if (!empty($this->columns)) {
            return $this->columns;
        }

        $map = $this->getMap();

        return $map->getColumns();
    }

    /**
     * Get primary keys.
     * @return array
     */
    public function getKeys()
    {
        if (!empty($this->keys)) {
            return $this->keys;
        }

        $rows = $this->readRows();

        return $rows->getPrimaryKeys(false);
    }

    /**
     * Get the model.
     * @return ActiveRecordInterface
     */
    public function getModel()
    {
        if (!empty($this->model)) {
            return $this->model;
        }

        //load the config
        require $this->runtimePath;

        $tables = $this->getTables();

        $modelName = $this->modelNamespace.$tables[$this->table];
        $model = new $modelName;

        return $this->model = $model;
    }

    /**
     * Create a record.
     * @return int
     */
    public function createRow($input)
    {
        $model = $this->getModel();

        $map = $this->getMap();

        foreach ($input as $column => $value) {
            if (!empty($map->getPrimaryKeys()[$column])) {
                continue;
            }

            $model->setByName($column, $value, $map::TYPE_FIELDNAME);
        }

        return $model->save();
    }

    /**
     * Read a row.
     * @return mixed
     */
    public function readRow()
    {
        $query = $this->getQuery();

        $query = $this->getBehavior($query);

        return $query->findPk($this->key);
    }

    /**
     * Read rows.
     * @return ObjectCollection
     */
    public function readRows()
    {
        $query = $this->getQuery();

        $query = $this->getBehavior($query);

        return $query->find();
    }

    /**
     * Get the behavior.
     * @param $query
     * @return ModelCriteria
     */
    private function getBehavior($query)
    {
        //initialize the table query subclass
        if (!method_exists($query, 'init')) {
            return $query;
        }

        $query->init();

        if (!empty($query->tableOrder)) {
            foreach ($query->tableOrder as $column => $direction) {
                $query->orderBy($column, $direction);
            }
        }

        return $query;
    }

    /**
     * Update the row.
     * @return int
     */
    public function updateRow($input)
    {
        $query = $this->getQuery()->findPk($this->key);

        $map = $this->getMap();

        foreach ($input as $column => $value) {
            if (!empty($map->getPrimaryKeys()[$column])) {
                continue;
            }

            $query->setByName($column, $value, $map::TYPE_FIELDNAME);
        }

        return $query->save();
    }

    /**
     * Delete the row.
     * @return int
     */
    public function deleteRow()
    {
        $query = $this->getQuery();

        return $query->findPk($this->key)->delete();
    }
}