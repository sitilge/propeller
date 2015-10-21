<?php

define('APP_PATH', dirname(__DIR__).DIRECTORY_SEPARATOR.'system');
define('SYS_PATH', dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR.'abimo'.DIRECTORY_SEPARATOR.'src');

require SYS_PATH.DIRECTORY_SEPARATOR.'Bootstrap.php';

new \Abimo\Bootstrap();
