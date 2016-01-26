<?php

namespace App\Models;

use App\Controllers\AdminController;

class AdminModel
{
    /**
     * @var \Abimo\Factory
     */
    private $factory;

    /**
     * @var \Abimo\Config
     */
    private $config;

    /**
     * @var \Abimo\Database
     */
    private $db;

    /**
     * @var AdminController
     */
    private $controller;

    /**
     * @var array
     */
    private $data = [];

    /**
     * AdminModel constructor.
     *
     * @param AdminController $controller
     */
    public function __construct(AdminController $controller)
    {
        $this->factory = new \Abimo\Factory();
        $this->config = $this->factory->config();
        $this->db = $this->factory->database();

        $this->controller = $controller;
    }

    /**
     * @return string
     */
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

    /**
     * @throws \ErrorException
     * @return void
     */
    private function manageData()
    {
        $jsonPath = rtrim($this->config->get('admin', 'jsonPath'), '/');

        $this->data = $this->getJson($jsonPath);

        if (!empty($_POST)) {
            if (!empty($_POST['order'])) {
                $this->setOrder();
            } else {
                $this->setData();
            }
        }

        $this->getData();
    }

    /**
     * @param $path
     * @throws \ErrorException
     * @return array
     */
    private function getJson($path)
    {
        $data = [];

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

            //TODO - there must always be the key present
            $key = $data[$table]['key'];

            //TODO - if the key is provided, build the default
            if (empty($data[$table]['columns'][$key])) {
                $data[$table]['columns'] = [
                        $key => [
                            'name' => 'ID',
                            'display' => false,
                            'disabled' => true
                        ]
                    ] + $data[$table]['columns'];
            }
        }

        ksort($data);

        return $data;
    }

    /**
     * @return array
     */
    public function getMenu()
    {
        return $this->data;
    }

    /**
     * @return array
     * @throws \ErrorException
     */
    private function getData()
    {
        if (empty($this->controller->table)) {
            return $this->data;
        }

        if (empty($this->data[$this->controller->table]['columns'])) {
            throw new \ErrorException('No columns provided in '.$this->controller->table.'.json.');

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
                            //TODO - Do we need this? Modify to always include the join key!
                            $id = $row[$join['key']];
                            unset($row[$join['key']]);

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
            $key = $this->data[$this->controller->table]['key'];

            while ($row = $statement->fetch()) {
                $this->data[$this->controller->table]['rows'][$row[$key]] = $row;
            }
        }

        $this->managePlugins();

        return $this->data;
    }

    /**
     * @return void
     */
    private function managePlugins()
    {
        $this->pluginImage();
    }

    /**
     * @return void
     */
    private function pluginImage()
    {
        if (empty($this->controller->action)) {
            return;
        }

        $publicPath = __DIR__.'/../../public';
        $imageDomain = trim($this->config->get('admin', 'imageDomain'), '/');
        $imagePath = trim($this->config->get('admin', 'imagePath'), '/');

        if (!empty($_FILES)) {
            if (isset($_FILES['image']['error']) && $_FILES['image']['error'] === 0) {
                if (!is_dir($publicPath.'/'.$imagePath.'/'.$this->controller->table)) {
                    mkdir($publicPath.'/'.$imagePath.'/'.$this->controller->table);
                }

                $storage = new \Upload\Storage\FileSystem($publicPath.'/'.$imagePath.'/'.$this->controller->table, true);

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

            return;
        }

        $structure = [];

        $directory = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($publicPath.'/'.$imagePath, \RecursiveDirectoryIterator::SKIP_DOTS));

        foreach ($directory as $file ) {
            if ('.' === substr($file->getFilename(), 0, 1)) {
                continue;
            }

            $pathname = $file->getPathname();
            $dir = basename($file->getPath());

            //structure by modification time and filename
            $structure[$dir][$file->getMtime().$file->getFilename()] = str_replace($publicPath, '', $pathname);

            krsort($structure[$dir]);
        }

        $content = $this->factory->template()
            ->file(__DIR__.'/../Views/Admin/Plugins/Image')
            ->set('structure', $structure)
            ->set('data', $this->data)
            ->set('imageDomain', $imageDomain)
            ->set('imagePath', $imagePath)
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

        return;
    }

    /**
     * @return void
     */
    private function setData()
    {
        $data = null;

        if ($this->controller->action === 'add' || $this->controller->action === 'edit') {
            $this->managePlugins();

            $data = $this->createUpdateRow();
        } elseif ($this->controller->action === 'remove') {
            $data = $this->deleteRow();
        }

        //TODO - if requested with non-ajax then redirect
        if (empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            $response = $this->factory->response();
            $router = new UrlModel();

            $response
                ->header('Location: '.$router->admin($this->controller->table))
                ->send();
        }

        exit($data);
    }

    /**
     * @return integer
     */
    private function createUpdateRow()
    {
        $columns = [];
        $values = [];
        $valuesInsert = [];
        $valuesUpdate = [];

        foreach ($this->data[$this->controller->table]['columns'] as $columnName => $column) {
            if (!empty($column['disabled'])) {
                continue;
            }

            $values[':'.$columnName] = $_POST[$this->controller->action][$columnName];

            $columns[] = $this->db->backtick($columnName);

            $valuesInsert[] = ':'.$columnName;
            $valuesUpdate[] = $columnName.'=:'.$columnName;
        }

        $tableQuery = $this->db->backtick($this->controller->table);
        $columnsQuery = implode(',', $columns);

        $valuesInsertQuery = implode(',', $valuesInsert);
        $valuesUpdateQuery = implode(',', $valuesUpdate);

        if (empty($this->controller->id)) {
            $query = 'INSERT INTO '.$tableQuery.' ('.$columnsQuery.') VALUES ('.$valuesInsertQuery.');';

            $statement = $this->db->handle->prepare($query);
            return $statement->execute($values);
        }

        $key = $this->data[$this->controller->table]['key'];
        $values[':'.$key] = $this->controller->id;

        $query = '
            UPDATE '.$tableQuery.'
            SET '.$valuesUpdateQuery.'
            WHERE '.$this->db->backtick($key).' = :'.$key;

        $statement = $this->db->handle->prepare($query);

        return $statement->execute($values);
    }

    /**
     * @return boolean
     */
    private function deleteRow()
    {
        $key = $this->data[$this->controller->table]['key'];

        $query = '
            DELETE FROM
                '.$this->db->backtick($this->controller->table).'
            WHERE
                '.$this->db->backtick($key).' = :'.$key
            ;

        $statement = $this->db->handle->prepare($query);
        $statement->bindValue(':'.$key, $this->controller->id);

        return $statement->execute();
    }

    /**
     * @return void
     */
    private function setOrder()
    {
        if (!empty($_POST['order'])) {
            $values = [];

            $key = $this->data[$this->controller->table]['key'];

            $query = '
                UPDATE '.$this->db->backtick($this->controller->table).'
                SET '.$this->db->backtick($this->data[$this->controller->table]['order']['column']).' = CASE '.$this->db->backtick($key)
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