<?php

namespace App\Misc;

use \Abimo;
use \FastRoute;

class Router
{
    public function __construct()
    {
        $dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $collector) {
            $collector->addRoute(['GET'], '/logout', [new \App\Controllers\AdminController(), 'logout']);
            $collector->addRoute(['GET', 'POST'], '/[{table}[/{action}[/{id}]]]', [new \App\Controllers\AdminController(), 'main']);
        });

        $factory = new Abimo\Factory();
        $request = $factory->request();

        $method = $request->method();
        $uri = $request->uri();

        $route = $dispatcher->dispatch($method, $uri);

        switch ($route[0]) {
            case FastRoute\Dispatcher::NOT_FOUND:
                throw new \ErrorException("Route $method $uri not found.");
                break;
            case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                //TODO - set 405 Method Not Allowed
                throw new \ErrorException("Method $method not allowed.");
                break;
            case FastRoute\Dispatcher::FOUND:
                $handler = $route[1];
                $arguments = $route[2];

                call_user_func_array($handler, $arguments);
        }
    }
}