<?php

namespace App\Models;

class UrlModel
{
    /**
     * @var \Abimo\Factory
     */
    private $factory;

    /**
     * UrlModel constructor.
     */
    public function __construct()
    {
        $this->factory = new \Abimo\Factory();
    }

    /**
     * The admin URL generator.
     *
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

    /**
     * Prepare the pattern.
     *
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