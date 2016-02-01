<?php

namespace App\Models;

use Abimo\Factory;
use App\Controllers\FrontController;

class PersistenceModel
{
    /**
     * @var BusinessModel
     */
    public $businessModel;

    /**
     * @var FrontController
     */
    public $frontController;

    /**
     * @var Factory
     */
    public $factory;

    /**
     * @var \Abimo\Config;
     */
    public $config;

    /**
     * @var \Abimo\Database
     */
    public $db;

    /**
     * PersistenceModel constructor.
     * @param FrontController $frontController
     * @param BusinessModel $businessModel
     */
    public function __construct(FrontController $frontController, BusinessModel $businessModel)
    {
        $this->frontController = $frontController;
        $this->businessModel = $businessModel;

        $this->factory = new Factory();

        $this->config = $this->factory->config();
        $this->db = $this->factory->database();
    }

    /**
     * @return array
     * @throws \ErrorException
     */
    public function getData()
    {
        if (empty($this->frontController->table)) {
            return $this->businessModel->data;
        }

        if (empty($this->businessModel->data[$this->frontController->table]['columns'])) {
            throw new \ErrorException('No columns provided in '.$this->frontController->table.'.json.');
        }

        //loop the columns, the structure for main columns and join columns
        //are equal intentionally to maintain readability

        //regular query
        $columnsQuery = '';
        $tableQuery = '';
        $orderQuery = '';

        foreach ($this->businessModel->data[$this->frontController->table]['columns'] as $columnName => $column) {
            $columnQueryName = $this->db->backtick($columnName);

            $columnsArray[] = $columnQueryName;

            if (!empty($column['order'])) {
                $orderArray[$column['order']] = $columnQueryName;

                if (!empty($column['order']['direction'])) {
                    $orderArray[$column['order']] .= ' '.$column['order']['direction'];
                }
            }

            //TODO - special join, override db if values already given
            if (!empty($column['values'])) {
                foreach ($column['values'] as $joinValueId => $joinValueValue) {
                    $this->businessModel->data[$this->frontController->table]['rowsJoin'][$columnName][$joinValueId] = $joinValueValue;
                }

                continue;
            }

            if (!empty($column['join'])) {
                foreach ($column['join'] as $joinTable => $join) {
                    //regular join query
                    $joinColumnsQuery = '';
                    $joinTableQuery = '';
                    $joinOrderQuery = '';

                    foreach ($join['columns'] as $joinColumnName => $joinColumn) {
                        $joinColumnQueryName = $this->db->backtick($joinColumnName);

                        $joinColumnsArray[] = $joinColumnQueryName;

                        if (!empty($joinColumn['order'])) {
                            $joinOrderArray[$column['order']] = $joinColumnQueryName;

                            if (!empty($joinColumn['order']['direction'])) {
                                $joinOrderArray[$column['order']] .= ' '.$joinColumn['order']['direction'];
                            }
                        }
                    }

                    if (!empty($joinColumnsArray)) {
                        $joinColumnsQuery = 'SELECT '.implode(',', $joinColumnsArray);

                        $joinTableQuery = ' FROM '.$this->db->backtick($joinTable);
                    }

                    if (!empty($joinOrderArray)) {
                        ksort($joinOrderArray);
                        $joinOrderQuery = ' ORDER BY '.implode(',', $joinOrderArray);
                    }

                    $query = $joinColumnsQuery.$joinTableQuery.$joinOrderQuery;

                    $statement = $this->db->handle->prepare($query);

                    if ($statement->execute()) {
                        while ($row = $statement->fetch()) {
                            //TODO - Do we need this? Modify to always include the join key!
                            $id = $row[$join['key']];
                            unset($row[$join['key']]);

                            $this->businessModel->data[$this->frontController->table]['rowsJoin'][$columnName][$id] = implode(', ', $row);
                        }
                    }
                }
            }
        }

        if (!empty($columnsArray)) {
            $columnsQuery = 'SELECT '.implode(',', $columnsArray);

            $tableQuery = ' FROM '.$this->db->backtick($this->frontController->table);
        }

        if (!empty($orderArray)) {
            ksort($orderArray);
            $orderQuery = ' ORDER BY '.implode(',', $orderArray);
        }

        //TODO - special table order here
        if (!empty($this->businessModel->data[$this->frontController->table]['order'])) {
            if (empty($orderQuery)) {
                $orderQuery .= ' ORDER BY '.$this->db->backtick($this->businessModel->data[$this->frontController->table]['order']['column']);
            } else {
                $orderQuery .= $this->db->backtick($this->businessModel->data[$this->frontController->table]['order']['column']);
            }

            if (!empty($this->businessModel->data[$this->frontController->table]['order']['direction'])) {
                $orderQuery .= ' '.$this->businessModel->data[$this->frontController->table]['order']['direction'];
            }
        }

        $query = $columnsQuery.$tableQuery.$orderQuery;

        $statement = $this->db->handle->prepare($query);

        if ($statement->execute()) {
            $key = $this->businessModel->data[$this->frontController->table]['key'];

            while ($row = $statement->fetch()) {
                $this->businessModel->data[$this->frontController->table]['rows'][$row[$key]] = $row;
            }
        }

        $this->businessModel->managePlugins();

        return $this->businessModel->data;
    }

