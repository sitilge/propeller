<?php

namespace App\Plugins;


use Abimo\Factory;
use Upload\File;
use Upload\Storage\FileSystem;
use Upload\Validation\Mimetype;
use Upload\Validation\Size;
use ImageOptimizer\OptimizerFactory;
use Webpatser\Sanitize\Sanitize;
use App\Plugins\AttachmentPlugin;
use App\Models\BusinessModel;
use Abimo\Config;
use Abimo\Request;
use Abimo\Template;


class ImagePlugin implements PluginInterface {
  
    /* @var Config */
    public $config ;
    
    /* @var BusinessModel */
    public $businessmodel ;
  
    function __construct( BusinessModel $businessmodel, Config $config ) {
      $this->businessmodel = $businessmodel ;
      $this->config = $config ;
    }

    # lifted from businessmodel - currently being heavily refactored. 
    public function pluginImage()
    {
      
        //TODO - image plugin works only in the row view
        if (empty($this->businessmodel->action)) {
            return;
        }



        $baseUrl = $this->businessmodel->factory->config()->get('app', 'baseUrl');
        $imagePublicPath = rtrim($this->businessmodel->config->get('admin', 'imagePublicPath'), '/');
        $imageDomain = trim($this->businessmodel->config->get('admin', 'imageDomain'), '/');
        $imageDir = trim($this->businessmodel->config->get('admin', 'imageDir'), '/');

        if (!empty($_FILES)) {
            if (isset($_FILES['image']['error']) && $_FILES['image']['error'] === 0) {
                if (!is_dir($imagePublicPath.'/'.$imageDir.'/'.$this->businessmodel->table)) {
                    mkdir($imagePublicPath.'/'.$imageDir.'/'.$this->businessmodel->table);
                }

                $storage = new FileSystem($imagePublicPath.'/'.$imageDir.'/'.$this->businessmodel->table, true);

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
//                $optimizer->optimize($imagePublicPath.'/'.$imageDir.'/'.$this->businessmodel->table.'/'.$file->getNameWithExtension());
            }

            return;
        }

        $structure = [];

        $directory = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($imagePublicPath.'/'.$imageDir, \RecursiveDirectoryIterator::SKIP_DOTS));

        foreach ($directory as $file ) {
            if ('.' === substr($file->getFilename(), 0, 1)) {
                continue;
            }

            $pathname = $file->getPathname();
            $dir = basename($file->getPath());

            //structure by modification time and filename
            $structure[$dir][$file->getMtime().$file->getFilename()] = str_replace($imagePublicPath, '', $pathname);

            krsort($structure[$dir]);
        }

        $content = $this->businessmodel->factory->template()
            ->file(__DIR__.'/../Views/Admin/Plugins/Image')
            ->set('structure', $structure)
            ->set('data', $this->businessmodel->data)
            ->set('baseUrl', $baseUrl)
            ->set('imageDomain', $imageDomain)
            ->set('imageDir', $imageDir)
            ->set('table', $this->businessmodel->table)
            ->set('action', $this->businessmodel->action)
            ->set('id', $this->businessmodel->id);

        foreach ($this->businessmodel->data[$this->businessmodel->table]['columns'] as $columnName => $column) {
            if (isset($column['plugin']) && $column['plugin'] == 'image') {
                $this->businessmodel->data[$this->businessmodel->table]['plugins'][$columnName] = $content
                    ->set('column', $columnName)
                    ->render();
            }
        }
    }


  public function postHandler( $columnname, Request $request ) {
    
    
    #TODO: handles only one image per record. make this work for multiple columns
    #   note that this method will be called once, separately, 
    #   for each column in a record designated for the image plugin
    

        $baseUrl = $this->businessmodel->factory->config()->get('app', 'baseUrl');
        $imagePublicPath = rtrim($this->businessmodel->factory->config()->get('admin', 'imagePublicPath'), '/');
        $imageDomain = trim($this->businessmodel->factory->config()->get('admin', 'imageDomain'), '/');
        $imageDir = trim($this->businessmodel->factory->config()->get('admin', 'imageDir'), '/');

        if (!empty($_FILES)) {
            if (isset($_FILES['image']['error']) && $_FILES['image']['error'] === 0) {
                if (!is_dir($imagePublicPath.'/'.$imageDir.'/'.$this->businessmodel->table)) {
                    mkdir($imagePublicPath.'/'.$imageDir.'/'.$this->businessmodel->table);
                }

                $storage = new FileSystem($imagePublicPath.'/'.$imageDir.'/'.$this->businessmodel->table, true);

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
//                $optimizer->optimize($imagePublicPath.'/'.$imageDir.'/'.$this->businessmodel->table.'/'.$file->getNameWithExtension());
            }

            return;
        }

    
    
  }

  public function viewTemplate( $columnname, Template $template ) {
    
        $baseUrl = $this->businessmodel->factory->config()->get('app', 'baseUrl');
        $imagePublicPath = rtrim($this->businessmodel->factory->config()->get('admin', 'imagePublicPath'), '/');
        $imageDomain = trim($this->businessmodel->factory->config()->get('admin', 'imageDomain'), '/');
        $imageDir = trim($this->businessmodel->factory->config()->get('admin', 'imageDir'), '/');

    #TODO: breakout $structure generation into a separate method, and cache it. 
    
        $structure = [];

        $directory = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($imagePublicPath.'/'.$imageDir, \RecursiveDirectoryIterator::SKIP_DOTS));

        foreach ($directory as $file ) {
            if ('.' === substr($file->getFilename(), 0, 1)) {
                continue;
            }

            $pathname = $file->getPathname();
            $dir = basename($file->getPath());

            //structure by modification time and filename
            $structure[$dir][$file->getMtime().$file->getFilename()] = str_replace($imagePublicPath, '', $pathname);

            krsort($structure[$dir]);
        }

        $template
            ->file(__DIR__.'/../Views/Admin/Plugins/Image')
            ->set('structure', $structure)
            ->set('data', $this->businessmodel->data)
            ->set('baseUrl', $baseUrl)
            ->set('imageDomain', $imageDomain)
            ->set('imageDir', $imageDir)
            ->set('table', $this->businessmodel->table)
            ->set('action', $this->businessmodel->action)
            ->set('id', $this->businessmodel->id)
            ->set('column', $columnname)
            ;

  }
  
}
