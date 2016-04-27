<?php

namespace App\Plugins;

use Abimo\Factory;
use App\Models\UrlModel;
use App\Models\PersistenceModel;

class TextPlugin implements PluginInterface
{
    /**
     * @var PersistenceModel
     */
    protected $persistenceModel;

    /**
     * @var Factory
     */
    protected $factory;

    /**
     * @var UrlModel
     */
    protected $urlModel;

    /**
     * @var array
     */
    protected $structure;

    /**
     * @var string
     */
    protected $table;

    /**
     * @var string
     */
    protected $action;

    /**
     * @var string
     */
    protected $id;

    /**
     * TextPlugin constructor.
     * @param PersistenceModel $persistenceModel
     */
    public function __construct(PersistenceModel $persistenceModel)
    {
        $this->persistenceModel = $persistenceModel;
        $this->structure = $this->persistenceModel->structure;
        $this->table = $this->persistenceModel->table;
        $this->action = $this->persistenceModel->action;
        $this->id = $this->persistenceModel->id;

        $this->factory = new Factory();
        $this->urlModel = new UrlModel(new Factory());
    }

    /**
     * Manage plugin post data.
     * @param $column
     * @param $data
     * @return void
     */
    public function managePost($column, $data)
    {
        $this->persistenceModel->updateRow($this->table, $this->id, $column, $data);
    }

    /**
     * Manage plugin view.
     * @param $column
     * @return string
     */
    public function manageView($column)
    {
        return $this->factory->template()
            ->file(__DIR__.'/../Views/Plugins/Text')
            ->set('url', $this->urlModel)
            ->set('structure', $this->structure)
            ->set('table', $this->table)
            ->set('action', $this->action)
            ->set('id', $this->id)
            ->set('column', $column)
            ->set('data', $this->structure[$this->table]['rows'][$this->id][$column])
            ->set('attributes', $this->structure[$this->table]['rows'][$this->id][$column])
            ->render()
            ;
    }
}