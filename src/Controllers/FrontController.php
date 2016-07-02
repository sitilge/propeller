<?php

namespace Propeller\Controllers;

use Propeller\Models\PersistenceModel;
use Propeller\Models\TemplateModel;
use Propeller\Models\UrlModel;
use Propeller\Views\MainView;

class FrontController
{
    /**
     * @var PersistenceModel
     */
    public $persistenceModel;

    /**
     * @var TemplateModel
     */
    public $templateModel;

    /**
     * @var UrlModel
     */
    public $urlModel;

    /**
     * @var MainController
     */
    public $mainController;

    /**
     * @var MainView
     */
    public $mainView;

    /**
     * FrontController constructor.
     * @param PersistenceModel $persistenceModel
     * @param TemplateModel $templateModel
     * @param UrlModel $urlModel
     * @param MainController $mainController
     * @param MainView $mainView
     */
    public function __construct(
        PersistenceModel $persistenceModel = null,
        TemplateModel $templateModel = null,
        UrlModel $urlModel = null,
        MainController $mainController = null,
        MainView $mainView = null
    )
    {
        $this->persistenceModel = $persistenceModel;
        $this->templateModel = $templateModel;
        $this->urlModel = $urlModel;
        $this->mainController = $mainController;
        $this->mainView = $mainView;
    }

    /**
     * Initialize the MVC triad and send the output.
     *
     * @param null $table
     * @param null $key
     */
    public function init($table = null, $key = null)
    {
        //build the model layer
        $persistenceModel = $this->persistenceModel;
        $persistenceModel->table = $table;
        $persistenceModel->key = $key;

        //build the controller layer
        $controller = $this->mainController;
        $controller->table = $table;
        $controller->key = $key;
        $controller->persistenceModel = $this->persistenceModel;
        $controller->urlModel = $this->urlModel;
        $controller->manageInput();

        //build the view layer
        $view = $this->mainView;
        $view->table = $table;
        $view->key = $key;
        $view->persistenceModel = $this->persistenceModel;
        $view->templateModel = $this->templateModel;
        $view->urlModel = $this->urlModel;
        $view->manageOutput();
    }
}