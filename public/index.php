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

//initialize router and dispatch
new \App\Misc\Router();
