<?php

return [
    'development' => false, //development mode; array(true, false)
    'display' => false, //throwable error display; array(true, false)
    'reporting' => E_ALL, //throwable reporting; array(E_ALL, ...)
    'log' => true, //throwable error logging; array(true, false)
    'path' => __DIR__.'/../Logs/Error.log', //throwable error log path; string
    'callable' => function(){
        $throwableController = new \App\Controllers\ThrowableController();
        $throwableController->main();
    } //throwable callable; callable
];