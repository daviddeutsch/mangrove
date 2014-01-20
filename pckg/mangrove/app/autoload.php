<?php

/*
 * Mock Autoloader to supply the base classes that mangrove needs to boot up
 *
 * Will be replaced during self install
 */

$base = realpath( dirname(__FILE__) . '/../../..' );

$includes = array(
	'libraries/valanx/mangrove/lib/core/MangroveApp',
	'libraries/valanx/mangrove/lib/core/MangroveAutoloadGenerator',
	'libraries/valanx/mangrove/lib/core/MangroveClassLoader',
	'libraries/valanx/mangrove/lib/core/MangroveInstaller',
	'libraries/valanx/mangrove/lib/core/MangroveUtils',
	'libraries/redbean/redbean-adaptive/rb'
);

foreach ( $includes as $path ) {
	include_once($base . '/' . $path . '.php');
}
