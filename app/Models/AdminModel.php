<?php

namespace App\Models;

use Abimo\Factory;

class AdminModel
{
    public $config = [];

    public $data = [];

    public $tablesPath = __DIR__.'/../Misc/Admin';

    public $publicPath = __DIR__.'/../../public';

    public $imageDir = 'img';

    public $db;

    public $controller;

    public function __construct($controller)
    {
        $this->factory = new Factory();

        $this->db = $this->factory->database();

        $this->controller = $controller;
    }

    public function getContent()
    {
        $this->manageData();

        if (null === $this->controller->table) {
            return '';
        }

        $content = $this->factory->template()
            ->set('router', new UrlModel())
            ->set('data', $this->data)
            ->set('table', $this->controller->table)
            ->set('action', $this->controller->action)
            ->set('id', $this->controller->id);

        if (null === $this->controller->action) {
            $content->file(__DIR__.'/../Views/Admin/Table');
        } else {
            $content->file(__DIR__.'/../Views/Admin/Row');
        }

        return $content->render();
    }

    public function manageData()
    {
        $this->data = $this->getJson($this->tablesPath);

        if (!empty($_POST)) {
            if (!empty($_POST['order'])) {
                $this->setOrder();
            } else {
                $this->setData();
            }
        }

        $this->getData();
    }

    public function getJson($path)
    {
        $iterator = new \DirectoryIterator($path);

        foreach ($iterator as $file) {
            if ('.' === substr($file->getFilename(), 0, 1)) {
                continue;
            }

            $string = file_get_contents($path.'/'.$file);

            if (!$json = json_decode($string, true)) {
                throw new \ErrorException('Unable to decode '.$file);
            }

            $table = basename($file, '.json');

            $data[$table] = $json;
        }

        return $data;
    }

    public function getMenu()
    {
        $menu = [];

        foreach ($this->data as $table => $config) {
            $menu[$table] = [
                'name' => !empty($config['name']) ? $config['name'] : $table
            ];
        }

        return $menu;
    }

