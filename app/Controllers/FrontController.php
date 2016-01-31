<?php

namespace App\Controllers;

use Abimo\Factory;
use App\Models\UrlModel;

class FrontController
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
     * @var TemplateController
     */
    public $templateController;

    /**
     * AdminController constructor.
     */
    public function __construct()
    {
//        $this->templateController = ;

        $this->factory = new \Abimo\Factory();
    }

    /**
     * @param null $table
     * @param null $action
     * @param null $id
     */
    public function main($table = null, $action = null, $id = null)
    {
//        $this->table = $table;
//        $this->action = $action;
//        $this->id = $id;

        $templateController = new \App\Controllers\TemplateController($this->table, $this->action, $this->id);

        $content = $templateController->getContent();
        $menu = $templateController->getMenu();
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