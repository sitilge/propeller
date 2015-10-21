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
 * Config class.
 *
 * @author Martins Eglitis
 */
class Config
{
    /**
     * The cookie config array.
     *
     * @var array
     */
    public $data = array();

    /**
     * An instance of loader class.
     *
     * @var \Abimo\Loader
     */
    public $loader;

    /**
     * Create a new config instance.
     *
     * @return void
     */
    public function __construct(Loader $loader)
    {
        $this->loader = $loader;
    }

    /**
     * Get the config value by key.
     *
     * @param  string  $key
     *
     * @return mixed
     */
    public function get($key)
    {
        $keys = explode('.', $key);
        $file = ucfirst(array_shift($keys));

        if (is_readable($path = APP_PATH.DIRECTORY_SEPARATOR.'config')) {
            return $this->data[$file] = $this->loader->requireFile($path.DIRECTORY_SEPARATOR.$file.'.php');
        }

        if (!empty($keys)) {
            return $this->data[$file][array_shift($keys)];
        }
    }

    /**
     * Set the config key and value.
     *
     * @param  string  $key
     * @param  mixed   $value
     *
     * @return void
     */
    public function set($key, $value)
    {
        $keys = explode('.', $key);
        $file = array_shift($keys);

        if (!empty($keys)) {
            $this->data[$file][array_shift($keys)] = $value;
        }

        $this->data[$file] = $value;
    }

    /**
     * Magically call get method.
     *
     * @param  string  $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * Magically call set method.
     *
     * @param  string  $key
     * @param  mixed   $value
     *
     * @return void
     */
    public function __set($key, $value)
    {
        $this->set($key, $value);
    }
}
