<?php

/*
 * Mock Autoloader to supply the base classes that mangrove needs to boot up
 *
 * Will be replaced during self install
 */

$base = realpath( dirname(__FILE__) . '/../..' );

$includes = array(
	'libraries/valanx/core/MangroveApp',
	'libraries/valanx/core/MangroveAutoloadGenerator',
	'libraries/valanx/core/MangroveClassLoader',
	'libraries/valanx/core/MangroveInstaller',
	'libraries/valanx/core/MangroveUtils',
	'libraries/redbean/jredbean/rb',
	'libraries/valanx/jredbean/src/jRedBean/jMysqlQueryWriter',
	'libraries/valanx/jredbean/src/jRedBean/jPostgreSqlQueryWriter',
	'libraries/valanx/jredbean/src/jRedBean/jSQLiteTQueryWriter',
	'libraries/valanx/jredbean/src/jRedBean/jR',

);

foreach ( $includes as $path ) {
	include_once($base.$path.'.php');
}
