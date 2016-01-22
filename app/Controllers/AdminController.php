<?php

namespace App\Controllers;

use Abimo\Factory;
use App\Models\UrlModel;

class AdminController
{
    public $table;

    public $action;

    public $id;

    public function __construct()
    {
        $this->factory = new Factory();

        $this->model = new \App\Models\AdminModel($this);
    }

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