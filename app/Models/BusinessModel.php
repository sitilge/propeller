<?php

namespace App\Models;

use Abimo\Factory;
use App\Controllers\FrontController;
use Upload\File;
use Upload\Storage\FileSystem;
use Upload\Validation\Mimetype;
use Upload\Validation\Size;

class BusinessModel
{
    /**
     * @var Factory
     */
    private $factory;

    /**
     * @var \Abimo\Config
     */
    private $config;

    /**
     * @var FrontController
     */
    private $frontController;

    /**
     * @var array
     */
    public $data = [];

    /**
     * @var PersistenceModel
     */
    private $persistenceModel;

    /**
     * AdminModel constructor.
     *
     * @param FrontController $frontController
     */
    public function __construct(FrontController $frontController)
    {
        $this->frontController = $frontController;

        $this->factory = new Factory();

        $this->config = $this->factory->config();

        $this->persistenceModel = new PersistenceModel($this->frontController, $this);
    }

    /**
     * @throws \ErrorException
     * @return void
     */
    public function manageData()
    {
        $jsonPath = rtrim($this->config->get('admin', 'jsonPath'), '/');

        $this->getJson($jsonPath);

        if (!empty($_POST)) {
            if (!empty($_POST['order'])) {
                $this->persistenceModel->updateOrder();
            } else {
                $data = null;

                if ($this->frontController->action === 'create') {
                    $this->managePlugins();

                    $data = $this->persistenceModel->createRow();
                } elseif ($this->frontController->action === 'update') {
                    $this->managePlugins();

                    $data = $this->persistenceModel->updateRow();
                } elseif ($this->frontController->action === 'remove') {
                    $data = $this->persistenceModel->deleteRow();
                }

                //TODO - if requested with non-ajax then redirect
                if (empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                    $response = $this->factory->response();
                    $url = new UrlModel();

                    $response
                        ->header('Location: '.$url->admin($this->frontController->table))
                        ->send();
                }

                exit($data);
            }
        }

        $this->persistenceModel->getData();
    }

    /**
     * @param $path
     * @throws \ErrorException
     * @return array
     */
    private function getJson($path)
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
        if (empty($this->frontController->action)) {
            return;
        }

        $publicPath = __DIR__.'/../../public';
        $imageDomain = trim($this->config->get('admin', 'imageDomain'), '/');
        $imagePath = trim($this->config->get('admin', 'imagePath'), '/');

        if (!empty($_FILES)) {
            if (isset($_FILES['image']['error']) && $_FILES['image']['error'] === 0) {
                if (!is_dir($publicPath.'/'.$imagePath.'/'.$this->frontController->table)) {
                    mkdir($publicPath.'/'.$imagePath.'/'.$this->frontController->table);
                }

                $storage = new FileSystem($publicPath.'/'.$imagePath.'/'.$this->frontController->table, true);

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
            ->set('table', $this->frontController->table)
            ->set('action', $this->frontController->action)
            ->set('id', $this->frontController->id);

        foreach ($this->data[$this->frontController->table]['columns'] as $columnName => $column) {
            if (isset($column['type']) && $column['type'] == 'image') {
                $this->data[$this->frontController->table]['plugins'][$columnName] = $content
                    ->set('column', $columnName)
                    ->render();
            }
        }
    }
}