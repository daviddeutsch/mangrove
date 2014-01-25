<?php

/*
 * Mock Autoloader to supply the base classes that mangrove needs to boot up
 *
 * Will be replaced during self install
 */

$base = realpath( dirname(__FILE__) . '/../../../libraries' );

$c = 'valanx/mangrove/lib/core/';
$i = 'valanx/mangrove/lib/installers/';

$includes = array(
	'redbean/redbean-adaptive/rb',
	$c . 'MangroveApp',
	$c . 'MangroveAutoloadGenerator',
	$c . 'MangroveClassLoader',
	$c . 'MangroveModelFormatter',
	$c . 'MangroveUtils',

	$c . 'Models/PackageModel',
	$c . 'Services/AbstractService',
	$c . 'Services/RestService',
	$c . 'Services/ApplicationService',
	$c . 'Services/PackageService',
	$c . 'Services/RepositoryService',

	$i . 'valanx/mangrove/MangroveInstaller',
	$i . 'joomla/JoomlaInstaller',
	$i . 'joomla/JoomlaAssetInstaller',
	$i . 'joomla/JoomlaComponentInstaller',
	$i . 'joomla/JoomlaLibraryInstaller',
	$i . 'joomla/JoomlaModuleInstaller',
	$i . 'joomla/JoomlaPluginInstaller'
);

foreach ( $includes as $path ) {
	include_once($base . '/' . $path . '.php');
}
