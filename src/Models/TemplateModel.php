<?php

namespace Propeller\Models;

class TemplateModel
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * @var string
     */
    private $file = '';

    /**
     * Set the file.
     * @param string $file
     * @return $this
     */
    public function file($file)
    {
        $this->file = $file;
        if (empty(pathinfo($file, PATHINFO_EXTENSION))) {
            $this->file .= '.php';
        }
        return $this;
    }

    /**
     * Set the parameter.
     * @param mixed $key
     * @param string $value
     * @return $this
     */
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

    /**
     * Get the parameter.
     * @param $key
     * @return mixed
     */
    public function get($key)
    {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    /**
     * Render the template.
     * @param null $input
     * @return string
     */
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