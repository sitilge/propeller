<?php

namespace Propeller\Models;

use Propel\Generator\Model\Behavior;

class BehaviorModel extends Behavior
{
    /**
     * Generate query attributes.
     *
     * @return string
     */
    public function queryAttributes()
    {
        //save the funny syntax
        return implode(' ', [
            $this->generateQueryAttributesPropellerTableCreate(),
            $this->generateQueryAttributesPropellerTableRead(),
            $this->generateQueryAttributesPropellerTableUpdate(),
            $this->generateQueryAttributesPropellerTableDelete(),
            $this->generateQueryAttributesPropellerTableOrder(),
            $this->generateQueryAttributesPropellerTableColumnsShow(),
        ]).'
 ';
    }

    /**
     * Generator method to allow to create a record.
     *
     * @return string
     */
    protected function generateQueryAttributesPropellerTableCreate()
    {
        return "
/**
 * Allow to create a record.
 *
 * @var bool
 */
private \$propellerTableCreate = true;
";
    }

    /**
     * Generator method to allow to read the record.
     *
     * @return string
     */
    protected function generateQueryAttributesPropellerTableRead()
    {
        return "
/**
 * Allow to read the record.
 *
 * @var bool
 */
private \$propellerTableRead = true;
";
    }

    /**
     * Generator method to allow to update the record.
     *
     * @return string
     */
    protected function generateQueryAttributesPropellerTableUpdate()
    {
        return "
/**
 * Allow to update the record.
 *
 * @var bool
 */
private \$propellerTableUpdate = true;
";
    }

    /**
     * Generator method to allow to delete the record.
     *
     * @return string
     */
    protected function generateQueryAttributesPropellerTableDelete()
    {
        return "
/**
 * Allow to delete the record.
 *
 * @var bool
 */
private \$propellerTableDelete = true;
";
    }

    /**
     * Generator method to order records according to columns and directions.
     *
     * @return string
     */
    protected function generateQueryAttributesPropellerTableOrder()
    {
        return "
/**
 * Order records according to columns and directions.
 *
 * @var array
 */
private \$propellerTableOrder = [];
";
    }

    /**
     * Generator method to show columns.
     *
     * @return string
     */
    protected function generateQueryAttributesPropellerTableColumnsShow()
    {
        return "
/**
 * Show columns.
 *
 * @var array
 */
private \$propellerTableColumnsShow = [];
";
    }

    /**
     * Generate query methods.
     *
     * @return string
     */
    public function queryMethods()
    {
        //save the funny syntax
        return implode(' ', [
            $this->generateQueryMethodsSetPropellerTableCreate(),
            $this->generateQueryMethodsGetPropellerTableCreate(),
            $this->generateQueryMethodsSetPropellerTableRead(),
            $this->generateQueryMethodsGetPropellerTableRead(),
            $this->generateQueryMethodsSetPropellerTableUpdate(),
            $this->generateQueryMethodsGetPropellerTableUpdate(),
            $this->generateQueryMethodsSetPropellerTableDelete(),
            $this->generateQueryMethodsGetPropellerTableDelete(),
            $this->generateQueryMethodsSetPropellerTableOrder(),
            $this->generateQueryMethodsGetPropellerTableOrder(),
            $this->generateQueryMethodsSetPropellerTableColumnsShow(),
            $this->generateQueryMethodsGetPropellerTableColumnsShow(),
        ]).'
 ';
    }

    /**
     * Generator method to set the record creating boolean.
     *
     * @return string
     */
    protected function generateQueryMethodsSetPropellerTableCreate()
    {
        return "
/**
 * Set the boolean to allow to create a record.
 *
 * @param bool \$key
 *
 * @return void
 */
public function setPropellerTableCreate(\$key = true)
{
    \$this->propellerTableCreate = \$key;
}
";
    }

    /**
     * Generator method to get the record creating boolean.
     *
     * @return string
     */
    protected function generateQueryMethodsGetPropellerTableCreate()
    {
        return "
/**
 * Get the boolean to allow to create a record.
 *
 * @return bool
 */
public function getPropellerTableCreate()
{
    return \$this->propellerTableCreate;
}
";
    }

    /**
     * Generator method to set the record reading boolean.
     *
     * @return string
     */
    protected function generateQueryMethodsSetPropellerTableRead()
    {
        return "
/**
 * Set the boolean to allow to read the record.
 *
 * @param bool \$key
 *
 * @return void
 */
public function setPropellerTableRead(\$key = true)
{
    \$this->propellerTableRead = \$key;
}
";
    }

    /**
     * Generator method to get the record reading boolean.
     *
     * @return string
     */
    protected function generateQueryMethodsGetPropellerTableRead()
    {
        return "
/**
 * Get the boolean to allow to read a new record.
 *
 * @return bool
 */
public function getPropellerTableRead()
{
    return \$this->propellerTableRead;
}
";
    }

