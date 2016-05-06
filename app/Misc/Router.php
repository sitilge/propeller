<?php

namespace App\Misc;

use Abimo\Factory;
use App\Models\BusinessModel;
use App\Models\PersistenceModel;
use App\Models\UrlModel;
use FastRoute\RouteCollector;

class Router
{
    /**
     * Router constructor.
     * @param Factory $factory
     * @throws \ErrorException
     */
    public function __construct(Factory $factory)
    {
        $config = $factory
            ->config()
            ->path(__DIR__.'/../../app/Config');

        $frontController = $config->get('app', 'frontController');

        $baseUrl = rtrim($config->get('app', 'baseUrl'), '/');

        $dispatcher = \FastRoute\simpleDispatcher(function(RouteCollector $collector) use ($frontController, $baseUrl) {
            //TODO : S.T.U.P.I.D
            $collector->addRoute(['GET', 'POST'], $baseUrl.'/[{table}[/{action}[/{id}]]]', [
                new $frontController(
                    new Factory(),
                    new BusinessModel(
                        new Factory(),
                        new UrlModel(new Factory())),
                    new PersistenceModel(new Factory()),
                    new UrlModel(new Factory())
                ), 'main']);
        });

        $method = $_SERVER['REQUEST_METHOD'];
        $uri = $factory->request()->uri();

        $route = $dispatcher->dispatch($method, $uri);

        switch ($route[0]) {
            case \FastRoute\Dispatcher::NOT_FOUND:
                $response = $factory->response();
                $response
                    ->header('HTTP/1.1 404 Not Found', true, 404)
                    ->send();

                throw new \ErrorException("Route $method $uri not found.");
                break;
            case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                $response = $factory->response();
                $response
                    ->header('HTTP/1.1 405 Method Not Allowed', true, 405)
                    ->send();

                throw new \ErrorException("Method $method not allowed.");
                break;
            case \FastRoute\Dispatcher::FOUND:
                $handler = $route[1];
                $arguments = $route[2];

                call_user_func_array($handler, $arguments);
        }
    }
}