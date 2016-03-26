<?php

return [
    'frontController' => 'App\Controllers\FrontController', //front controller class; string
    'base_uri_path' => str_replace( '/'. basename( $_SERVER['SCRIPT_NAME'] ), '', $_SERVER['SCRIPT_NAME'] ),
];
