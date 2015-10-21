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
 * Session redis class.
 *
 * @author Martins Eglitis
 */
class Redis
{
    /**
     * The redis config array.
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
     * Redis handle.
     *
     * @var callable
     */
    public $handle;

    /**
     * Create a new redis instance.
     *
     * @param  \Abimo\Config $config
     *
     * @return void
     */
    public function __construct()
    {
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
    }
}