    /**
     * @return integer
     */
    public function createRow()
    {
        $columns = [];
        $values = [];

        foreach ($this->businessModel->data[$this->frontController->table]['columns'] as $columnName => $column) {
            if (!empty($column['disabled'])) {
                continue;
            }

            $values[':'.$columnName] = $_POST[$this->frontController->action][$columnName];

            $columns[] = $this->db->backtick($columnName);
        }

        $tableQuery = $this->db->backtick($this->frontController->table);
        $columnsQuery = implode(',', $columns);

        $valuesQuery = implode(',', array_keys($values));

        $query = 'INSERT INTO '.$tableQuery.' ('.$columnsQuery.') VALUES ('.$valuesQuery.')';

        $statement = $this->db->handle->prepare($query);
        return $statement->execute($values);
    }

    public function updateRow()
    {
        $columns = [];
        $values = [];
        $valuesQuery = [];

        foreach ($this->businessModel->data[$this->frontController->table]['columns'] as $columnName => $column) {
            if (!empty($column['disabled'])) {
                continue;
            }

            $values[':'.$columnName] = $_POST[$this->frontController->action][$columnName];

            $columns[] = $this->db->backtick($columnName);

            $valuesQuery[] = $columnName.'=:'.$columnName;
        }

        $tableQuery = $this->db->backtick($this->frontController->table);
        $valuesQuery = implode(',', $valuesQuery);

        $key = $this->businessModel->data[$this->frontController->table]['key'];
        $values[':'.$key] = $this->frontController->id;

        $query = '
            UPDATE '.$tableQuery.'
            SET '.$valuesQuery.'
            WHERE '.$this->db->backtick($key).' = :'.$key
        ;

        $statement = $this->db->handle->prepare($query);

        return $statement->execute($values);
    }

    /**
     * @return boolean
     */
    public function deleteRow()
    {
        $key = $this->businessModel->data[$this->frontController->table]['key'];

        $query = '
            DELETE FROM
                '.$this->db->backtick($this->frontController->table).'
            WHERE
                '.$this->db->backtick($key).' = :'.$key
        ;

        $statement = $this->db->handle->prepare($query);
        $statement->bindValue(':'.$key, $this->frontController->id);

        return $statement->execute();
    }

    /**
     * @return void
     */
    public function updateOrder()
    {
        if (!empty($_POST['order'])) {
            $values = [];

            $key = $this->businessModel->data[$this->frontController->table]['key'];

            $query = '
                UPDATE '.$this->db->backtick($this->frontController->table).'
                SET '.$this->db->backtick($this->businessModel->data[$this->frontController->table]['order']['column']).' = CASE '.$this->db->backtick($key)
            ;

            foreach ($_POST['order'] as $order => $id) {
                $values[':'.$key.$id] = $id;
                $values[':order'.$order] = $order;
                $query .= ' WHEN :'.$key.$id.' THEN :order'.$order;
            }

            $query .= ' END WHERE '.$this->db->backtick($key).' IN ('.implode(',', array_values($_POST['order'])).')';

            $statement = $this->db->handle->prepare($query);
            exit($statement->execute($values));
        }
    }
}