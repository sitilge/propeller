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
 * Database pdo class.
 *
 * @author Martins Eglitis
 */
class Pdo
{
     /**
     * The pdo config array.
     *
     * @var array
     */
    public $config = array();
    
    /**
     * Pdo handle.
     *
     * @var callable
     */
    public $handle;
    
    /**
     * Create a new pdo instance.
     *
     * @param  \Abimo\Config $config
     *
     * @return void
     */
    public function __construct(\Abimo\Config $config)
    {
        if (!class_exists('\\PDO', false)) {
            throw new \BadFunctionCallException("Class PDO not found", 97);
        }

        $this->config['database'] = $config->database;
        $this->config['pdo'] = $config->pdo;

        $handle = new \PDO($this->config['pdo']['driver'].':host='.$this->config['database']['host'].';dbname='.$this->config['database']['schema'].';charset=utf8', $this->config['database']['user'], $this->config['database']['password']);

		$handle->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		$handle->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
		
        $this->handle = $handle;
    }
	
    /**
     * Prepare the identifier.
     *
     * @param  string $identifier
     *
     * @return string
     */
	public function prepareIdentifier($identifier)
	{
		return "`$identifier`";
	}

    /**
     * Prepare fields for 'set' query.
     *
     * @param  mixed $fields
     *
     * @return string
     */
	public function prepareSet($fields)
	{
		if (is_array($fields)) {
			$set = '';
            
			foreach ($fields as $field) {
				$set.="`$field`=:$field, ";
			}

			return substr($set, 0, -2);
		}
        
        return "`$fields`=:$fields";
	}

    /**
     * Magically forward call to pdo handler.
     *
     * @param  string $name
     * @param  array  $args
     *
     * @return string
     */
    public function __call($name, $args)
    {
		return call_user_func_array(array($this->handle, $name), $args);
    }
}