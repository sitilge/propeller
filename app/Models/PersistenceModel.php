<?php

namespace App\Models;

use Abimo\Factory;
use Abimo\Database;

class PersistenceModel
{
    /**
     * @var Factory
     */
    protected $factory;

    /**
     * @var Database
     */
    protected $db;

    /**
     * @var string
     */
    public $table;

    /**
     * @var string
     */
    public $action;

    /**
     * @var string
     */
    public $id;

    /**
     * @var array
     */
    public $structure = [];

    /**
     * PersistenceModel constructor.
     * @param Factory $factory
     */
    public function __construct(Factory $factory)
    {
        $this->factory = $factory;

        $this->db = $this->factory->database(
            $this->factory
                ->config()
                ->path(__DIR__.'/../../app/Config')
        )
        ->connect();
    }

    /**
     * Read rows.
     * @param $table
     * @param null $id
     * @return array
     * @throws \ErrorException
     */
    public function readRows($table, $id = null)
    {
        if (empty($this->structure[$table]['columns'])) {
            throw new \ErrorException('No columns provided in '.$table.'.json.');
        }

        //loop the columns, the structure for main columns and join columns
        //are equal intentionally to maintain readability

        //regular query
        $columnsQuery = '';
        $tableQuery = '';
        $whereQuery = '';
        $orderQuery = '';
        $columnsArray = [];
        $valuesArray = [];

        foreach ($this->structure[$table]['columns'] as $columnName => $column) {
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
                    $this->structure[$table]['rowsJoin'][$columnName][$joinValueId] = $joinValueValue;
                }

                continue;
            }

            if (!empty($column['join'])) {
                foreach ($column['join'] as $joinTable => $join) {
                    //regular join query
                    $joinColumnsQuery = '';
                    $joinTableQuery = '';
                    $joinOrderQuery = '';
                    $joinColumnsArray = [] ;

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

                            $this->structure[$table]['rowsJoin'][$columnName][$key] = implode(', ', $row);
                        }
                    }
                }
            }
        }

        if (!empty($columnsArray)) {
            $columnsQuery = 'SELECT '.implode(',', $columnsArray);

            $tableQuery = ' FROM '.$this->db->backtick($table);
        }

        if (!empty($id)) {
            $whereQuery = ' WHERE '.$this->db->backtick($this->structure[$table]['key']).' = :'.$this->structure[$table]['key'];

            $valuesArray[$this->structure[$table]['key']] = $id;
        }

        if (!empty($orderArray)) {
            ksort($orderArray);
            $orderQuery = ' ORDER BY '.implode(',', $orderArray);
        }

        //TODO - special table order here
        if (!empty($this->structure[$table]['order'])) {
            if (empty($orderQuery)) {
                $orderQuery .= ' ORDER BY '.$this->db->backtick($this->structure[$table]['order']['column']);
            } else {
                $orderQuery .= $this->db->backtick($this->structure[$table]['order']['column']);
            }

            if (!empty($this->structure[$table]['order']['direction'])) {
                $orderQuery .= ' '.$this->structure[$table]['order']['direction'];
            }
        }

        $query = $columnsQuery.$tableQuery.$whereQuery.$orderQuery;

        $statement = $this->db->handle->prepare($query);

        if ($statement->execute($valuesArray)) {
            $key = $this->structure[$table]['key'];

            while ($row = $statement->fetch()) {
                $this->structure[$table]['rows'][$row[$key]] = $row;
            }
        }

        return $this->structure;
    }

    /**
     * Create a row.
     * @param $table
     * @param $inputColumns
     * @param $inputData
     * @return bool
     */
    public function createRow($table, $inputColumns, $inputData)
    {
        $columns = [];
        $values = [];

        foreach ($inputColumns as $columnName => $column) {
            if (!empty($column['plugin'])) {
                continue;
            }

            if (!empty($column['attributes']['disabled'])) {
                continue;
            }

            $values[':'.$columnName] = $inputData[$columnName];

            $columns[] = $this->db->backtick($columnName);
        }

        $tableQuery = $this->db->backtick($table);
        $columnsQuery = implode(',', $columns);

        $valuesQuery = implode(',', array_keys($values));

        $query = 'INSERT INTO '.$tableQuery.' ('.$columnsQuery.') VALUES ('.$valuesQuery.')';

        $statement = $this->db->handle->prepare($query);

        return $statement->execute($values);
    }

    /**
     * Update a row.
     * @param $table
     * @param $id
     * @param $inputColumns
     * @param $inputData
     * @return bool
     */
    public function updateRow($table, $id, $inputColumns, $inputData)
    {
        if (!is_array($inputData)) {
            $inputData = [$inputColumns => $inputData];
        }

        if (!is_array($inputColumns)) {
            $inputColumns = [$inputColumns => []];
        }

        $columns = [];
        $values = [];
        $valuesQuery = [];

        foreach ($inputColumns as $columnName => $column) {
            if (!empty($column['plugin'])) {
                continue;
            }

            if (!empty($column['attributes']['disabled'])) {
                continue;
            }

            $values[':'.$columnName] = $inputData[$columnName];

            $columns[] = $this->db->backtick($columnName);

            $valuesQuery[] = $columnName.'=:'.$columnName;
        }

        $tableQuery = $this->db->backtick($table);
        $valuesQuery = implode(',', $valuesQuery);

        $key = $this->structure[$table]['key'];
        $values[':'.$key] = $id;

        $query = '
            UPDATE '.$tableQuery.'
            SET '.$valuesQuery.'
            WHERE '.$this->db->backtick($key).' = :'.$key
        ;

        $statement = $this->db->handle->prepare($query);

        return $statement->execute($values);
    }

    /**
     * Delete a row.
     * @param $table
     * @param $id
     * @return bool
     */
    public function deleteRow($table, $id)
    {
        $key = $this->structure[$table]['key'];

        $query = '
            DELETE FROM '.$this->db->backtick($table).'
            WHERE '.$this->db->backtick($key).' = :'.$key
        ;

        $statement = $this->db->handle->prepare($query);
        $statement->bindValue(':'.$key, $id);

        return $statement->execute();
    }

    /**
     * Update the order of column.
     * @param $table
     */
    public function updateOrder($table)
    {
        if (!empty($_POST['order'])) {
            $values = [];

            $key = $this->structure[$table]['key'];

            $query = '
                UPDATE '.$this->db->backtick($table).'
                SET '.$this->db->backtick($this->structure[$table]['order']['column']).' = CASE '.$this->db->backtick($key)
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
