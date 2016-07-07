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
    }

    public function testInitThrowable()
    {
        $root = \org\bovigo\vfs\vfsStream::setup('root');

        \org\bovigo\vfs\vfsStream::newFile('Config.php')->at($root)->setContent('
        <?php
                return [
                    \'development\' => \'true\'
                ];
            ');

        $bootstrap = new \Propeller\Misc\Bootstrap();

        $bootstrap->configPath = $root->getChild('Config.php')->url();

        $bootstrap->initThrowable();
    }

    public function testInitRoute()
    {
        $root = \org\bovigo\vfs\vfsStream::setup('root');

        \org\bovigo\vfs\vfsStream::newFile('Routes.php')->at($root)->setContent('
        <?php
                return [
                    [
                        [\'POST\', \'GET\', \'PUT\', \'DELETE\'],
                        \'/[{table}[/{key}]]\',
                        [new \Propeller\Controllers\FrontController(
                            new \Propeller\Models\PersistenceModel(),
                            new \Propeller\Models\OrmModel(),
                            new \Propeller\Models\TemplateModel(),
                            new \Propeller\Models\UrlModel(),
                            new \Propeller\Controllers\MainController(),
                            new \Propeller\Views\MainView()
                        ), \'init\']
                    ]
                ];
            ');

        $bootstrap = new \Propeller\Misc\Bootstrap();

        $bootstrap->routesPath = $root->getChild('Routes.php')->url();

        $bootstrap->initRoute();
    }
}
