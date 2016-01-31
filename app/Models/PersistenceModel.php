<?php

namespace App\Models;

use Abimo\Factory;

class PersistenceModel
{
    public function __construct()
    {
        $this->factory = new Factory();
        $this->config = $this->factory->config();
        $this->db = $this->factory->database();

//        $this->model = new \App\Models\MainModel($this);
    }





}