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
 * Template class.
 *
 * @author Martins Eglitis
 */
class Template
{
    /**
     * The data array to be used when capturing.
     *
     * @var array
     */
    public $data = array();

    /**
     * The file to be used when capturing.
     *
     * @var array
     */
    public $file;

    /**
     * An instance of loader class.
     *
     * @var \Abimo\Loader
     */
    public $loader;

    /**
     * Create a new config instance.
     *
     * @param  \Abimo\Loader $loader
     *
     * @return void
     */
    public function __construct(\Abimo\Loader $loader)
    {
        $this->loader = $loader;
    }

    /**
     * Capture the file with given data.
     *
     * @param  string $file
     * @param  array  $data
     *
     * @return string
     */
    public function capture($file, array $data = array())
    {
        ob_start();

        $this->loader->requireFile($file, $data);

        return ob_get_clean();
    }

    /**
     * Set the file.
     *
     * @param  string  $file
     *
     * @return \Abimo\Template
     */
    public function file($file)
    {
        //TODO - check here for template locations
        if (false === strpos($file, SYS_PATH)) {
            $this->file = APP_PATH.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.$file.'.php';
        } else {
            $this->file = $file;
        }

        return $this;
    }

    /**
     * Set a new data.
     *
     * @param  string  $key
     * @param  string  $value
     *
     * @return void
     */
    public function set($key, $value)
    {
        if (is_array($key)) {
            foreach ($key as $name => $value) {
                $this->data[$name] = $value;
            }
        } else {
            $this->data[$key] = $value;
        }

        return $this;
    }

    /**
     * Render the file.
     *
     * @param  string  $file
     *
     * @return string
     */
    public function render($file = false)
    {
        if ($file) {
            $this->file($file);
        }

        return $this->capture($this->file, $this->data);
    }

    /**
     * Magically set a new data.
     *
     * @param  string  $key
     * @param  string  $value
     *
     * @return void
     */
    public function __set($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * Magically get the data.
     *
     * @param  string  $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        return isset($this->data[$key]) ? $this->data[$key] : false;
    }
}
