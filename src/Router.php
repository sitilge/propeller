<?php

/*
 * This file is part of Abimo.
 * 
 * The MIT License (MIT)
 *
 * Copyright (c) 2015 Martins Eglitis
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Abimo;

/**
 * Router class.
 *
 * @author Martins Eglitis
 */
class Router
{
    /**
     * The action to execute.
     *
     * @var mixed
     */
    public $action;

    /**
     * Args to pass to action.
     *
     * @var array
     */
    public $args = array();

    /**
     * An instance of container class.
     *
     * @var \Abimo\Injection\Container
     */
    public $container;

    /**
     * An instance of config class.
     *
     * @var callable
     */
    public $config;

    /**
     * An array of route-pattern mapping.
     *
     * @var array
     */
    public static $map;

    /**
     * An array of route patterns.
     *
     * @var array
     */
    public $patterns = array(
        ':number' => '?([0-9]+)',
        ':slug' => '?([^/]+)'
    );

    /**
     * An instance of request class.
     *
     * @var callable
     */
    public $request;

    /**
     * An array of routes.
     *
     * @var array
     */
    public static $routes = array();

    /**
     * Create a new response instance.
     *
     * @param  \Abimo\Injection\Container $container
     *
     * @return void
     */
    public function __construct(Injection\Container $container)
    {
        $this->container = $container;
        $this->config = $container['config'];
        $this->request = $container['request'];
    }

    /**
     * Get all routes.
     *
     * @return array
     */
    private function routes()
    {
		$routes = array();

        $this->container['loader']->requireFile(APP_PATH.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'Routes.php');

        if (array_key_exists($method = $this->request->method(), static::$routes)) {
            $routes = static::$routes[$method];
        }

        if (array_key_exists('all', static::$routes)) {
            $routes = $routes + static::$routes['all'];
        }

		if (empty($routes)) {
			throw new \InvalidArgumentException("No routes found. Maybe your Routes.php file is empty?", 100);
		}

        return $routes;
    }

    /**
     * Get url by route.
     *
     * @param  string $route
     * @param  array  $args
     *
     * @return mixed
     */
    public function url($route, $args = array())
    {
        if (empty(static::$map[$route])) {
            return false;
        }

		if (!is_array($args)) {
			$args = array($args);
		}

        $exploded = explode('/', static::$map[$route]);

        $url = array();

        foreach ($exploded as $exp) {
            if (false !== strpos($exp, ':')) {

                $arg = array_shift($args);

                if (null === $arg && false === strpos($exp, '?')) {
                    return false;
                } elseif (null !== $arg) {
                    $url[] = $arg;
                }

                continue;
            }

            $url[] = $exp;
        }

        $aliases = array_keys($this->patterns);
        $patterns = array_values($this->patterns);

        $pattern = str_replace($aliases, $patterns, static::$map[$route]);

        if (preg_match('~^'.$pattern.'$~', $url = implode('/', $url))) {
            return $url;
        }

        return false;
    }

    /**
     * Match request uri against routes.
     *
     * @return \Abimo\Router
     */
    public function match()
    {
		$method = $this->request->method();
        $uri = $this->request->uri();

        $routes = $this->routes();
        $aliases = array_keys($this->patterns);
        $patterns = array_values($this->patterns);

        foreach ($routes as $pattern => $action) {
            if (false !== strpos($pattern, ':')) {
                $pattern = str_replace($aliases, $patterns, $pattern);
            }
            if (preg_match('~^'.$pattern.'$~', $uri, $match)) {
                $this->action = $action;
                $this->args = str_replace('/', '', array_slice($match, 1));

                return $this;
            }
        }

        throw new \ErrorException("Route $uri not found. Have you defined it with the $method method (use *all* to match all methods) ?", 90);
    }

    /**
     * Register a new route.
     *
     * @return void
     */
    public static function register($method, $pattern, $routes, $action)
    {
        if (is_array($routes)) {
            foreach ($routes as $route) {
                static::$map[$route] = $pattern;
            }
        } else {
            static::$map[$routes] = $pattern;
        }

        static::$routes[$method][$pattern] = $action;
    }

    /**
     * Run router in respect to action and args.
     *
     * @return callable
     */
    public function run()
    {
        if (isset($this->action)) {
            if ($this->action instanceof \Closure) {
                return call_user_func_array($this->action, $this->args);
            } else {
                $actions = explode('.', $this->action);

                $class = '\App\Controllers\\'.array_shift($actions);
                $method = array_shift($actions);

                return call_user_func_array(array(new $class($this->container), $method), $this->args);
            }
        }
    }

    /**
     * Magically register a new route.
     *
     * @param  string $method
     * @param  array  $args
     *
     * @return void
     */
    public static function __callStatic($method, $args)
    {
        static::register($method, array_shift($args), array_shift($args), array_shift($args));
    }
}
