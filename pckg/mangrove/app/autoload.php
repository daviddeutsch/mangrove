<?php

/*
 * Mock Autoloader to supply the base classes that mangrove needs to boot up
 *
 * Will be replaced during self install
 */

$base = realpath( dirname(__FILE__) . '/../../../libraries' );

$c = 'valanx/mangrove/lib/core/';
$i = 'valanx/mangrove/lib/installers/';
$b = 'valanx/mangrove-base/lib/core/';

$includes = array(
	'redbean/redbean-adaptive/rb',

	$b . 'MangroveApp',
	$b . 'MangroveAppInstance',
	$b . 'MangroveModelFormatter',

	$b . 'Services/AbstractService',
	$b . 'Services/RestService',
	$b . 'Services/HookService',

	$c . 'MangroveClientApp',
	$c . 'MangroveAutoloadGenerator',
	$c . 'MangroveClassLoader',
	$c . 'MangroveConnector',
	$c . 'MangroveUtils',

	$c . 'Models/PackageModel',

	$c . 'Services/ApplicationService',
	$c . 'Services/PackageService',
	$c . 'Services/RepositoryService',

	$i . 'valanx/mangrove/MangroveInstaller',
	$i . 'valanx/mangrove/AssetInstaller',
	$i . 'valanx/mangrove/LibraryInstaller',

	$i . 'joomla/JoomlaInstaller',
	$i . 'joomla/JoomlaComponentInstaller',
	$i . 'joomla/JoomlaLibraryInstaller',
	$i . 'joomla/JoomlaModuleInstaller',
	$i . 'joomla/JoomlaPluginInstaller'
);

foreach ( $includes as $path ) {
	include_once($base . '/' . $path . '.php');
}
