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
 * Bakery class.
 *
 * @author Martins Eglitis
 */
class Bakery
{
    /**
     * The cookie for the bakery.
     *
     * @var \Abimo\Cookie
     */
    public $cookie;

    /**
     * Data to be saved or loaded.
     *
     * @var array
     */
    public $data = array();

    /**
     * The ID as recieved from cookie.
     *
     * @var string
     */
    public $id;

    /**
     * The session for the bakery.
     *
     * @var \Abimo\Session
     */
    public $session;

    /**
     * Create a new bakery instance.
     *
     * @param  \Abimo\Cookie $cookie
     * @param  string        $session
     *
     * @return void
     */
    public function __construct(Cookie $cookie, $session)
    {
        $this->cookie = $cookie;
        $this->session = $session;
    }

    /**
     * Loads session data based on cookie id.
     *
     * @return mixed
     */
    public function load()
    {
        $config = $this->cookie->config['cookie'];

        $this->id = $this->cookie->read($config['name']);

        if ($this->data = $this->session->read($this->id)) {
            return $this->data;
        }

        $this->id = chr(mt_rand(ord('a'), ord('z'))).substr(md5(time()), 1);
    }

    /**
     * Saves session data and cookie.
     *
     * @return void
     */
    public function save()
    {
        $config = $this->cookie->config['cookie'];

        $this->cookie->write($config['name'], $this->id, $config['expire']);

        $this->session->write($this->id, $this->data);
    }
}
