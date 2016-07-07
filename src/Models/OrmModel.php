<?php

namespace Propeller\Models;

use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveRecord\ActiveRecordInterface;

class OrmModel
{
    /**
     * @param $namespace
     * @param $table
     *
     * @return ModelCriteria
     */
    public function getQuery($namespace, $table)
    {
        $query = $namespace.$table.'Query';

        return new $query();
    }

    /**
     * @param $namespace
     * @param $table
     *
     * @return ActiveRecordInterface
     */
    public function getModel($namespace, $table)
    {
        $model = $namespace.$table;

        return new $model();
    }

    /**
     * @param $object
     *
     * @return mixed
     */
    public function save($object)
    {
        return $object->save();
    }

    /**
     * @param $object
     *
     * @return mixed
     */
    public function delete($object)
    {
        return $object->delete();
    }
}
