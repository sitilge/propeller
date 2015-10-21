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
 * Injection container class.
 *
 * @author Martins Eglitis
 */
class Container implements \ArrayAccess
{
    protected $values = array();

    public function __construct(array $values = array())
    {
        $this->values = $values;
    }

    public function offsetSet($id, $value)
    {
        $this->values[$id] = $value;
    }

    public function offsetGet($id)
    {
        if (!array_key_exists($id, $this->values)) {
            throw new \InvalidArgumentException(sprintf('Identifier "%s" is not defined.', $id));
        }

        $isFactory = is_object($this->values[$id]) && method_exists($this->values[$id], '__invoke');

        return $isFactory ? $this->values[$id]($this) : $this->values[$id];
    }

    public function offsetExists($id)
    {
        return array_key_exists($id, $this->values);
    }

    public function offsetUnset($id)
    {
        unset($this->values[$id]);
    }

    public static function share(callable $callable)
    {
        return function ($c) use ($callable) {
            static $object;
            if (null === $object) {
                $object = $callable($c);
            }
            return $object;
        };
    }

    public static function protect(callable $callable)
    {
        return function ($c) use ($callable) {
            return $callable;
        };
    }

    public function raw($id)
    {
        if (!array_key_exists($id, $this->values)) {
            throw new InvalidArgumentException(sprintf('Identifier "%s" is not defined.', $id));
        }
        return $this->values[$id];
    }

    public function extend($id, callable $callable)
    {
        if (!array_key_exists($id, $this->values)) {
            throw new InvalidArgumentException(sprintf('Identifier "%s" is not defined.', $id));
        }
        if (!is_object($this->values[$id]) || !method_exists($this->values[$id], '__invoke')) {
            throw new InvalidArgumentException(sprintf('Identifier "%s" does not contain an object definition.', $id));
        }

        $factory = $this->values[$id];
        return $this->values[$id] = function ($c) use ($callable, $factory) {
            return $callable($factory($c), $c);
        };
    }

    public function keys()
    {
        return array_keys($this->values);
    }
}