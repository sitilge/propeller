<?php

use Abimo\Factory;
use App\Models\UrlModel;
use App\Models\BusinessModel;
use App\Models\PersistenceModel;
use App\Controllers\ThrowableController;

return [
    'development' => false, //development mode; array(true, false)
    'display' => false, //throwable error display; array(true, false)
    'reporting' => E_ALL, //throwable reporting; array(E_ALL, ...)
    'log' => true, //throwable error logging; array(true, false)
    'path' => __DIR__.'/../Logs/Error.log', //throwable error log path; string
    'callable' => function(){
        $throwableController = new ThrowableController(
            new Factory(),
            new BusinessModel(
                new Factory(),
                new UrlModel(new Factory())),
            new PersistenceModel(new Factory()),
            new UrlModel(new Factory())
        );
        $throwableController->main();
    } //throwable callable; callable
];