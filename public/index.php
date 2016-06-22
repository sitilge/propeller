<?php

//TODO - delete this

function dd($i)
{
    ob_clean();
    echo "<pre>";
    var_dump($i);
    echo "<pre>";
    exit;
}

require __DIR__.'/../vendor/autoload.php';

$frontController = new \Propeller\Misc\Bootstrap();

$frontController->init();