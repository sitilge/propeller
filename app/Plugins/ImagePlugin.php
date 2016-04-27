<?php

namespace App\Plugins;

use Abimo\Factory;
use App\Models\UrlModel;
use App\Models\PersistenceModel;
use Upload\File;
use Upload\Storage\FileSystem;
use Upload\Validation\Mimetype;
use Upload\Validation\Size;
use ImageOptimizer\OptimizerFactory;
use Webpatser\Sanitize\Sanitize;

class ImagePlugin implements PluginInterface
{
    /**
     * @var PersistenceModel
     */
    protected $persistenceModel;

    /**
     * @var Factory
     */
    protected $factory;

    /**
     * @var UrlModel
     */
    protected $urlModel;

    /**
     * @var array
     */
    protected $structure;

    /**
     * @var string
     */
    protected $table;

    /**
     * @var string
     */
    protected $action;

    /**
     * @var string
     */
    protected $id;

    /**
     * ImagePlugin constructor.
     * @param PersistenceModel $persistenceModel
     */
    public function __construct(PersistenceModel $persistenceModel)
    {
        $this->persistenceModel = $persistenceModel;
        $this->structure = $this->persistenceModel->structure;
        $this->table = $this->persistenceModel->table;
        $this->action = $this->persistenceModel->action;
        $this->id = $this->persistenceModel->id;

        $this->factory = new Factory();
        $this->urlModel = new UrlModel(new Factory());
    }

    /**
     * Manage plugin post data.
     * @param $column
     * @param $data
     * @return void
     */
    public function managePost($column, $data)
    {
        $imagePublicPath = rtrim($this->factory->config()->get('admin', 'imagePublicPath'), '/');
        $imageDir = trim($this->factory->config()->get('admin', 'imageDir'), '/');

        if (!empty($_FILES)) {
            if (isset($_FILES['image']['error']) && $_FILES['image']['error'] === 0) {
                if (!is_dir($imagePublicPath.'/'.$imageDir.'/'.$this->table)) {
                    mkdir($imagePublicPath.'/'.$imageDir.'/'.$this->table);
                }

                $storage = new FileSystem($imagePublicPath.'/'.$imageDir.'/'.$this->table, true);

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
//                $optimizer->optimize($imagePublicPath.'/'.$imageDir.'/'.$this->table.'/'.$file->getNameWithExtension());
            }

            $this->persistenceModel->updateRow($this->table, $this->id, $column, $data);
        }
    }

    /**
     * Manage plugin view.
     * @param $column
     * @return string
     */
    public function manageView($column)
    {
        $baseUrl = $this->factory->config()->get('app', 'baseUrl');
        $imagePublicPath = rtrim($this->factory->config()->get('admin', 'imagePublicPath'), '/');
        $imageDomain = trim($this->factory->config()->get('admin', 'imageDomain'), '/');
        $imageDir = trim($this->factory->config()->get('admin', 'imageDir'), '/');

        $directories = [];

        $directory = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($imagePublicPath.'/'.$imageDir, \RecursiveDirectoryIterator::SKIP_DOTS));

        foreach ($directory as $file) {
            if ('.' === substr($file->getFilename(), 0, 1)) {
                continue;
            }

            $pathname = $file->getPathname();
            $dir = basename($file->getPath());

            //structure by modification time and filename
            $directories[$dir][$file->getMtime().$file->getFilename()] = str_replace($imagePublicPath, '', $pathname);

            krsort($directories[$dir]);
        }

        return $this->factory->template()
            ->file(__DIR__.'/../Views/Plugins/Image')
            ->set('directories', $directories)
            ->set('baseUrl', $baseUrl)
            ->set('imageDomain', $imageDomain)
            ->set('imageDir', $imageDir)
            ->set('url', $this->urlModel)
            ->set('structure', $this->structure)
            ->set('table', $this->table)
            ->set('action', $this->action)
            ->set('id', $this->id)
            ->set('column', $column)
            ->set('data', $this->structure[$this->table]['rows'][$this->id][$column])
            ->render()
            ;
    }
}