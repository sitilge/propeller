<?php

require __DIR__.'/../vendor/autoload.php';

$factory = new \Abimo\Factory();

//factory config and set the path for config files
$config = $factory
    ->config()
    ->path(__DIR__.'/../app/Config');

//factory throwable and register handlers
$factory
    ->throwable($config)
    ->configure()
    ->register();

define('BASE_PATH', str_replace( '/'. basename( __FILE__ ), '', $_SERVER['SCRIPT_NAME'] ) );


//initialize router and dispatch
new \App\Misc\Router();
