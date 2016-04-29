<?php

namespace App\Controllers;

use Abimo\Factory;
use App\Models\UrlModel;
use App\Models\BusinessModel;
use App\Models\PersistenceModel;

class ThrowableController
{
    /**
     * @var Factory
     */
    public $factory;

    /**
     * @var BusinessModel
     */
    public $businessModel;

    /**
     * @var PersistenceModel
     */
    public $persistenceModel;

    /**
     * @var UrlModel
     */
    public $urlModel;

    /**
     * ThrowableController constructor.
     * @param Factory $factory
     * @param BusinessModel $businessModel
     * @param PersistenceModel $persistenceModel
     * @param UrlModel $urlModel
     */
    public function __construct(Factory $factory, BusinessModel $businessModel, PersistenceModel $persistenceModel, UrlModel $urlModel)
    {
        $this->factory = $factory;
        $this->businessModel = $businessModel;
        $this->persistenceModel = $persistenceModel;
        $this->urlModel = $urlModel;
    }

    /**
     * The main method called on error.
     * @return void
     */
    public function main()
    {
        echo $this->getTemplate();
    }

    /**
     * Get the template.
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