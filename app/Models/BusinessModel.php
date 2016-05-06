<?php

namespace App\Models;

use Abimo\Factory;

class BusinessModel
{
    /**
     * @var Factory
     */
    protected $factory;

    /**
     * @var UrlModel
     */
    protected $urlModel;

    /**
     * @var PersistenceModel
     */
    public $persistenceModel;

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
     * BusinessModel constructor.
     * @param Factory $factory
     * @param UrlModel $urlModel
     */
    public function __construct(Factory $factory, UrlModel $urlModel)
    {
        $this->factory = $factory;
        $this->urlModel = $urlModel;

        $this->managePermissions();
        $this->structure = $this->getStructure();
    }

    /**
     * Manage the schema.
     * @return array
     */
    public function manageSchema()
    {
        return [];
    }

    /**
     * Manage the table.
     * @return array
     * @throws \ErrorException
     */
    public function manageTable()
    {
        if (!empty($_POST['order'])) {
            $this->persistenceModel->updateOrder($this->table);
        }

        return $this->persistenceModel->readRows($this->table);
    }

    /**
     * Manage the row.
     * @return array
     * @throws \ErrorException
     */
    public function manageRow()
    {
        if (!empty($_POST)) {
            $this->managePluginsPost();

            if ($this->action === 'create') {
                $this->persistenceModel->createRow($this->table, $this->id, $this->structure[$this->table]['columns'], $_POST);
            } elseif ($this->action === 'update') {
                $this->persistenceModel->updateRow($this->table, $this->id, $this->structure[$this->table]['columns'], $_POST);
            } elseif ($this->action === 'delete') {
                $this->persistenceModel->deleteRow($this->table, $this->id);
            }

            if (!empty($this->id)) {
                exit(json_encode($this->urlModel->admin($this->table)));
            }
        }

        $this->structure = $this->persistenceModel->readRows($this->table, $this->id);

        $this->managePluginsStructure();

        return $this->structure;
    }

    /**
     * Parse the .json files and prepare the structure.
     * @throws \ErrorException
     * @return array
     */
    public function getStructure()
    {
        if (!empty($this->structure)) {
            return $this->structure;
        }
        
        $structure = [];

        $config = $this->factory
            ->config()
            ->path(__DIR__.'/../../app/Config');

        $path = rtrim($config->get('admin', 'jsonPath'), '/');

        $iterator = new \DirectoryIterator($path);

        foreach ($iterator as $file) {
            $file = $file->getFilename();

            //TODO - skip hidden files
            if (substr($file, 0, 1) === '.') {
                continue;
            }

            $string = file_get_contents($path.'/'.$file);

            if (!$json = json_decode($string, true)) {
                throw new \ErrorException('Unable to decode '.$file);
            }

            $table = basename($file, '.json');

            $structure[$table] = $json;

            //TODO - there must always be the key present
            $key = $structure[$table]['key'];

            //TODO - if the key is not provided, build the default
            if (empty($structure[$table]['columns'][$key])) {
                $structure[$table]['columns'] = [
                        $key => [
                            'name' => 'ID',
                            'view' => false,
                            'attributes' => [
                                'disabled' => 'true'
                            ]
                        ]
                    ] + $structure[$table]['columns'];
            }
        }

        ksort($structure);

        return $this->structure = $structure;
    }

    /**
     * Manage CRUD permissions.
     * @throws \ErrorException
     */
    public function managePermissions()
    {
        if (!empty($this->action)) {
            if (empty($this->structure[$this->table][$this->action])) {
                throw new \ErrorException('Permission denied to: '.$this->action);
            }
        }
    }

    /**
     * Manage plugins post data.
     */
    public function managePluginsPost()
    {
        foreach ($this->structure[$this->table]['columns'] as $columnName => $column) {
            if (empty($column['plugin'])) {
                continue;
            }

            //TODO - improvement required
            $plugin = '\\App\\Plugins\\'.ucfirst($column['plugin']).'Plugin';

            $plugin = new $plugin($this->persistenceModel);

            $this->structure[$this->table]['plugin'][$columnName] = $plugin->managePost($columnName, $_POST[$columnName]);
        }
    }

    /**
     * Manage plugins structure.
     */
    public function managePluginsStructure()
    {
        foreach ($this->structure[$this->table]['columns'] as $columnName => $column) {
            if (empty($column['plugin'])) {
                continue;
            }

            //TODO - improvement required
            $plugin = '\\App\\Plugins\\'.ucfirst($column['plugin']).'Plugin';

            $plugin = new $plugin($this->persistenceModel);

            $this->structure[$this->table]['plugin'][$columnName] = $plugin->manageView($columnName);
        }
    }
}
