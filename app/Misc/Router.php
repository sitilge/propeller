<?php

namespace App\Misc;

use \Abimo;
use \FastRoute;

class Router
{
    /**
     * Router constructor.
     */
    public function __construct()
    {

        $factory = new Abimo\Factory();

        $frontController = $factory->config()->get('app', 'frontController');
        $frontController = new $frontController;
        
        $base_uri_path = $factory->config()->get('app','base_uri_path');

        $dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $collector) use ($frontController, $base_uri_path) {
            $collector->addRoute(['GET', 'POST'], $base_uri_path .'/[{table}[/{action}[/{id}]]]', [new $frontController, 'main']);
        });

        $request = $factory->request();

        $method = $request->method();
        $uri = $request->uri();

        $route = $dispatcher->dispatch($method, $uri);

        switch ($route[0]) {
            case FastRoute\Dispatcher::NOT_FOUND:
                $response = $factory->response();
                $response
                    ->header('404', true, 404)
                    ->send();

                throw new \ErrorException("Route $method $uri not found.");
                break;
            case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                $response = $factory->response();
                $response
                    ->header('405', true, 405)
                    ->send();

                throw new \ErrorException("Method $method not allowed.");
                break;
            case FastRoute\Dispatcher::FOUND:
                $handler = $route[1];
                $arguments = $route[2];

                call_user_func_array($handler, $arguments);
        }
    }
}
