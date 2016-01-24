<?php

namespace App\Controllers;

use Abimo\Factory;
use App\Models\UrlModel;

class AdminController
{
    /**
     * @var string
     */
    public $table;

    /**
     * @var string
     */
    public $action;

    /**
     * @var string
     */
    public $id;

    /**
     * AdminController constructor.
     */
    public function __construct()
    {
        $this->factory = new Factory();

        $this->model = new \App\Models\AdminModel($this);
    }

    /**
     * @param null $table
     * @param null $action
     * @param null $id
     */
    public function main($table = null, $action = null, $id = null)
    {
        $this->table = $table;
        $this->action = $action;
        $this->id = $id;

        $content = $this->model->getContent();
        $menu = $this->model->getMenu();
        $segment = $this->factory->request()->segment(null, 1);

        echo $this->factory->template()
            ->file(__DIR__.'/../Views/Admin/Template')
            ->set('router', new UrlModel())
            ->set('menu', $menu)
            ->set('content', $content)
            ->set('segment', $segment)
            ->render();
    }
}