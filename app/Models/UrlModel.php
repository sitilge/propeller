<?php

namespace App\Models;

use Abimo\Factory;

class UrlModel
{
    /**
     * @var \Abimo\Factory
     */
    private $factory;

    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * The admin URL generator.
     * @param null $table
     * @param null $action
     * @param null $id
     * @return string
     */
    public function admin($table = null, $action = null, $id = null)
    {
        $pattern = [];

        if (null !== $table) {
            $pattern[] = '%s';
            if (null !== $action) {
                $pattern[] = '%s';
                if (null !== $id) {
                    $pattern[] = '%d';
                }
            }
        }

        $baseUrl = $this->factory->config()->get('app','baseUrl');

        return $this->prepare($baseUrl.'/'.implode('/', $pattern), [
            $table,
            $action,
            $id
        ]);
    }

//SHOW COLUMNS FROM `$table`

    /**
     * Prepare the pattern.
     * @param $pattern
     * @param $arguments
     * @return string
     */
    private function prepare($pattern, $arguments)
    {
        foreach ($arguments as &$argument) {
            $argument = preg_replace('#[^a-zA-Z0-9_\-]#', '', str_replace(' ', '_', $argument));
        }

        return vsprintf($pattern, $arguments);
    }
}