<?php

namespace Propeller\Models;

class TemplateModel
{
    public $data = [];

    public $file = '';

    public function __construct()
    {

    }

    public function init()
    {

    }

    public function file($file)
    {
        $this->file = $file;
        if (empty(pathinfo($file, PATHINFO_EXTENSION))) {
            $this->file .= '.php';
        }
        return $this;
    }

    public function set($key, $value)
    {
        if (is_array($key)) {
            foreach ($key as $name => $value) {
                $this->data[$name] = $value;
            }
        } else {
            $this->data[$key] = $value;
        }
        return $this;
    }

    public function get($key)
    {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    public function render($input = null)
    {
        ob_start();
        extract($this->data, EXTR_SKIP);
        if (null !== $input) {
            echo $input;
        } else {
            require $this->file;
        }

        return ob_get_clean();
    }
}