<?php

if ( !class_exists( 'Com_MangroveInstallerScript' ) ) {

/**
 * Mangrove Bootstrap installer
 *
 * Does the following:
 *
 * · Requires being deployed with a payload
 * · Payload needs to include RedBean and Joomla Installers
 * · Sets up RedBean and Joomla Installers
 * · Continues to pull itself up by its bootstraps
 *
 * @package Mangrove
 * @copyright 2013 Copyright (C) David Deutsch
 * @author David Deutsch <skore@valanx.org> http://www.valanx.org
 * @license GNU/GPL v.3 or later
 */
class Com_MangroveInstallerScript
{
	function postflight( $type, $parent )
	{
		$this->install();
	}

	function install()
	{
		// Check whether mangrove already exists

		// Make sure we have the basic libraries in place

		// Load payload JSON
		$payload = self::getJSON( __DIR__.'/info.json' );

		// RedBean
		$redbean = '';
		foreach ( $payload->payload as $k => $v ) {
			if ( strpos( $k, 'redbean/redbean' ) ) {
				$redbean = $v;
			}
		}

		$this->installPackage( __DIR__.'/payload/'.$redbean.'.zip' );

		// All Installers
		foreach ( $payload->payload as $k => $v ) {
			if ( strpos( $k, 'installers/' ) ) {
				$this->installPackage( __DIR__.'/payload/'.$v.'.zip' );
			}
		}

		// Mangrove
		$mangrove = '';
		foreach ( $payload->payload as $k => $v ) {
			if ( strpos( $k, 'mangrove/core' ) ) {
				$mangrove = $v;
			}
		}

		$this->installPackage( __DIR__.'/payload/'.$mangrove.'.zip' );

		$app = JFactory::getApplication();

		$app->redirect( 'index.php?option=com_mangrove' );
	}

	function installPackage( $path )
	{
		$temp = 'JTEMP/mangrove';

		// Copy to temp directory

		$file = pathinfo( $path );

		$zip = new ZipArchive();

		$zip->open( $path );

		$target = $temp.'/'.$file['filename'];

		$zip->extractTo( $target );

		// Load info.json
		$info = self::getJSON( $target.'/info.json' );

		switch ( $info->type ) {
			case 'joomla-library':
				break;
			case 'mangrove-installer':

				break;
		}

		$this->registerPackage( $info );
	}

	function registerPackage( $info )
	{

	}

	static function getJSON( $path )
	{
		return json_decode( file_get_contents( $path ) );
	}
}
}

if ( !function_exists( 'com_install' ) ) {
	function com_install()
	{
		$installer = new Com_MangroveInstallerScript;
		$installer->install();
	}
}

?>
