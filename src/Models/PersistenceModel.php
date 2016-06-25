<?php

namespace Propeller\Models;

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
    private $table = '';

    /**
     * @var string
     */
    private $key = '';

    /**
     * @var string
     */
    private $schemaPath = __DIR__.'/../Config/Database/Config/schema.xml';

    /**
     * @var string
     */
    private $runtimePath = __DIR__.'/../Config/Database/Config/generated-conf/config.php';

    /**
     * @var string
     */
    private $modelNamespace = 'Models\\Models';

    /**
     * @var array
     */
    private $tables = [];

    /**
     * @var ModelCriteria
     */
    private $query;

    /**
     * @var TableMap
     */
    private $map;

    /**
     * @var ColumnMap
     */
    private $columns;

    /**
     * @var ActiveRecordInterface
     */
    private $model;

    /**
     * @var string
     */
    public $output = '';

    /**
     * PersistenceModel constructor.
     * @param $table
     * @param $key
     */
    public function __construct($table, $key)
    {
        $this->table = $table;
        $this->key = $key;
    }

    /**
     * Get the configuration file.
     * @return void
     */
    private function getConfig()
    {
        require $this->runtimePath;
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
        $this->getConfig();

        $tables = $this->getTables();

        $queryName = $this->modelNamespace.$tables[$this->table].'Query';
        $query = new $queryName;

        //initialize the table query subclass
        if (method_exists($query, 'init')) {
            $query->init();
        }

        return $this->query = $query;
    }

    /**
     * Get the table map.
     * @return TableMap
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
     * Get the behavior.
     * @param $query
     * @return ModelCriteria
     */
    private function getBehavior($query)
    {
        //TODO - not using `select` on columns here
        //TODO - see http://stackoverflow.com/questions/37847376/propel-get-primary-key-after-find
        if (!empty($query->tableOrder)) {
            foreach ($query->tableOrder as $column => $direction) {
                $query->orderBy($column, $direction);
            }
        }

        return $query;
    }

    /**
     * Get the model.
     * @return ActiveRecordInterface
     */
    private function getModel()
    {
        if (!empty($this->model)) {
            return $this->model;
        }

        //load the config
        $this->getConfig();

        $tables = $this->getTables();

        $modelName = $this->modelNamespace.$tables[$this->table];
        $model = new $modelName;

        return $this->model = $model;
    }

    /**
     * Create a record.
     * @return int
     */
    public function createRow()
    {
        $model = $this->getModel();

        $map = $this->getMap();

        $input = $_POST;

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
     * Update the row.
     * @return int
     */
    public function updateRow()
    {
        $query = $this->getQuery()->findPk($this->key);

        $map = $this->getMap();

        parse_str(file_get_contents("php://input"), $input);

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