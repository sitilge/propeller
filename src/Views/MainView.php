<?php

namespace Propeller\Views;

use Propeller\Models\PersistenceModel;
use Propeller\Models\TemplateModel;
use Propeller\Models\UrlModel;

class MainView
{
    /**
     * @var string
     */
    public $containerTemplate = __DIR__.'/../Templates/Container';

    /**
     * @var string
     */
    public $schemaTemplate = __DIR__.'/../Templates/Schema';

    /**
     * @var string
     */
    public $tableTemplate = __DIR__.'/../Templates/Table';

    /**
     * @var string
     */
    public $rowTemplate = __DIR__.'/../Templates/Row';

    /**
     * MainView constructor.
     *
     * @param null                  $table
     * @param null                  $key
     * @param PersistenceModel|null $persistenceModel
     * @param TemplateModel|null    $templateModel
     * @param UrlModel|null         $urlModel
     */
    public function __construct(
        $table = null,
        $key = null,
        PersistenceModel $persistenceModel = null,
        TemplateModel $templateModel = null,
        UrlModel $urlModel = null
    ) {
        $this->table = $table;
        $this->key = $key;
        $this->templateModel = $templateModel;
        $this->persistenceModel = $persistenceModel;
        $this->urlModel = $urlModel;
    }

    /**
     * Render container template.
     *
     * @return string
     */
    public function renderContainerTemplate()
    {
        return $this->templateModel
            ->set('url', $this->urlModel)
            ->set('tables', $this->persistenceModel->getTables())
            ->set('content', $this->getContent())
            ->file($this->containerTemplate)
            ->render();
    }

    /**
     * Get the content.
     *
     * @return string
     */
    private function getContent()
    {
        if (null === $this->table) {
            return $this->renderSchemaTemplate();
        }

        if (null === $this->key) {
            return $this->renderTableTemplate();
        }

        return $this->renderRowTemplate();
    }

    /**
     * Render schema template.
     *
     * @return string
     */
    public function renderSchemaTemplate()
    {
        return $this->templateModel
            ->set('url', $this->urlModel)
            ->file($this->schemaTemplate)
            ->render();
    }

    /**
     * Render table template.
     *
     * @return string
     */
    public function renderTableTemplate()
    {
        return $this->templateModel
            ->set('url', $this->urlModel)
            ->set('query', $this->persistenceModel->getQuery())
            ->set('model', $this->persistenceModel->getModel())
            ->set('rows', $this->persistenceModel->readRows())
            ->file($this->tableTemplate)
            ->render();
    }

    /**
     * Render row template.
     *
     * @return string
     */
    public function renderRowTemplate()
    {
        $template = $this->templateModel
            ->set('url', $this->urlModel)
            ->set('query', $this->persistenceModel->getQuery())
            ->set('model', $this->persistenceModel->getModel())
            ->set('key', $this->key)
            ->set('row', $this->persistenceModel->readRow())
            ->file($this->rowTemplate);

        $method = $_SERVER['REQUEST_METHOD'];

        if ($method === 'POST') {
            $template = $template
                ->set('key', empty($_POST) ? null : $this->key)
                ->set('row', empty($_POST) ? null : $this->persistenceModel->readRow());
        }

        return $template->render();
    }

    /**
     * Manage the output.
     */
    public function manageOutput()
    {
        $method = $_SERVER['REQUEST_METHOD'];

        //TODO - violating DRY
        $input = $this->getInput();

        //create
        if ($method === 'POST') {
            if (!empty($input)) {
                http_response_code(201);
                echo $this->getOutput(json_encode($this->persistenceModel->output));

                return;
            }

            echo $this->getOutput($this->renderRowTemplate());
        }

        //read
        if ($method === 'GET') {
            echo $this->getOutput($this->renderContainerTemplate());
        }

        //update
        if ($method === 'PUT') {
            echo $this->getOutput(json_encode($this->persistenceModel->output));
        }

        //delete
        if ($method === 'DELETE') {
            echo $this->getOutput(json_encode($this->persistenceModel->output));
        }
    }

    /**
     * Get the output.
     *
     * @param string $output
     *
     * @return string
     */
    private function getOutput($output = '')
    {
        return PHP_SAPI === 'cli' ? '' : $output;
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
