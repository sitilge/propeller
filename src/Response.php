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
 * Response class.
 *
 * @author Martins Eglitis
 */
class Response
{
    /**
     * An instance of cookie class.
     *
     * @var \Abimo\Cookie
     */
    public $cookie;

    /**
     * The headers array.
     *
     * @var array
     */
    public $headers = array();

    /**
     * The response messages array.
     *
     * @var array
     */
    public $messages = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Moved Temporarily',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Large',
        415 => 'Unsupported Media Type',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported'
    );

    /**
     * Create a new response instance.
     *
     * @param  \Abimo\Cookie $cookie
     *
     * @return void
     */
    public function __construct(Cookie $cookie)
    {
        $this->cookie = $cookie;
    }

    /**
     * Add new response headers.
     *
     * @param  array $headers
     *
     * @return void
     */
    public function headers(array $headers = array())
    {
        foreach ($headers as $name => $value) {
            $this->headers[strtolower($name)] = $value;
        }
    }

    /**
     * Send the response by setting headers, cookies and echoing output.
     *
     * @param  string $output
     * @param  int    $code
     *
     * @return void
     */
    public function send($output, $code = 200)
    {
        header('HTTP/1.1 '.$code.' '.$this->messages[$code], true, $code);

        foreach ($this->headers as $name => $value) {
            header($name.' : '.$value, true, $code);
        }

        foreach ($this->cookie->data as $cookie) {
            setcookie($cookie['name'], $cookie['value'], $cookie['expire'], $cookie['path'], $cookie['domain'], $cookie['secure'], $cookie['httponly']);
        }

        echo $output;
    }
}
