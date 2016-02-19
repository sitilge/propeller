<?php

namespace App\Models;

use Abimo\Factory;
use Upload\File;
use Upload\Storage\FileSystem;
use Upload\Validation\Mimetype;
use Upload\Validation\Size;
use ImageOptimizer\OptimizerFactory;
use Webpatser\Sanitize\Sanitize;

class BusinessModel
{
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
     * @var Factory
     */
    private $factory;

    /**
     * @var \Abimo\Config
     */
    private $config;

    /**
     * @var array
     */
    public $data = [];

    /**
     * BusinessModel constructor.
     *
     * @param $table
     * @param $action
     * @param $id
     */
    public function __construct($table = null, $action = null, $id = null)
    {
        $this->table = $table;
        $this->action = $action;
        $this->id = $id;

        $this->factory = new Factory();

        $this->config = $this->factory->config();
    }

    /**
     * CRUD based on the data.
     *
     * @throws \ErrorException
     * @return void
     */
    public function manageData()
    {
        $persistenceModel = new PersistenceModel($this);

        $this->getJson();

        $this->managePermissions();

        if (!empty($_POST)) {
            if (!empty($_POST['order'])) {
                $persistenceModel->updateOrder();
            } else {
                $response = $this->factory->response();
                $url = new UrlModel();

                if ($this->action === 'create') {
                    $this->managePlugins();

                    $persistenceModel->createRow();

                    $response
                        ->header('Location: '.$url->admin($this->table))
                        ->send();

                    exit;
                } elseif ($this->action === 'update') {
                    $this->managePlugins();

                    $persistenceModel->updateRow();

                    $response
                        ->header('Location: '.$url->admin($this->table))
                        ->send();

                    exit;
                } elseif ($this->action === 'delete') {
                    $persistenceModel->deleteRow();

                    if (!empty($this->id)) {
                        exit(json_encode($url->admin($this->table)));
                    }
                }
            }
        }

        $persistenceModel->readRows();
    }

    /**
     * Parse the .json files and prepare the structure.
     *
     * @throws \ErrorException
     * @return array
     */
    public function getJson()
    {
        $path = rtrim($this->config->get('admin', 'jsonPath'), '/');

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

            $data[$table] = $json;

            //TODO - there must always be the key present
            $key = $data[$table]['key'];

            //TODO - if the key is not provided, build the default
            if (empty($data[$table]['columns'][$key])) {
                $data[$table]['columns'] = [
                        $key => [
                            'name' => 'ID',
                            'view' => false,
                            'attributes' => [
                                'disabled' => 'true'
                            ]
                        ]
                    ] + $data[$table]['columns'];
            }
        }

        ksort($data);

        return $this->data = $data;
    }

    /**
     * Manage CRUD permissions.
     *
     * @throws \ErrorException
     */
    public function managePermissions()
    {
        if (!empty($this->action)) {
            switch($this->action) {
                case 'create':
                    if (empty($this->data[$this->table]['create'])) {
                        throw new \ErrorException('Permission denied to: '.$this->action);
                    }
                    break;
                case 'update':
                    if (empty($this->data[$this->table]['update'])) {
                        throw new \ErrorException('Permission denied to: '.$this->action);
                    }
                    break;
                case 'delete':
                    if (empty($this->data[$this->table]['delete'])) {
                        throw new \ErrorException('Permission denied to: '.$this->action);
                    }
                    break;
            };
        }
    }

    /**
     * Manage the plugins.
     *
     * @return void
     */
    public function managePlugins()
    {
        $this->pluginImage();
    }

    /**
     * Build the image plugin.
     *
     * @return void
     */
    private function pluginImage()
    {
//        phpinfo();
//        exit;

        //TODO - image plugin works only in the row view
        if (empty($this->action)) {
            return;
        }

        $publicPath = rtrim($this->config->get('admin', 'publicPath'), '/');
        $imageDomain = trim($this->config->get('admin', 'imageDomain'), '/');
        $imageDir = trim($this->config->get('admin', 'imageDir'), '/');

        if (!empty($_FILES)) {
            if (isset($_FILES['image']['error']) && $_FILES['image']['error'] === 0) {
                if (!is_dir($publicPath.'/'.$imageDir.'/'.$this->table)) {
                    mkdir($publicPath.'/'.$imageDir.'/'.$this->table);
                }

                $storage = new FileSystem($publicPath.'/'.$imageDir.'/'.$this->table, true);

                $file = new File('image', $storage);

//                TODO - sanitize the filename
                $filename = Sanitize::string($file->getName());

                $file
                    ->setName($filename)
                    ->addValidations([
                        new Mimetype([
                            'image/png',
                            'image/jpg',
                            'image/jpeg',
                            'image/gif'
                        ]),
                        new Size('5M')
                    ]);

                $file->upload();

//                TODO - uncomment to optimize the image
//                $optimizer = new OptimizerFactory([
//                    'ignore_errors' => false
//                ]);
//
//                $optimizer = $optimizer->get();
//
//                $optimizer->optimize($publicPath.'/'.$imageDir.'/'.$this->table.'/'.$file->getNameWithExtension());
            }

            return;
        }

        $structure = [];

        $directory = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($publicPath.'/'.$imageDir, \RecursiveDirectoryIterator::SKIP_DOTS));

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
            ->set('imageDir', $imageDir)
            ->set('table', $this->table)
            ->set('action', $this->action)
            ->set('id', $this->id);

        foreach ($this->data[$this->table]['columns'] as $columnName => $column) {
            if (isset($column['plugin']) && $column['plugin'] == 'image') {
                $this->data[$this->table]['plugins'][$columnName] = $content
                    ->set('column', $columnName)
                    ->render();
            }
        }
    }
}