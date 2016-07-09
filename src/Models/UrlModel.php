<?php

namespace Propeller\Models;

class UrlModel
{
    /**
     * @var string
     */
    public $base = '';

    /**
     * The main route builder.
     *
     * @param null $table
     * @param null $key
     *
     * @return string
     */
    public function main($table = null, $key = null)
    {
        $pattern = [];

        if (null !== $table) {
            $pattern[] = '%s';
        }

        if (null !== $key) {
            $pattern[] = '%s';
        }

        return $this->base.'/'.$this->prepare(implode('/', $pattern), [
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
     *
     * @param int $index
     *
     * @return string
     */
    public function getSegment($index = 1)
    {
        $url = $this->getUrl();

        $segments = explode('/', $url);
        $count = count($segments);

        if ($count <= $index || $index < 1) {
            return $segments[$count - 1];
        }

        return $segments[$index];
    }

    /**
     * Get the current url.
     *
     * @return string
     */
    public function getUrl()
    {
        if (!empty($_SERVER['PATH_INFO'])) {
            return rawurldecode(parse_url($_SERVER['PATH_INFO'], PHP_URL_PATH));
        } elseif (!empty($_SERVER['REQUEST_URI'])) {
            return rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
        }
    }
}
