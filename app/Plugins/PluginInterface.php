<?php

namespace App\Plugins;

use App\Models\PersistenceModel;

interface PluginInterface
{
    /**
     * PluginInterface constructor.
     * @param PersistenceModel $persistenceModel
     */
    public function __construct(PersistenceModel $persistenceModel);

    /**
     * Manage plugin post data.
     * @param $column
     * @param $data
     * @return mixed
     */
    public function managePost($column, $data);

    /**
     * Manage plugin view.
     * @param $column
     * @return mixed
     */
    public function manageView($column);
}