<?php

namespace App\Models;

use Abimo\Factory;

function dd($input)
{
    echo "<pre>";
    print_r($input);
    exit;
}

class AdminModel
{
    public $config = [];

    public $data = [];

    public $tablesConfigPath = __DIR__.'/../Misc/Admin/Tables';

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

    public function getConfig()
    {
        $this->data = $this->getJson($this->tablesConfigPath);

        return $this;
    }

    public function getJson($path)
    {
        $directory = new \DirectoryIterator($path);

        foreach ($directory as $file) {
            if (!$file->isFile()) {
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

    public function getAuth()
    {
        if (empty($_SESSION['admin'])) {
            if (empty($_POST)) {
                echo $this->factory->template()
                    ->file(__DIR__.'/../Views/Admin/Auth')
                    ->render();

                exit;
            }

            $data = $this->getJson(__DIR__.'/../Misc/Admin');

            $table = array_keys($data)[0];

            $email = $_POST['email'];
            $password = $_POST['password'];

            $query = '
                SELECT
                    '.$this->db->backtick($data[$table]['key']).' AS id,
                    '.$this->db->backtick($data[$table]['columns']['name']).' AS name,
                    '.$this->db->backtick($data[$table]['columns']['email']).' AS email,
                    '.$this->db->backtick($data[$table]['columns']['password']).' AS password,
                    '.$this->db->backtick($data[$table]['columns']['level']).' AS level
                FROM
                    '.$this->db->backtick($table).'
                WHERE
                    '.$this->db->backtick($data[$table]['columns']['email']).' = :email
                    AND
                    '.$this->db->backtick($data[$table]['columns']['password']).' = :password
                LIMIT 1
            ';

            $statement = $this->db->handle->prepare($query);
            $statement->bindValue(':email', $email);
            $statement->bindValue(':password', $password);

            $statement->execute();

            if (empty($user = $statement->fetch())) {
                throw new \ErrorException('No user '.$email.' and password combination found.');
            }

            $_SESSION['admin'] = $user;

            $response = $this->factory->response();

            $router = new UrlModel();

            $location = 'Location: '.$router->admin();

            $response
                ->header($location)
                ->send();

            exit;
        }
    }
    
    public function getMenu()
    {
//        echo "<pre>";
//        print_r($this->data);
//        dd($_SESSION);
        $menu = [];

        foreach ($this->data as $table => $config) {
            if (
                empty($_SESSION['admin']) ||
                empty($this->data[$table]['level']) ||
                (!empty($this->data[$table]['level']) &&
                $this->data[$table]['level'] <= $_SESSION['admin']['level'])
            ) {
                $menu[$table] = [
                    'name' => !empty($config['name']) ? $config['name'] : $table
                ];
            }
        }

        return $menu;
    }
    
    public function getContent()
    {
        $content = $this->factory->template()
            ->set('router', new UrlModel())
            ->set('data', $this->data)
            ->set('table', $this->controller->table)
            ->set('action', $this->controller->action)
            ->set('id', $this->controller->id)
            ;

        if (empty($this->controller->action)) {
            $content = $content
                ->file(__DIR__.'/../Views/Admin/Table');
        } else {
            $content = $content
                ->file(__DIR__.'/../Views/Admin/Row');
        }

        return $content->render();
    }

    public function getData()
    {
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
    }

    public function getPlugins()
    {
        foreach ($this->data[$this->controller->table]['columns'] as $columnName => $column) {
            if (!empty($column['type'])) {
                switch ($column['type']) {
                    case 'image':
                        $this->data[$this->controller->table]['plugins'][$columnName] = $this->pluginImage($columnName);

                        break;
                }
            }
        }
    }

    public function pluginImage($column)
    {
        $structure = [];

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->publicPath.'/'.$this->imageDir, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
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
            ->set('column', $column)
            ->set('table', $this->controller->table)
            ->set('action', $this->controller->action)
            ->set('id', $this->controller->id)
            ->render();

        return $content;
    }
    
    public function setData()
    {
        if ($this->controller->action === 'add' || $this->controller->action === 'edit') {
            $id = $this->createUpdateRow();

            if ($this->controller->action === 'add') {
                $response = $this->factory->response();
                $router = new UrlModel();

                $location = 'Location: '.$router->admin($this->controller->table);

                $response
                    ->header($location)
                    ->send();

                exit;
            }
        } elseif ($this->controller->action === 'remove') {
            $this->deleteRow();
        }
    }
    
    public function createUpdateRow()
	{
        if (isset($_FILES['image']['error']) && $_FILES['image']['error'] === 0) {
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