<?php

function __autoload($class)
{
	require($class.'.php');
}

set_include_path(implode(PATH_SEPARATOR, array(
	'.',
	'./controllers',
	get_include_path()
)));

$frontController = new FrontController();
$frontController->run();