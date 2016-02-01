<?php

namespace App\Controllers;

use \Abimo\Factory;
use App\Models\UrlModel;
use App\Models\BusinessModel;

class ThrowableController
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
     * @var array
     */
    public $data = [];

    /**
     * @var Factory
     */
    public $factory;

    /**
     * @var UrlModel
     */
    public $urlModel;

    /**
     * ThrowableController constructor.
     */
    public function __construct()
    {
        $this->factory = new Factory();
        $this->urlModel = new UrlModel();

        $this->businessModel = new BusinessModel();
    }

    public function main()
    {
        $content = $this->getContent();
        $menu = $this->getMenu();
        $segment = $this->factory->request()->segment(null, 1);

        echo $this->factory->template()
            ->file(__DIR__.'/../Views/Admin/Template')
            ->set('url', new UrlModel())
            ->set('menu', $menu)
            ->set('content', $content)
            ->set('segment', $segment)
            ->render();
    }

    /**
     * @return string
     */
    public function getContent()
    {
        $this->businessModel->getJson();

        return $this->factory->template()
            ->file(__DIR__.'/../Views/Admin/Throwable')
            ->render();
    }

    /**
     * @return array
     */
    public function getMenu()
    {
        return $this->businessModel->data;
    }
}