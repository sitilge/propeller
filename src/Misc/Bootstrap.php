<?php

namespace Propeller\Misc;

use FastRoute\RouteCollector;
use Whoops\Handler\Handler;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Handler\JsonResponseHandler;
use Whoops\Run;

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
     * Initialize the throwable.
     */
    public function initThrowable()
    {
        //TODO - move to attribute of the class
        $config = require $this->configPath;

        if (empty($config['development'])) {
            $this->initHandler($config['callable']);

            return;
        }

        $server = filter_input_array(INPUT_SERVER);

        if (!empty($server['HTTP_X_REQUESTED_WITH'])
            && strtolower($server['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $this->initHandler(new JsonResponseHandler());

            return;
        }

        $this->initHandler(new PrettyPageHandler());
    }

    /**
     * Initialize the throwable handler.
     *
     * @param Handler $handler
     */
    private function initHandler(Handler $handler)
    {
        $run = new Run();

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

        $method = filter_input(INPUT_SERVER, 'REQUEST_METHOD');
        $uri = rawurldecode(parse_url(filter_input(INPUT_SERVER, 'REQUEST_URI'), PHP_URL_PATH));

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
