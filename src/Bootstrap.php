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
 * Bootstrap class.
 *
 * @author Martins Eglitis
 */
class Bootstrap
{
    /**
     * Create a new bootstrap instance.
     *
     * @return void
     */
    public function __construct()
    {
        require SYS_PATH.DIRECTORY_SEPARATOR.'Injection'.DIRECTORY_SEPARATOR.'Provider.php';
        require SYS_PATH.DIRECTORY_SEPARATOR.'Injection'.DIRECTORY_SEPARATOR.'Container.php';

        $provider = new Injection\Provider(new Injection\Container());
        $container = $provider->register();

        $loader = $container['loader'];
        $loader->register();
        $loader->addNamespace('Abimo', SYS_PATH);
        $loader->addNamespace('App', APP_PATH);

        $throwable = $container['throwable'];
        $throwable->register();

        $bakery = $container['bakery'];

        $bakery->load();

        $router = $container['router'];

        $output = $router->match()->run();

        $bakery->save();

        $response = $container['response'];

        $response->send($output);
    }
}
