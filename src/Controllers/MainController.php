<?php

namespace Propeller\Controllers;

use Propeller\Models\PersistenceModel;
use Propeller\Models\UrlModel;

class MainController
{
    /**
     * @var string
     */
    public $table = '';

    /**
     * @var string
     */
    public $key = '';

    /**
     * @var PersistenceModel
     */
    public $persistenceModel;

    /**
     * @var UrlModel
     */
    public $urlModel;

    /**
     * MainController constructor.
     * @param null $table
     * @param null $key
     * @param PersistenceModel|null $persistenceModel
     * @param UrlModel|null $urlModel
     */
    public function __construct(
        $table = null,
        $key = null,
        PersistenceModel $persistenceModel = null,
        UrlModel $urlModel = null
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
        //TODO - fix this mess
        $method = $_SERVER['REQUEST_METHOD'];

        switch ($method) {
            case 'POST' :
                if (!empty($_POST)) {
                    $input = $_POST;
                    $this->persistenceModel->createRow($input);
                    http_response_code(201);
                }

                break;
            case 'PUT' :
                parse_str(file_get_contents("php://input"), $input);
                $this->persistenceModel->updateRow($input);

                break;
            case 'DELETE' :
                $this->persistenceModel->deleteRow();

                break;
        }

        $this->persistenceModel->output = json_encode($this->urlModel->main($this->table));
    }
}