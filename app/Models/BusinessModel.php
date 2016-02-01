<?php

namespace App\Models;

use Abimo\Factory;
use Upload\File;
use Upload\Storage\FileSystem;
use Upload\Validation\Mimetype;
use Upload\Validation\Size;

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
     * @throws \ErrorException
     * @return void
     */
    public function manageData()
    {
        $persistenceModel = new PersistenceModel($this);

        $this->getJson();

        if (!empty($_POST)) {
            if (!empty($_POST['order'])) {
                $persistenceModel->updateOrder();
            } else {
                $data = null;

                if ($this->action === 'create') {
                    $this->managePlugins();

                    $data = $persistenceModel->createRow();
                } elseif ($this->action === 'update') {
                    $this->managePlugins();

                    $data = $persistenceModel->updateRow();
                } elseif ($this->action === 'remove') {
                    $data = $persistenceModel->deleteRow();
                }

                //TODO - if requested with non-ajax then redirect
                if (empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                    $response = $this->factory->response();
                    $url = new UrlModel();

                    $response
                        ->header('Location: '.$url->admin($this->table))
                        ->send();
                }

                exit($data);
            }
        }

        $persistenceModel->getData();
    }

    /**
     * @throws \ErrorException
     * @return array
     */
    public function getJson()
    {
        $path = rtrim($this->config->get('admin', 'jsonPath'), '/');

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
                            'view' => false,
                            'disabled' => true
                        ]
                    ] + $data[$table]['columns'];
            }
        }

        ksort($data);

        return $this->data = $data;
    }

    /**
     * @return void
     */
    public function managePlugins()
    {
        $this->pluginImage();
    }

    /**
     * @return void
     */
    private function pluginImage()
    {
        //TODO - image plugin works only in the row view
        if (empty($this->action)) {
            return;
        }

        $publicPath = __DIR__.'/../../public';
        $imageDomain = trim($this->config->get('admin', 'imageDomain'), '/');
        $imagePath = trim($this->config->get('admin', 'imagePath'), '/');

        if (!empty($_FILES)) {
            if (isset($_FILES['image']['error']) && $_FILES['image']['error'] === 0) {
                if (!is_dir($publicPath.'/'.$imagePath.'/'.$this->table)) {
                    mkdir($publicPath.'/'.$imagePath.'/'.$this->table);
                }

                $storage = new FileSystem($publicPath.'/'.$imagePath.'/'.$this->table, true);

                $file = new File('image', $storage);

                $file
                    ->addValidations([
                        new Mimetype([
                            'image/png',
                            'image/jpg',
                            'image/jpeg',
                            'image/gif',
                            'image/tif',
                        ]),
                        new Size('5M')
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
            ->set('table', $this->table)
            ->set('action', $this->action)
            ->set('id', $this->id);

        foreach ($this->data[$this->table]['columns'] as $columnName => $column) {
            if (isset($column['type']) && $column['type'] == 'image') {
                $this->data[$this->table]['plugins'][$columnName] = $content
                    ->set('column', $columnName)
                    ->render();
            }
        }
    }
}