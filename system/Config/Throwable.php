<?php

return array(
	'display' => true, //throwable error display; array(true, false)
	'reporting' => E_ALL, //throwable reporting; array(E_ALL, ...)
	'log' => true, //throwable error logging; array(true, false)
	'path' => APP_PATH.DIRECTORY_SEPARATOR.'logs'.DIRECTORY_SEPARATOR.'Error.log', //throwable error log path; string
	'controller' => 'ThrowableController', //throwable error controller; string
	'action' => 'throwable' //throwable error action; string
);