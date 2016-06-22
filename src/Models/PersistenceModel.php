<?php

namespace Propeller\Models;

class PersistenceModel
{
    /**
     * @var string
     */
    private $table;

    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $schemaPath = __DIR__.'/../Config/Database/schema.xml';

    /**
     * @var string
     */
    public $output;

    public function __construct($table, $key)
    {
        $this->table = $table;
        $this->key = $key;
    }

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

    public function getQuery()
    {
        if (!empty($this->query)) {
            return $this->query;
        }

        $tables = $this->getTables();

        $queryName = 'Models\\Models\\'.$tables[$this->table].'Query';
        $query = new $queryName;

        //initialize the table query subclass
        if (method_exists($query, 'init')) {
            $query->init();
        }

        return $this->query = $query;
    }

    public function getMap()
    {
        if (!empty($this->map)) {
            return $this->map;
        }

        $query = $this->getQuery();

        return $this->map = $query->getTableMap();
    }

    public function getColumns()
    {
        if (!empty($this->columns)) {
            return $this->columns;
        }

        $map = $this->getMap();

        return $map->getColumns();
    }

    public function getKeys()
    {
        if (!empty($this->keys)) {
            return $this->keys;
        }

        $rows = $this->readRows();

        return $rows->getPrimaryKeys(false);
    }

    //TODO - move to OOP
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

    //TODO - merge with $this->getQuery()
    public function getModel()
    {
        $tables = $this->getTables();

        $modelName = 'Models\\Models\\'.$tables[$this->table];
        $model = new $modelName;

        return $this->model = $model;
    }

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

        $model->save();
    }

    public function readRow()
    {
        $query = $this->getQuery();

        //TODO - clean this mess
        $query = $this->getBehavior($query);

        return $query->findPk($this->key);
    }

    public function readRows()
    {
        $query = $this->getQuery();

        //TODO - clean this mess
        $query = $this->getBehavior($query);

        return $query->find();
    }

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

    public function deleteRow()
    {
        $query = $this->getQuery();

        return $query->findPk($this->key)->delete();
    }
}