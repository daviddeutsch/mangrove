<?php

/*
 * Mock Autoloader to supply the base classes that mangrove needs to boot up
 *
 * Will be replaced during self install
 */

$base = realpath( dirname(__FILE__) . '/../../..' );

$includes = array(
	'libraries/redbean/redbean-adaptive/rb',
	'libraries/valanx/mangrove/lib/core/MangroveApp',
	'libraries/valanx/mangrove/lib/core/MangroveAutoloadGenerator',
	'libraries/valanx/mangrove/lib/core/MangroveClassLoader',
	'libraries/valanx/mangrove/lib/core/MangroveInstaller',
	'libraries/valanx/mangrove/lib/core/MangroveUtils',
	'libraries/valanx/mangrove/lib/installers/joomla/JoomlaInstaller',
	'libraries/valanx/mangrove/lib/installers/joomla/JoomlaAssetInstaller',
	'libraries/valanx/mangrove/lib/installers/joomla/JoomlaComponentInstaller',
	'libraries/valanx/mangrove/lib/installers/joomla/JoomlaLibraryInstaller',
	'libraries/valanx/mangrove/lib/installers/joomla/JoomlaModuleInstaller',
	'libraries/valanx/mangrove/lib/installers/joomla/JoomlaPluginInstaller'
);

foreach ( $includes as $path ) {
	include_once($base . '/' . $path . '.php');
}
