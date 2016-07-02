<?php

use PHPUnit\Framework\TestCase;

class BootstrapTest extends TestCase
{
    /**
     * @var \org\bovigo\vfs\vfsStreamDirectory
     */
    private $root;

    public function setUp()
    {
        if (!empty($uri = getenv('REQUEST_URI'))) {
            $_SERVER['REQUEST_URI'] = $uri;
        }

        if (!empty($method = getenv('REQUEST_METHOD'))) {
            $_SERVER['REQUEST_METHOD'] = $method;
        }

        $this->root = \org\bovigo\vfs\vfsStream::setup('root');
    }

    public function testInitThrowable()
    {
        \org\bovigo\vfs\vfsStream::newFile('Config.php')->at($this->root)->setContent('
        <?php
                return [
                    \'development\' => \'true\'
                ];
            ');

        $bootstrap = new \Propeller\Misc\Bootstrap();

        $bootstrap->configPath = $this->root->getChild('Config.php')->url();

        $bootstrap->initThrowable();
    }

    public function testInitRoute()
    {
        \org\bovigo\vfs\vfsStream::newFile('Routes.php')->at($this->root)->setContent('
        <?php
                return [
                    [
                        //TODO - http://www.restapitutorial.com/lessons/httpmethods.html
                        // POST - create, GET - read, PUT - update, DELETE - delete
                        [\'POST\', \'GET\', \'PUT\', \'DELETE\'],
                        \'/[{table}[/{key}]]\',
                        [new \Propeller\Controllers\FrontController(
                            new \Propeller\Models\PersistenceModel(),
                            new \Propeller\Models\TemplateModel(),
                            new \Propeller\Models\UrlModel(),
                            new \Propeller\Controllers\MainController(),
                            new \Propeller\Views\MainView()
                        ), \'init\']
                    ]
                ];
            ');

        $bootstrap = new \Propeller\Misc\Bootstrap();

        $bootstrap->routesPath = $this->root->getChild('Routes.php')->url();

        $bootstrap->initRoute();
    }
}

