<?php

namespace Propeller\Controllers;

use Propeller\Models\PersistenceModel;
use Propeller\Models\UrlModel;

class MainController
{
    /**
     * @var string
     */
    private $table = '';

    /**
     * @var string
     */
    private $key = '';

    /**
     * @var PersistenceModel
     */
    private $persistenceModel;

    /**
     * @var UrlModel
     */
    private $urlModel;

    /**
     * MainController constructor.
     * @param $table
     * @param $key
     * @param PersistenceModel $persistenceModel
     * @param UrlModel $urlModel
     */
    public function __construct(
        $table,
        $key,
        PersistenceModel $persistenceModel,
        UrlModel $urlModel
    ) {
        $this->table = $table;
        $this->key = $key;
        $this->persistenceModel = $persistenceModel;
        $this->urlModel = $urlModel;
    }

    /**
     * Manage the input.
     */
    public function manageInput()
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