    public function getData()
    {
        if (empty($this->controller->table)) {
            return $this->data;
        }

        //loop the columns, the structure for main columns and join columns
        //are equal intentionally to maintain readability

        //regular query
        $columnsQuery = '';
        $tableQuery = '';
        $orderQuery = '';

        foreach ($this->data[$this->controller->table]['columns'] as $columnName => $column) {
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
                    $this->data[$this->controller->table]['rowsJoin'][$columnName][$joinValueId] = $joinValueValue;
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
                            //special join logic
                            $id = $row['id'];
                            unset($row['id']);

                            $this->data[$this->controller->table]['rowsJoin'][$columnName][$id] = implode(', ', $row);
                        }
                    }
                }
            }
        }

        if (!empty($columnsArray)) {
            $columnsQuery = 'SELECT '.implode(',', $columnsArray);

            $tableQuery = ' FROM '.$this->db->backtick($this->controller->table);
        }

        if (!empty($orderArray)) {
            ksort($orderArray);
            $orderQuery = ' ORDER BY '.implode(',', $orderArray);
        }

        //TODO - special table order here
        if (!empty($this->data[$this->controller->table]['order'])) {
            if (empty($orderQuery)) {
                $orderQuery .= ' ORDER BY '.$this->db->backtick($this->data[$this->controller->table]['order']['column']);
            } else {
                $orderQuery .= $this->db->backtick($this->data[$this->controller->table]['order']['column']);
            }

            if (!empty($this->data[$this->controller->table]['order']['direction'])) {
                $orderQuery .= ' '.$this->data[$this->controller->table]['order']['direction'];
            }
        }

        $query = $columnsQuery.$tableQuery.$orderQuery;

        $statement = $this->db->handle->prepare($query);

        if ($statement->execute()) {
            while ($row = $statement->fetch()) {
                $this->data[$this->controller->table]['rows'][$row['id']] = $row;
            }
        }

        $this->managePlugins();

        return $this->data;
    }

    public function managePlugins()
    {
        $this->pluginImage();
    }

    public function pluginImage()
    {
        if (!empty($_FILES)) {
            if (isset($_FILES['image']['error']) && $_FILES['image']['error'] === 0) {
                if (!is_dir($this->publicPath.'/'.$this->imageDir.'/'.$this->controller->table)) {
                    mkdir($this->publicPath.'/'.$this->imageDir.'/'.$this->controller->table);
                }

                $storage = new \Upload\Storage\FileSystem($this->publicPath.'/'.$this->imageDir.'/'.$this->controller->table, true);

                $file = new \Upload\File('image', $storage);

                $file
                    ->addValidations([
                        new \Upload\Validation\Mimetype([
                            'image/png',
                            'image/jpg',
                            'image/jpeg',
                            'image/gif',
                            'image/tif',
                        ]),
                        new \Upload\Validation\Size('5M')
                    ]);

                $file->upload();
            }
        } else {
            $structure = [];

            $directory = new \RecursiveDirectoryIterator($this->publicPath.'/'.$this->imageDir, \RecursiveDirectoryIterator::SKIP_DOTS);
            foreach ($directory as $file ) {
                if ('.' === substr($file->getFilename(), 0, 1)) {
                    continue;
                }

                $pathname = $file->getPathname();
                $dir = basename($file->getPath());

                //structure by modification time and filename
                $structure[$dir][$file->getMtime().$file->getFilename()] = str_replace($this->publicPath, '', $pathname);

                krsort($structure[$dir]);
            }

            $content = $this->factory->template()
                ->file(__DIR__.'/../Views/Admin/Plugins/Image')
                ->set('structure', $structure)
                ->set('data', $this->data)
                ->set('imageDir', $this->imageDir)
                ->set('table', $this->controller->table)
                ->set('action', $this->controller->action)
                ->set('id', $this->controller->id);

            foreach ($this->data[$this->controller->table]['columns'] as $columnName => $column) {
                if (isset($column['type']) && $column['type'] == 'image') {
                    $this->data[$this->controller->table]['plugins'][$columnName] = $content
                        ->set('column', $columnName)
                        ->render();
                }
            }
        }
    }
    
    public function setData()
    {
        if ($this->controller->action === 'add' || $this->controller->action === 'edit') {
            $this->managePlugins();

            $response = $this->factory->response();
            $router = new UrlModel();

            $response
                ->header('Location: '.$router->admin($this->controller->table))
                ->send();

            exit($this->createUpdateRow());
        } elseif ($this->controller->action === 'remove') {
            exit($this->deleteRow());
        }
    }
    
    public function createUpdateRow()
    {
        $columns = [];
        $values = [];
        $valuesInsert = [];
        $valuesUpdate = [];

        foreach ($this->data[$this->controller->table]['columns'] as $columnName => $column) {
            $values[':'.$columnName.'Insert'] = $_POST[$this->controller->action][$columnName];
            $values[':'.$columnName.'Update'] = $_POST[$this->controller->action][$columnName];

            $columns[] = $this->db->backtick($columnName);

            $valuesInsert[] = ':'.$columnName.'Insert';
            $valuesUpdate[] = $columnName.'=:'.$columnName.'Update';
        }
            
        $tableQuery = $this->db->backtick($this->controller->table);
        $columnsQuery = implode(',', $columns);
        $valuesInsertQuery = implode(',', $valuesInsert);
        $valuesUpdateQuery = implode(',', $valuesUpdate);

        $query = '
            INSERT
                INTO '.$tableQuery.' ('.$columnsQuery.')
            VALUES	('.$valuesInsertQuery.')
            ON DUPLICATE KEY UPDATE
                '.$valuesUpdateQuery
            ;

        $statement = $this->db->handle->prepare($query);
        $statement->execute($values);

        return $this->db->handle->lastInsertId();
    }

    public function deleteRow()
    {
        $query = '
            DELETE FROM
                '.$this->db->backtick($this->controller->table).'
            WHERE
                `id` = :row_id
            '
            ;

        $statement = $this->db->handle->prepare($query);
        $statement->bindValue(':row_id', $this->controller->id);

        exit($statement->execute());
    }

    public function setOrder()
    {
        if (!empty($_POST['order'])) {
            $query = '
                UPDATE '.$this->db->backtick($this->controller->table).'
                SET '.$this->db->backtick($this->data[$this->controller->table]['order']['column']).' = CASE `id`'
            ;

            foreach ($_POST['order'] as $order => $id) {
                $values[':id'.$id] = $id;
                $values[':order'.$order] = $order;
                $query .= ' WHEN :id'.$id.' THEN :order'.$order;
            }

            $query .= ' END WHERE `id` IN ('.implode(',', array_values($_POST['order'])).')';

            $statement = $this->db->handle->prepare($query);
            exit($statement->execute($values));
        }
    }
}