<?php

namespace Propeller\Misc;

use FastRoute\RouteCollector;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Handler\JsonResponseHandler;
use Whoops\Run;
use Whoops\Util\Misc;

class Bootstrap
{
    /**
     * @var string
     */
    public $configPath = __DIR__.'/../Config/Main/Config.php';

    /**
     * @var string
     */
    public $routesPath = __DIR__.'/Routes.php';

    /**
     * @var array
     */
    public $config = [];

    /**
     * @var array
     */
    public $routes = [];

    /**
     * The initial method called.
     *
     * @throws \ErrorException
     */
    public function init()
    {
        $this->initThrowable();

        $this->initRoute();
    }

    /**
     * Initialize the throwable handler.
     */
    public function initThrowable()
    {
        //TODO - move to attribute of the class
        $config = require $this->configPath;

        $run = new Run();

        if (empty($config['development'])) {
            $handler = $config['callable'];
        } else {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])
                && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                $handler = new JsonResponseHandler();
            } else {
                $handler = new PrettyPageHandler();
            }
        }

        $run->pushHandler($handler);

        $run->register();
    }

    /**
     * Initialize the route handler.
     *
     * @throws \ErrorException
     */
    public function initRoute()
    {
        $routes = require $this->routesPath;

        $dispatcher = \FastRoute\simpleDispatcher(function (RouteCollector $collector) use ($routes) {
            foreach ($routes as $route) {
                $collector->addRoute($route[0], $route[1], $route[2]);
            }
        });

        $method = $_SERVER['REQUEST_METHOD'];
        $uri = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

        $route = $dispatcher->dispatch($method, $uri);

        switch ($route[0]) {
            case \FastRoute\Dispatcher::NOT_FOUND:
                throw new \ErrorException("Route $method $uri not found.");
                break;
            case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                throw new \ErrorException("Method $method not allowed.");
                break;
            case \FastRoute\Dispatcher::FOUND:
                $handler = $route[1];
                $arguments = $route[2];

                call_user_func_array($handler, $arguments);
        }
    }
}
