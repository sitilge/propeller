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
    protected $table;

    /**
     * @var string
     */
    protected $action;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var Factory
     */
    protected $factory;

    /**
     * @var BusinessModel
     */
    protected $businessModel;

    /**
     * @var UrlModel
     */
    protected $urlModel;

    /**
     * FrontController constructor.
     */
    public function __construct()
    {
        $this->factory = new Factory();
        $this->urlModel = new UrlModel();
        $this->businessModel = new BusinessModel();
    }

    /**
     * The main method called from router.
     *
     * @param null $table
     * @param null $action
     * @param null $id
     */
    public function main($table = null, $action = null, $id = null)
    {
        $this->table = $table;
        $this->action = $action;
        $this->id = $id;

        $this->businessModel = new $this->businessModel($this->table, $this->action, $this->id);

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
     * Get the data for menu.
     *
     * @return array
     */
    protected function getMenu()
    {
        return $this->businessModel->data;
    }
}