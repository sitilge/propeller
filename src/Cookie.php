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
 * Cookie class.
 *
 * @author Martins Eglitis
 */
class Cookie
{
    /**
     * The cookie config array.
     *
     * @var array
     */
    public $config = array();

    /**
     * Data for bakery to be saved.
     *
     * @var array
     */
    public $data = array();

    /**
     * Create a new cookie instance.
     *
     * @param  \Abimo\Config $config
     *
     * @return void
     */
    public function __construct(Config $config)
    {
        $this->config['app'] = $config->app;
        $this->config['cookie'] = $config->cookie;
    }

    /**
     * Write a new cookie.
     *
     * @param  string $name
     * @param  string $value
     * @param  int    $expire
     * @param  string $path
     * @param  string $domain
     * @param  bool   $secure
     * @param  bool   $httponly
     *
     * @return void
     */
    public function write($name, $value, $expire = 0, $path = null, $domain = null, $secure = false, $httponly = false)
    {
        if ($expire >= 0) {
            $expire += time();
        }

        if (null === $path) {
            $path = "/";
        }

        if (null === $domain) {
            $domain = $this->config['app']['url'];
        }

        $this->data[$name] = array('name' => $name, 'value' => $value, 'expire' => $expire, 'path' => $path, 'domain' => $domain, 'secure' => $secure, 'httponly' => $httponly);
    }

    /**
     * Read a cookie by name.
     *
     * @param  string $name
     *
     * @return mixed
     */
    public function read($name)
    {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }
    }

    /**
     * Delete a cookie by name.
     *
     * @param  string $name
     *
     * @return void
     */
    public function delete($name)
    {
        if (isset($this->data[$name])) {
            unset($this->data[$name]);
        }
    }
}
