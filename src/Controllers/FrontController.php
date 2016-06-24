<?php

namespace Propeller\Controllers;

use Propeller\Models\PersistenceModel;
use Propeller\Models\TemplateModel;
use Propeller\Models\UrlModel;
use Propeller\Views\MainView;

class FrontController
{
    public function init($table = null, $key = null)
    {
        //init model layer
        $persistenceModel = new PersistenceModel(
            $table,
            $key
        );
        $persistenceModel->init();

        $templateModel = new TemplateModel();
        $templateModel->init();

        $urlModel = new UrlModel();
        $urlModel->init();

        //init controller layer
        $controller = new MainController(
            $table,
            $key,
            $persistenceModel,
            $urlModel
        );
        $controller->manageInput();

        //init view layer
        $view = new MainView(
            $table,
            $key,
            $persistenceModel,
            $templateModel,
            $urlModel
        );
        $view->manageOutput();
    }
}