<?php

namespace Propeller\Behaviors;

use Propel\Generator\Model\Behavior;

class PropellerBehavior extends Behavior
{
    public function queryAttributes()
    {
        return $this->generateQueryAttributes();
    }

    protected function generateQueryAttributes()
    {
        //save the funny syntax
        return implode(' ', [
            $this->generateQueryAttributesTableCreate(),
            $this->generateQueryAttributesTableRead(),
            $this->generateQueryAttributesTableUpdate(),
            $this->generateQueryAttributesTableDelete(),
            $this->generateQueryAttributesTableOrder(),
            $this->generateQueryAttributesTableOrderColumns(),
            $this->generateQueryAttributesTableOrderDirections(),

            $this->generateQueryAttributesTableColumnsShow(),
            $this->generateQueryAttributesTableColumnsDisable(),
        ]).'
 ';
    }

    protected function generateQueryAttributesTableCreate()
    {
        return "
public \$tableCreate = true;
";
    }

    protected function generateQueryAttributesTableRead()
    {
        return "
public \$tableRead = true;
";
    }

    protected function generateQueryAttributesTableUpdate()
    {
        return "
public \$tableUpdate = true;
";
    }

    protected function generateQueryAttributesTableDelete()
    {
        return "
public \$tableDelete = true;
";
    }

    protected function generateQueryAttributesTableOrder()
    {
        return "
public \$tableOrder = false;
";
    }

    protected function generateQueryAttributesTableOrderColumns()
    {
        return "
public \$tableOrderColumns = [];
";
    }

    protected function generateQueryAttributesTableOrderDirections()
    {
        return "
public \$tableOrderDirections = [];
";
    }

    protected function generateQueryAttributesTableColumnsShow()
    {
        return "
public \$tableColumnsShow = [];
";
    }

    protected function generateQueryAttributesTableColumnsDisable()
    {
        return "
public \$tableColumnsDisable = [];
";
    }

    public function queryMethods()
    {
        return $this->generateQueryMethods();
    }

    protected function generateQueryMethods()
    {
        //save the funny syntax
        return implode(' ', [

        ]).'
 ';
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