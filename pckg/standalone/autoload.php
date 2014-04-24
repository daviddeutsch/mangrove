<?php

// Get stupid.

function sw_autoloader($class) {
	$class = str_replace(array('\\', '_'), '/', $class);

	require_once __DIR__ . '/lib/' . $class . '.php';
}

spl_autoload_register('sw_autoloader');

