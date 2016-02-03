<?php

namespace App\Controllers;

use \Abimo\Factory;
use App\Models\UrlModel;
use App\Models\BusinessModel;

class ThrowableController
{
    /**
     * @var Factory
     */
    private $factory;

    /**
     * @var UrlModel
     */
    private $urlModel;

    /**
     * ThrowableController constructor.
     */
    public function __construct()
    {
        $this->factory = new Factory();
        $this->urlModel = new UrlModel();

        $this->businessModel = new BusinessModel();
    }

    /**
     * The main method called on error.
     *
     * @return void
     */
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
     * Get the content.
     *
     * @return string
     */
    private function getContent()
    {
        $this->businessModel->getJson();

        return $this->factory->template()
            ->file(__DIR__.'/../Views/Admin/Throwable')
            ->render();
    }

    /**
     * Get the data for menu.
     *
     * @return array
     */
    private function getMenu()
    {
        return $this->businessModel->data;
    }
}