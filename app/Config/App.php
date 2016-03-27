<?php

return [
    'frontController' => App\Controllers\FrontController::class, //front controller class; string
    'baseUrl' => str_replace('/'.basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']) //base url; string
];