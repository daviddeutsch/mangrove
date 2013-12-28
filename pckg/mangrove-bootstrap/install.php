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
	/**
	 * Path to bootstrap directory
	 *
	 * @var string
	 */
	private $base_path;

	/**
	 * Path to CMS temp directory
	 *
	 * @var string
	 */
	private $temp_path;

	/**
	 * Path to bootstrap payload directory
	 *
	 * @var string
	 */
	private $payload_path;

	/**
	 * Flag showing if the database is booted up
	 *
	 * @var array
	 */
	private $boot = array(
		'db' => false,
		'redbean' => false
	);

	private $installers = array();

	function __construct()
	{
		$this->base_path = dirname(__FILE__);

		$this->temp_path = realpath($this->base_path . '/../mangrove');

		if ( !is_dir($this->temp_path) ) {
			mkdir($this->temp_path);
		}

		$this->payload_path = $this->base_path . '/payload';
	}

	function postflight( $type, $parent )
	{
		$this->install();
	}

	function install()
	{
		// TODO: Check whether mangrove already exists

		// If it does exist, copy payload and redirect to mangrove
		// Although we have to be careful - maybe we want to update ourselves?

		// Make sure we have the basic libraries in place

		// Load payload JSON
		$payload = self::getJSON( __DIR__.'/info.json' );

		$this->installPayload($payload, 'redbean/redbean');

		if ( !$this->boot['redbean'] ) {
			// Try to acquire replacement package?
		}

		$this->installPayload($payload, 'installers/');

		$this->installPayload($payload, 'mangrove/core');

		$app = JFactory::getApplication();

		$app->redirect( 'index.php?option=com_mangrove' );
	}

	function installPayload( $payload, $key )
	{print_r($payload->payload);print_r($key);exit;
		foreach ( $payload->payload as $k => $v ) {
			if ( strpos($k, $key) !== false ) {
				$this->installPackage($this->payload_path . '/' . $k . '.zip');
			}
		}
	}

	function installPackage( $path )
	{
		$file = pathinfo($path);

		$zip = new ZipArchive();

		$zip->open($path);

		$target = $this->temp_path . '/' . $file['filename'];

		$zip->extractTo($target);

		// Load info.json
		$info = self::getJSON($target . '/info.json');

		switch ( $info->type ) {
			case 'joomla-library':
				break;
			case 'mangrove-installer':
				break;
		}

		if ( strpos('redbean', $path) ) {
			$this->boot['redbean'] = true;
		}

		$this->registerPackage( $info );
	}

	function registerPackage( $info )
	{
		if ( !$this->boot['db'] ) {
			//$this->db();
		}
	}

	private static function db()
	{
		R::addDatabase(
			self::$config->database->name,
			'mysql:host='.self::$config->database->host.';'
			.'dbname='.self::$config->database->name,
			self::$config->database->user,
			self::$config->database->password
		);

		R::selectDatabase( self::$config->database->name );
		//R::$writer->setUseCache(true);
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

/**
 * Dummy Class that only installs a package, used to bootstrap other installers
 */
class DummyInstaller
{

}
