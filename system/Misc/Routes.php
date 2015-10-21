<?php

use Abimo\Router;

Router::all('/:slug?/:slug?/:number?', 'admin', 'AdminController.main');

//$routes['reservation'] = array(
//	'reservation',
//	'presenter' => 'pages',
//	'method' => 'reservation'
//);
//
//$routes['shops'] = array(
//	'shops/<name_clean>',
//	'presenter' => 'pages',
//	'method' => 'shops',
//	'name_clean' => ''
//);
//
//$routes['repairs'] = array(
//	'reparationer(/<name_clean>)',
//	'presenter' => 'pages',
//	'method' => 'repairs',
//	'name_clean' => ''
//);
//
//$routes['pages'] = array(
//	'<name_clean>',
//	'presenter' => 'pages',
//	'method' => 'page',
//	'name_clean' => ''
//);
//