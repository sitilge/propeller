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

namespace Abimo\Session;

/**
 * Session memcached class.
 *
 * @author Martins Eglitis
 */
class Memcached
{
    /**
     * The memcached config array.
     *
     * @var array
     */
    public $config = array();
    
    /**
     * An array of data to be written to session.
     *
     * @var array
     */
    public $data = array();
    
    /**
     * Memcached handle.
     *
     * @var callable
     */
    public $handle;

    /**
     * Create a new memcached instance.
     *
     * @param  \Abimo\Config $config
     *
     * @return void
     */
    public function __construct(\Abimo\Config $config)
    {
        if (!class_exists('\\Memcached', false)) {
            throw new \BadFunctionCallException("Class Memcached not found", 97);
        }

        $this->config['app'] = $config->app;
        $this->config['memcached'] = $config->memcached;
        $this->config['session'] = $config->session;

        $memcached = $this->config['memcached'];

        //TODO - check for persistent connections
        $this->handle = new \Memcached();

        $this->handle->addServer($memcached['host'], $memcached['port']);
    }

    /**
     * Get value by key.
     *
     * @param  string $key
     *
     * @return void
     */
    public function get($key)
    {
        return $this->data[$key];
    }

    /**
     * Set value with respective key.
     *
     * @param  string $key
     * @param  string $value
     *
     * @return void
     */
    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }
    
    /**
     * Delete value by key.
     *
     * @param  string $key
     *
     * @return void
     */
    public function delete($key)
    {
        if (isset($this->data[$key])) {
            unset($this->data[$key]);
        }
    }

    /**
     * Read session data from session handler.
     *
     * @param  string $key
     *
     * @return void
     */
    public function read($key)
    {
        if ($data = $this->handle->get($this->config['app']['key'].'_'.$key)) {
            return unserialize($data);
        }
    }

    /**
     * Write session data to session handler.
     *
     * @param  string $key
     * @param  string $value
     *
     * @return void
     */
    public function write($key, $value)
    {
        $this->handle->set($this->config['app']['key'].'_'.$key, serialize($value), time() + $this->config['session']['expire']);
    }
}