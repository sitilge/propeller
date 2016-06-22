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

        $templateModel = new TemplateModel();

        $urlModel = new UrlModel();

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
        $view->output();
    }
}