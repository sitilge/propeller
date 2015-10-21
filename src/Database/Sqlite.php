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

namespace Abimo\Database;

/**
 * Database sqlite class.
 *
 * @author Martins Eglitis
 */
class Sqlite
{
    /**
     * The sqlite config array.
     *
     * @var array
     */
    public $config = array();
    
    /**
     * Sqlite handle.
     *
     * @var callable
     */
    public $handle;

    /**
     * Create a new sqlite instance.
     *
     * @param  \Abimo\Config $config
     *
     * @return void
     */
    public function __construct(\Abimo\Config $config)
    {
        if (!class_exists('\\Sqlite3', false)) {
            throw new \BadFunctionCallException("Class Sqlite3 not found", 97);
        }

        $this->config['database'] = $config->database;

        $handle = new \SQLite3($this->config['database']['path'].$this->config['database']['schema'].'.db');

        if (0 === $handle->lastErrorCode()) {
            $this->handle = $handle;
        }
    }

    /**
     * Execute the query.
     *
     * @param  string $string
     *
     * @return mixed
     */
    public function query($string)
    {
        return $this->handle->query($string);
    }

    /**
     * Fetch the result.
     *
     * @param  string $result
     * @param  int    $flag
     *
     * @return mixed
     */
    public function fetch($result, $flag = SQLITE3_ASSOC)
    {
        return $result->fetchArray($flag);
    }

    /**
     * Escape the string.
     *
     * @param  string $string
     * @param  int    $flag
     *
     * @return mixed
     */
    public function escape($string, $flag = null)
    {
        return $this->handle->escapeString($flag ? htmlspecialchars($string, $flag, 'UTF-8') : $string);
    }

    /**
     * Get the last insert id.
     *
     * @return mixed
     */
    public function insertId()
    {
        return $this->handle->lastInsertRowid();
    }
}