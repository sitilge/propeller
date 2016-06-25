<?php

namespace Propeller\Controllers;

use Propeller\Models\PersistenceModel;
use Propeller\Models\TemplateModel;
use Propeller\Models\UrlModel;
use Propeller\Views\MainView;

class FrontController
{
    /**
     * Initialize the MVC triad and send the output.
     *
     * @param null $table
     * @param null $key
     */
    public function init($table = null, $key = null)
    {
        //build the model layer
        $persistenceModel = new PersistenceModel(
            $table,
            $key
        );

        $templateModel = new TemplateModel();

        $urlModel = new UrlModel();

        //build the controller layer
        $controller = new MainController(
            $table,
            $key,
            $persistenceModel,
            $urlModel
        );
        $controller->manageInput();

        //build the view layer
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