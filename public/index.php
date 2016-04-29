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
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//initialize router and dispatch
new \App\Misc\Router(new \Abimo\Factory());