<?php

namespace App\Controllers;

use App\Models\BusinessModel;
use App\Models\PersistenceModel;
use App\Models\UrlModel;

class TemplateController
{
    private $persistenceModel;

    public function __construct()
    {
//        $this->persistenceModel = new PersistenceModel();
        $this->businessModel = new BusinessModel();
        $this->urlModel = new UrlModel();
    }

    /**
     * @return string
     */
    public function getContent()
    {
        $this->businessModel->manageData();

        if (null === $this->controller->table) {
            return '';
        }

        $content = $this->factory->template()
            ->set('router', $this->urlModel)
            ->set('data', $this->data)
            ->set('table', $this->controller->table)
            ->set('action', $this->controller->action)
            ->set('id', $this->controller->id);

        if (null === $this->controller->action) {
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
        return $this->data;
    }
}