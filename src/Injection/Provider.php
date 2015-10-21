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

namespace Abimo\Injection;

/**
 * Injection provider class.
 *
 * @author Martins Eglitis
 */
class Provider
{
    public $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function register()
    {
        $this->container['bakery'] = $this->container->share(function ($c)
        {
            return new \Abimo\Bakery($c['cookie'], $c['session']);
        });

        $this->container['config'] = $this->container->share(function ($c)
        {
            return new \Abimo\Config($c['loader']);
        });

        $this->container['cookie'] = $this->container->share(function ($c)
        {
            return new \Abimo\Cookie($c['config']);
        });

        $this->container['database'] = $this->container->share(function ($c) {
            return \Abimo\Database::singleton($c['database_'.$c['config']->database['driver']]);
        });

        $this->container['database_mysql'] = $this->container->share(function ($c) {
            return new \Abimo\Database\Mysql($c['config']);
        });

        $this->container['database_pdo'] = $this->container->share(function ($c) {
            return new \Abimo\Database\Pdo($c['config']);
        });

        $this->container['database_sqlite'] = $this->container->share(function ($c) {
            return new \Abimo\Database\Sqlite($c['config']);
        });

        $this->container['helper'] = $this->container->share(function () {
            return new \Abimo\Helper();
        });

        $this->container['loader'] = $this->container->share(function () {
            require SYS_PATH.DIRECTORY_SEPARATOR.'Loader.php';
            return new \Abimo\Loader();
        });

        $this->container['request'] = $this->container->share(function () {
            return new \Abimo\Request();
        });

        $this->container['response'] = $this->container->share(function ($c) {
            return new \Abimo\Response($c['cookie']);
        });

        $this->container['router'] = $this->container->share(function ($c) {
            return new \Abimo\Router($c);
        });

        $this->container['services'] = $this->container->share(function ($c) {
            return new \Abimo\Services($c);
        });

        $this->container['session'] = $this->container->share(function ($c) {
            return \Abimo\Session::singleton($c['session_'.$c['config']->session['driver']]);
        });

        $this->container['session_database'] = $this->container->share(function ($c) {
            return new \Abimo\Session\Database($c['config']);
        });

        $this->container['session_memcached'] = $this->container->share(function ($c) {
            return new \Abimo\Session\Memcached($c['config']);
        });

        $this->container['session_redis'] = $this->container->share(function ($c) {
            return new \Abimo\Session\Redis($c['config']);
        });

        //TODO - check sharing
        $this->container['template'] = function ($c) {
            return new \Abimo\Template($c['loader']);
        };

        $this->container['throwable'] = $this->container->share(function ($c) {
            return new \Abimo\Throwable($c['config'], $c['router'], $c['template']);
        });

        //TODO - check sharing
        $this->container['validator'] = function () {
            return new \Abimo\Validator();
        };

        return $this->container;
    }
}