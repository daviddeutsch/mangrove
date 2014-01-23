<?php

/*
 * Mock Autoloader to supply the base classes that mangrove needs to boot up
 *
 * Will be replaced during self install
 */

$base = realpath( dirname(__FILE__) . '/../../../libraries' );

$includes = array(
	'redbean/redbean-adaptive/rb',
	'valanx/mangrove/lib/core/MangroveApp',
	'valanx/mangrove/lib/core/MangroveAutoloadGenerator',
	'valanx/mangrove/lib/core/MangroveClassLoader',
	'valanx/mangrove/lib/core/MangroveInstaller',
	'valanx/mangrove/lib/core/MangroveModelFormatter',
	'valanx/mangrove/lib/core/MangroveUtils',
	'valanx/mangrove/lib/installers/joomla/JoomlaInstaller',
	'valanx/mangrove/lib/installers/joomla/JoomlaAssetInstaller',
	'valanx/mangrove/lib/installers/joomla/JoomlaComponentInstaller',
	'valanx/mangrove/lib/installers/joomla/JoomlaLibraryInstaller',
	'valanx/mangrove/lib/installers/joomla/JoomlaModuleInstaller',
	'valanx/mangrove/lib/installers/joomla/JoomlaPluginInstaller'
);

foreach ( $includes as $path ) {
	include_once($base . '/' . $path . '.php');
}
