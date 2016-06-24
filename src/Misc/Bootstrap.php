<?php

namespace Propeller\Misc;

use FastRoute\RouteCollector;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Handler\JsonResponseHandler;
use Whoops\Run;
use Whoops\Util\Misc;

class Bootstrap
{
    public function init()
    {
        $this->initThrowable();

        $this->initRoute();
    }

    private function initThrowable()
    {
        //TODO - move to attribute of the class
        $throwable = require __DIR__.'/../Config/Main/Config.php';

        if (empty($throwable['development'])) {
            $handler = $throwable['callable'];
        } else {
            $handler = new PrettyPageHandler();
        }

        $run = new Run();

        $run->pushHandler($handler);

        if (Misc::isAjaxRequest()) {
            if (empty($throwable['development'])) {
                //TODO - check here
            } else {
                $run->pushHandler(new JsonResponseHandler());
            }
        }

        $run->register();
    }

    private function initRoute()
    {
        $routes = require __DIR__.'/../Misc/Routes.php';

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
