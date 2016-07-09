<?php

namespace Propeller\Models;

use Propel\Runtime\ActiveQuery\ModelCriteria;
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
     * @var OrmModel
     */
    public $ormModel;

    /**
     * @var string
     */
    public $schema = __DIR__.'/../Config/Database/Config/schema.xml';

    /**
     * @var string
     */
    public $runtime = __DIR__.'/../Config/Database/Config/generated-conf/config.php';

    /**
     * @var string
     */
    public $namespace = 'Models\\Models\\';

    /**
     * @var array
     */
    public $tables = [];

    /**
     * @var ModelCriteria
     */
    public $query;

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
     *
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
     *
     * @return array
     */
    public function getTables()
    {
        if (!empty($this->tables)) {
            return $this->tables;
        }

        $xml = simplexml_load_file($this->schema);

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
     *
     * @return ModelCriteria
     */
    public function getQuery()
    {
        require $this->runtime;

        $tables = $this->getTables();

        return $this->query = $this->ormModel->getQuery(
            $this->namespace,
            $tables[$this->table]
        );
    }

    /**
     * Get the model.
     *
     * @return ActiveRecordInterface
     */
    public function getModel()
    {
        require $this->runtime;

        $tables = $this->getTables();

        return $this->model = $this->ormModel->getModel(
            $this->namespace,
            $tables[$this->table]
        );
    }

    /**
     * @param array $input
     *
     * @return mixed
     */
    public function createRow($input)
    {
        $model = $this->getModel();

        foreach ($input as $column => $value) {
            $model->setByName($column, $value, 'fieldName');
        }

        return $this->ormModel->save($model);
    }

    /**
     * Read a row.
     *
     * @return ObjectCollection
     */
    public function readRow()
    {
        $query = $this->getQuery();

        $query = $this->getBehavior($query);

        if (strpos($this->key, '-') !== false) {
            $this->key = explode('-', $this->key);
        }

        return $query->findPk($this->key);
    }

    /**
     * Read rows.
     *
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
     *
     * @param $input
     *
     * @return mixed
     */
    public function updateRow($input)
    {
        $query = $this->getQuery()->findPk($this->key);

        foreach ($input as $column => $value) {
            $query->setByName($column, $value, 'fieldName');
        }

        return $this->ormModel->save($query);
    }

    /**
     * Delete the row.
     *
     * @return int
     */
    public function deleteRow()
    {
        $query = $this->getQuery()->findPk($this->key);

        return $this->ormModel->delete($query);
    }

    /**
     * Get the behavior.
     *
     * @param $query
     *
     * @return ModelCriteria
     */
    private function getBehavior(ModelCriteria $query)
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
}
