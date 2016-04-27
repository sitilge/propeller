<?php

namespace App\Controllers;

use \Abimo\Factory;
use App\Models\PersistenceModel;
use App\Models\UrlModel;
use App\Models\BusinessModel;

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
     * @var array
     */
    public $structure = [];

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
     * FrontController constructor.
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
     * The main method called from router.
     * @param null $table
     * @param null $action
     * @param null $id
     */
    public function main($table = null, $action = null, $id = null)
    {
        //TODO : eh, inheritance?
        $this->table = $this->persistenceModel->table = $this->businessModel->table = $table;
        $this->action = $this->persistenceModel->action = $this->businessModel->action = $action;
        $this->id = $this->persistenceModel->id = $this->businessModel->id = $id;
        $this->structure = $this->persistenceModel->structure = $this->businessModel->structure;

        $this->businessModel->persistenceModel = $this->persistenceModel;

        echo $this->getTemplate();
    }

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
        if (null === $this->table) {
            return $this->factory->template()
                ->file(__DIR__.'/../Views/Schema')
                ->set('url', $this->urlModel)
                ->set('data', $this->businessModel->manageSchema())
                ->render();
        }

        if (null === $this->action) {
            return $this->factory->template()
                ->file(__DIR__.'/../Views/Table')
                ->set('url', $this->urlModel)
                ->set('data', $this->businessModel->manageTable())
                ->set('table', $this->table)
                ->set('action', $this->action)
                ->render();
        }

        return $this->factory->template()
            ->file(__DIR__.'/../Views/Row')
            ->set('url', $this->urlModel)
            ->set('data', $this->businessModel->manageRow())
            ->set('table', $this->table)
            ->set('action', $this->action)
            ->set('id', $this->id)
            ->render();
    }
}