<?php

namespace Propeller\Models;

class UrlModel
{
    /**
     * @var string
     */
    private $base = '';

    /**
     * The main route builder.
     * @param null $table
     * @param null $key
     * @return string
     */
    public function main($table = null, $key = null)
    {
        $pattern = [];

        if (null !== $table) {
            $pattern[] = '%s';
        }

        if (null !== $key) {
            $pattern[] = '%d';
        }

        return $this->prepare($this->base.'/'.implode('/', $pattern), [
            $table,
            $key,
        ]);
    }

    /**
     * Prepare the pattern.
     *
     * @param $pattern
     * @param $arguments
     *
     * @return string
     */
    private function prepare($pattern, $arguments)
    {
        foreach ($arguments as &$argument) {
            $argument = preg_replace('#[^a-zA-Z0-9_\-]#', '', str_replace(' ', '_', $argument));
        }

        return vsprintf($pattern, $arguments);
    }

    /**
     * Get the segment.
     * @param null $uri
     * @param int $index
     * @return mixed
     */
    public function getSegment($uri = null, $index = 1)
    {
        if (null === $uri) {
            $uri = $this->getCurrentUrl();
        }
        if ($uri) {
            $segments = explode('/', $uri);
            $count = count($segments);
            if ($count <= $index || $index < 1) {
                return $segments[$count - 1];
            }
            return $segments[$index];
        }
        return null;
    }

    /**
     * Get the current url.
     * @return mixed
     */
    public function getCurrentUrl()
    {
        if (!empty($_SERVER['PATH_INFO'])) {
            return rawurldecode(parse_url($_SERVER['PATH_INFO'], PHP_URL_PATH));
        } elseif (!empty($_SERVER['REQUEST_URI'])) {
            return rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
        }
        return false;
    }
}