    /**
     * Generator method to set the record updating boolean.
     *
     * @return string
     */
    protected function generateQueryMethodsSetPropellerTableUpdate()
    {
        return "
/**
 * Set the boolean to allow to update the record.
 *
 * @param bool \$key
 *
 * @return void
 */
public function setPropellerTableUpdate(\$key = true)
{
    \$this->propellerTableUpdate = \$key;
}
";
    }

    /**
     * Generator method to get the record updating boolean.
     *
     * @return string
     */
    protected function generateQueryMethodsGetPropellerTableUpdate()
    {
        return "
/**
 * Get the boolean to allow to update a new record.
 *
 * @return bool
 */
public function getPropellerTableUpdate()
{
    return \$this->propellerTableUpdate;
}
";
    }

    /**
     * Generator method to set the record deleting boolean.
     *
     * @return string
     */
    protected function generateQueryMethodsSetPropellerTableDelete()
    {
        return "
/**
 * Set the boolean to allow to delete a new record.
 *
 * @param bool \$key
 *
 * @return void
 */
public function setPropellerTableDelete(\$key = true)
{
    \$this->propellerTableDelete = \$key;
}
";
    }

    /**
     * Generator method to get the record deleting boolean.
     *
     * @return string
     */
    protected function generateQueryMethodsGetPropellerTableDelete()
    {
        return "
/**
 * Get the boolean to allow to delete a new record.
 *
 * @return bool
 */
public function getPropellerTableDelete()
{
    return \$this->propellerTableDelete;
}
";
    }

    /**
     * Generator method to set the order of records.
     *
     * @return string
     */
    protected function generateQueryMethodsSetPropellerTableOrder()
    {
        return "
/**
 * Set the order of records according to the column and the direction.
 *
 * @param string \$key The column.
 * @param string \$value The value.
 *
 * @return void
 */
public function setPropellerTableOrder(\$key, \$value)
{
    \$this->propellerTableOrder[\$key] = \$value;
}
";
    }

    /**
     * Generator method to get the order of records.
     *
     * @return string
     */
    protected function generateQueryMethodsGetPropellerTableOrder()
    {
        return "
/**
 * Get the order of records according to the column and the direction.
 *
 * @param string \$key The column.
 *
 * @return string
 */
public function getPropellerTableOrder(\$key)
{
    return isset(\$this->propellerTableOrder[\$key]) ? \$this->propellerTableOrder[\$key] : null;
}
";
    }

    /**
     * Generator method to set the visibility of the column.
     *
     * @return string
     */
    protected function generateQueryMethodsSetPropellerTableColumnsShow()
    {
        return "
/**
 * Set the visibility of the column.
 *
 * @param string \$key The column.
 * @param bool \$value The value.
 *
 * @return void
 */
public function setPropellerTableColumnsShow(\$key, \$value = true)
{
    \$this->propellerTableColumnsShow[\$key] = \$value;
}
";
    }

    /**
     * Generator method to get the visibility of the column.
     *
     * @return string
     */
    protected function generateQueryMethodsGetPropellerTableColumnsShow()
    {
        return "
/**
 * Get the visibility of the column.
 *
 * @param string \$key The column.
 *
 * @return bool
 */
public function getPropellerTableColumnsShow(\$key)
{
    return isset(\$this->propellerTableColumnsShow[\$key]) ? \$this->propellerTableColumnsShow[\$key] : null;
}
";
    }

//    public function objectMethods()
//    {
//        $script = '';
//        $script .= $this->addUpdateAggregateColumn();
//        return $script;
//    }
//
//    protected function addUpdateAggregateColumn()
//    {
//        $sql = sprintf('SELECT %s FROM %s WHERE %s = ?',
//            $this->getParameter('expression'),
//            $this->getParameter('foreign_table'),
//            $this->getParameter('foreign_column')
//        );
////        echo $sql;exit;
//        $table = $this->getTable();
//        $aggregateColumn = $table->getColumn($this->getParameter('name'));
//        $columnPhpName = $aggregateColumn->getPhpName();
//        $localColumn = $table->getColumn($this->getParameter('local_column'));
//
//        return "
///**
// * Updates the aggregate column {$aggregateColumn->getName()}
// *
// * @param PropelPDO \$con A connection object
// */
//public function martinsBest{$columnPhpName}(PropelPDO \$con)
//{
//  \$sql = '{$sql}';
//  \$stmt = \$con->prepare(\$sql);
//  \$stmt->execute(array(\$this->get{$localColumn->getPhpName()}()));
//  \$this->set{$columnPhpName}(\$stmt->fetchColumn());
//  \$this->save(\$con);
//}
//";
//    }
}