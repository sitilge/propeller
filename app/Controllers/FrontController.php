<?php

namespace App\Controllers;

use \Abimo\Factory;
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
     * FrontController constructor.
     */
    public function __construct()
    {
        $this->factory = new Factory();
        $this->urlModel = new UrlModel();

        $this->businessModel = new BusinessModel($this);
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
        $this->businessModel->manageData();

        if (null === $this->table) {
            return $this->factory->template()
                ->file(__DIR__.'/../Views/Admin/Welcome')
                ->render();
        }

        $content = $this->factory->template()
            ->set('url', $this->urlModel)
            ->set('data', $this->businessModel->data)
            ->set('table', $this->table)
            ->set('action', $this->action)
            ->set('id', $this->id);

        if (null === $this->action) {
            $content->file(__DIR__.'/../Views/Admin/Table');
        } else {
            $content->file(__DIR__.'/../Views/Admin/Row');
        }

        return $content->render();
    }

    /**
     * @return array
     */
    public function getMenu()
    {
        return $this->businessModel->data;
    }
}