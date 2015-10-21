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
 * Loader class.
 *
 * @author Martins Eglitis
 */
class Loader
{
    /**
     * The loader namespace prefix array.
     *
     * @var array
     */
    public $prefixes = array();

    /**
     * Register autoloader.
     *
     * @return void
     */
    public function register()
    {
        spl_autoload_register(array($this, 'loadClass'));
    }

    /**
     * Add namespace.
     *
     * @param  string $prefix
     * @param  string $path
     * @param  bool   $prepend
     *
     * @return void
     */
    public function addNamespace($prefix, $path, $prepend = false)
    {
        $prefix = trim($prefix, '\\') . '\\';

        $path = rtrim($path, DIRECTORY_SEPARATOR) . '/';

        if (empty($this->prefixes[$prefix])) {
            $this->prefixes[$prefix] = array();
        }

        if ($prepend) {
            array_unshift($this->prefixes[$prefix], $path);
        }

        array_push($this->prefixes[$prefix], $path);
    }

    /**
     * Load the provided class.
     *
     * @param  string $class
     *
     * @return void
     */
    public function loadClass($class)
    {
        $relativePrefix = $class;

        while (false !== $pos = strrpos($relativePrefix, '\\')) {

            $relativePrefix = substr($class, 0, $pos + 1);

            $relativeClass = substr($class, $pos + 1);

            if ($this->loadMappedFile($relativePrefix, $relativeClass)) {
                break;
            }

            $relativePrefix = rtrim($relativePrefix, '\\');
        }
    }

    /**
     * Search for a file that matches the class in prefixes array.
     *
     * @param  string $relativePrefix
     * @param  string $relativeClass
     *
     * @return bool
     */
    private function loadMappedFile($relativePrefix, $relativeClass)
    {
        if (empty($this->prefixes[$relativePrefix])) {
            return false;
        }

        foreach ($this->prefixes[$relativePrefix] as $path) {
            $file = $path.str_replace('\\', '/', $relativeClass).'.php';

            if ($this->requireFile($file)) {
                return $file;
            }
        }

        return false;
    }

    /**
     * Require the file and extracts args if provided.
     *
     * @param  string $file
     * @param  array  $args
     *
     * @return mixed
     */
    public function requireFile($file, array $args = array())
    {
        if (file_exists($file)) {
            if (!empty($args)) {
                extract($args, EXTR_SKIP);
            }

            return require $file;
        }
    }
}
