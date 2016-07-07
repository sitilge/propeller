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
     *
     * @param null                  $table
     * @param null                  $key
     * @param PersistenceModel|null $persistenceModel
     * @param UrlModel|null         $urlModel
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
        $method = $_SERVER['REQUEST_METHOD'];

        //TODO - violating DRY
        $input = $this->getInput();

        //create
        if ($method === 'POST') {
            if (!empty($input)) {
                $this->persistenceModel->createRow($input);
            }
        }

        //read
        if ($method === 'GET') {
        }

        //update
        if ($method === 'PUT') {
            if (!empty($input)) {
                $this->persistenceModel->updateRow($input);
            }
        }

        //delete
        if ($method === 'DELETE') {
            $this->persistenceModel->deleteRow();
        }

        $this->persistenceModel->output = $this->urlModel->main($this->table);
    }

    /**
     * Get the input.
     *
     * @return array
     */
    private function getInput()
    {
        $input = [];

        parse_str(file_get_contents('php://input'), $input);

        if (!isset($input)) {
            if (!empty($_POST)) {
                $input = $_POST;
            } else {
                $input = $_GET;
            }
        }

        return $input;
    }
}
