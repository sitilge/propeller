<?php

namespace App\Models;

class UrlModel
{
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

        $factory = new \Abimo\Factory();
        $base_uri_path = $factory->config()->get('app','base_uri_path');
        
        return $this->prepare( $base_uri_path .'/'.implode('/', $pattern), [
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
