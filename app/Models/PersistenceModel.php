<?php

namespace App\Models;

use Abimo\Factory;

class PersistenceModel
{
    /**
     * @var string
     */
    private $table;

    /**
     * @var string
     */
    private $action;

    /**
     * @var string
     */
    private $id;

    /**
     * @var BusinessModel
     */
    private $businessModel;

    /**
     * @var Factory
     */
    private $factory;

    /**
     * @var \Abimo\Config;
     */
    private $config;

    /**
     * @var \Abimo\Database
     */
    private $db;

    /**
     * PersistenceModel constructor.
     *
     * @param BusinessModel $businessModel
     */
    public function __construct(BusinessModel $businessModel)
    {
        $this->businessModel = $businessModel;

        $this->table = $this->businessModel->table;
        $this->action = $this->businessModel->action;
        $this->id = $this->businessModel->id;

        $this->factory = new Factory();

        $this->config = $this->factory->config();
        $this->db = $this->factory->database();
    }

    /**
     * Read all rows.
     *
     * @return array
     * @throws \ErrorException
     */
    public function readRows()
    {
        if (empty($this->table)) {
            return $this->businessModel->data;
        }

        if (empty($this->businessModel->data[$this->table]['columns'])) {
            throw new \ErrorException('No columns provided in '.$this->table.'.json.');
        }

        //loop the columns, the structure for main columns and join columns
        //are equal intentionally to maintain readability

        //regular query
        $columnsQuery = '';
        $tableQuery = '';
        $orderQuery = '';

        foreach ($this->businessModel->data[$this->table]['columns'] as $columnName => $column) {
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
                    $this->businessModel->data[$this->table]['rowsJoin'][$columnName][$joinValueId] = $joinValueValue;
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
                            //TODO - store the key in another variable
                            //TODO - then implode all the join columns
                            $key = $row[$join['key']];
                            unset($row[$join['key']]);

                            $this->businessModel->data[$this->table]['rowsJoin'][$columnName][$key] = implode(', ', $row);
                        }
                    }
                }
            }
        }

        if (!empty($columnsArray)) {
            $columnsQuery = 'SELECT '.implode(',', $columnsArray);

            $tableQuery = ' FROM '.$this->db->backtick($this->table);
        }

        if (!empty($orderArray)) {
            ksort($orderArray);
            $orderQuery = ' ORDER BY '.implode(',', $orderArray);
        }

        //TODO - special table order here
        if (!empty($this->businessModel->data[$this->table]['order'])) {
            if (empty($orderQuery)) {
                $orderQuery .= ' ORDER BY '.$this->db->backtick($this->businessModel->data[$this->table]['order']['column']);
            } else {
                $orderQuery .= $this->db->backtick($this->businessModel->data[$this->table]['order']['column']);
            }

            if (!empty($this->businessModel->data[$this->table]['order']['direction'])) {
                $orderQuery .= ' '.$this->businessModel->data[$this->table]['order']['direction'];
            }
        }

        $query = $columnsQuery.$tableQuery.$orderQuery;

        $statement = $this->db->handle->prepare($query);

        if ($statement->execute()) {
            $key = $this->businessModel->data[$this->table]['key'];

            while ($row = $statement->fetch()) {
                $this->businessModel->data[$this->table]['rows'][$row[$key]] = $row;
            }
        }

        $this->businessModel->managePlugins();

        return $this->businessModel->data;
    }

    /**
     * Create the row.
     *
     * @return bool
     */
    public function createRow()
    {
        $columns = [];
        $values = [];

        foreach ($this->businessModel->data[$this->table]['columns'] as $columnName => $column) {
            if (!empty($column['attributes']['disabled'])) {
                continue;
            }

            $values[':'.$columnName] = $_POST[$this->action][$columnName];

            $columns[] = $this->db->backtick($columnName);
        }

        $tableQuery = $this->db->backtick($this->table);
        $columnsQuery = implode(',', $columns);

        $valuesQuery = implode(',', array_keys($values));

        $query = 'INSERT INTO '.$tableQuery.' ('.$columnsQuery.') VALUES ('.$valuesQuery.')';

        $statement = $this->db->handle->prepare($query);

        return $statement->execute($values);
    }

    /**
     * Update the row.
     *
     * @return bool
     */
    public function updateRow()
    {
        $columns = [];
        $values = [];
        $valuesQuery = [];

        foreach ($this->businessModel->data[$this->table]['columns'] as $columnName => $column) {
            if (!empty($column['attributes']['disabled'])) {
                continue;
            }

            $values[':'.$columnName] = $_POST[$this->action][$columnName];

            $columns[] = $this->db->backtick($columnName);

            $valuesQuery[] = $columnName.'=:'.$columnName;
        }

        $tableQuery = $this->db->backtick($this->table);
        $valuesQuery = implode(',', $valuesQuery);

        $key = $this->businessModel->data[$this->table]['key'];
        $values[':'.$key] = $this->id;

        $query = '
            UPDATE '.$tableQuery.'
            SET '.$valuesQuery.'
            WHERE '.$this->db->backtick($key).' = :'.$key
        ;

        $statement = $this->db->handle->prepare($query);

        return $statement->execute($values);
    }

    /**
     * Delete the row.
     *
     * @return boolean
     */
    public function deleteRow()
    {
        $key = $this->businessModel->data[$this->table]['key'];

        $query = '
            DELETE FROM
                '.$this->db->backtick($this->table).'
            WHERE
                '.$this->db->backtick($key).' = :'.$key
        ;

        $statement = $this->db->handle->prepare($query);
        $statement->bindValue(':'.$key, $this->id);

        return $statement->execute();
    }

    /**
     * Update the order of column.
     *
     * @return void
     */
    public function updateOrder()
    {
        if (!empty($_POST['order'])) {
            $values = [];

            $key = $this->businessModel->data[$this->table]['key'];

            $query = '
                UPDATE '.$this->db->backtick($this->table).'
                SET '.$this->db->backtick($this->businessModel->data[$this->table]['order']['column']).' = CASE '.$this->db->backtick($key)
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