<?php

namespace Propeller\Views;

use Propeller\Models\PersistenceModel;
use Propeller\Models\TemplateModel;
use Propeller\Models\UrlModel;

class MainView
{
    public function __construct(
        $table,
        $key,
        PersistenceModel $persistenceModel,
        TemplateModel $templateModel,
        UrlModel $urlModel
    ) {
        $this->table = $table;
        $this->key = $key;
        $this->templateModel = $templateModel;
        $this->persistenceModel = $persistenceModel;
        $this->urlModel = $urlModel;
    }

    private function renderContainerTemplate()
    {
        return $this->templateModel
            ->set('url', $this->urlModel)
            ->set('tables', $this->persistenceModel->getTables())
            ->set('segment', $this->urlModel->getSegment())
            ->set('content', $this->getContent())
            ->file(__DIR__.'/../Templates/Container')
            ->render();
    }

    private function getContent()
    {
        if (null === $this->table) {
            return $this->renderSchemaTemplate();
        }

        if (null === $this->key) {
            return $this->renderTableTemplate();
        }

        return $this->renderRowTemplate();}

    private function renderSchemaTemplate()
    {
        return $this->templateModel
            ->set('url', $this->urlModel)
            ->file(__DIR__.'/../Templates/Schema')
            ->render();
    }

    private function renderTableTemplate()
    {
        return $this->templateModel
            ->set('url', $this->urlModel)
            ->set('query',$this->persistenceModel->getQuery())
            ->set('map', $this->persistenceModel->getMap())
            ->set('columns', $this->persistenceModel->getColumns())
            ->set('keys', $this->persistenceModel->getKeys())
            ->set('rows', $this->persistenceModel->readRows())
            ->file(__DIR__.'/../Templates/Table')
            ->render();
    }

    private function renderRowTemplate()
    {
        $template = $this->templateModel
            ->set('url', $this->urlModel)
            ->set('query',$this->persistenceModel->getQuery())
            ->set('map', $this->persistenceModel->getMap())
            ->set('columns', $this->persistenceModel->getColumns())
            ->set('key', $this->key)
            ->set('row', $this->persistenceModel->readRow())
            ->file(__DIR__.'/../Templates/Row');

        $method = $_SERVER['REQUEST_METHOD'];

        if ($method === 'POST') {
            $template = $template
                ->set('key', empty($_POST) ? null : $this->key)
                ->set('row', empty($_POST) ? null : $this->persistenceModel->readRow());
        }


        return $template->render();
    }

    public function manageOutput()
    {
        $method = $_SERVER['REQUEST_METHOD'];

        switch ($method) {
            case 'POST' :
                if (!empty($_POST)) {
                    $this->persistenceModel->createRow($_POST);
                    http_response_code(201);
                }

                break;
            case 'PUT' :
                $this->persistenceModel->updateRow($_POST);

                break;
            case 'DELETE' :
                $this->persistenceModel->deleteRow($_POST);

                break;
        }

        $this->persistenceModel->output = json_encode($this->urlModel->main($this->table));
    }
}