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
    public $factory;

    /**
     * @var UrlModel
     */
    private $urlModel;

    /**
     * @var BusinessModel
     */
    public $businessModel;

    /**
     * ThrowableController constructor.
     */
    public function __construct()
    {

    }

    /**
     * The main method called on error.
     * @return void
     */
    public function main()
    {
        //TODO - move away from STUPID
        $this->factory = new Factory();
        $this->urlModel = new UrlModel();
        $this->businessModel = new BusinessModel();

        echo $this->getTemplate();
    }

    /**
     * @return string
     */
    private function getTemplate()
    {
        return $this->factory->template()
            ->file(__DIR__.'/../Views/Template')
            ->set('url', $this->urlModel)
            ->set('structure', $this->businessModel->structure)
            ->set('segment', $this->factory->request()->segment(null, 1))
            ->set('content', $this->getContent())
            ->render();
    }

    /**
     * Get the content.
     * @return string
     */
    private function getContent()
    {
        return $this->factory->template()
            ->file(__DIR__.'/../Views/Throwable')
            ->render();
    }
}