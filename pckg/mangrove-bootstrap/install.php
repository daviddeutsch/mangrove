<?php

jimport('joomla.installer.installer');

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
	 * Path to CMS temp directory
	 *
	 * @var string
	 */
	private $temp;

	/**
	 * Path to bootstrap directory
	 *
	 * @var string
	 */
	private $base;

	/**
	 * Path to mangrove tmp directory
	 *
	 * @var string
	 */
	private $mangrove;

	/**
	 * Path to mangrove installation directory
	 *
	 * @var string
	 */
	private $com;

	/**
	 * List of installed payload for use on bootup in mangrove
	 *
	 * @var object
	 */
	private $payload;

	function __construct()
	{
		// Establish path names
		$this->temp = JFactory::getApplication()->getCfg('tmp_path');

		$this->base = array_pop( glob($this->temp.'/install*', GLOB_ONLYDIR) );

		$this->mangrove = $this->temp . '/mangrove';

		if ( !is_dir($this->mangrove) ) mkdir($this->mangrove, 0744);

		$this->com = JPATH_ROOT . '/administrator/components/com_mangrove';

		// Load payload JSON
		$this->payload = self::getJSON($this->base . '/info.json');
	}

	function postflight( $type, $parent )
	{
		$this->install();
	}

	function install()
	{
		// Move payload to mangrove temp directory
		foreach ( glob($this->base.'/payload/*.zip') as $zip ) {
			rename($zip, $this->mangrove.'/'.basename($zip));
		}

		// Cleanup
		self::rrmdir($this->base);

		/**
		 * TODO: Check whether mangrove already exists
		 *
		 * If it does exist, copy payload, write the json directive and
		 * redirect to mangrove
		 */

		// Install core and dependencies
		foreach (
			array(
				'mangrove/core',
				'installers/',
				'redbean/redbean',
				'valanx/jredbean'
			) as $install
		) {
			$this->installPayload($install);
		}

		// Write payload.json so that mangrove can take over
		self::putJSON( $this->mangrove.'/payload.json', $this->payload );

		JFactory::getApplication()->redirect('index.php?option=com_mangrove');
	}

	function installPayload( $key )
	{
		foreach ( $this->payload->payload as $k => $v ) {
			if ( strpos($k, $key) !== false ) {
				$this->installPackage($this->mangrove.'/'.$v);
			}
		}
	}

	function installPackage( $path )
	{
		$file = pathinfo($path);

		$zip = new ZipArchive();

		$zip->open($path);

		$target = $this->mangrove . '/' . $file['filename'];

		if ( !is_dir($target) ) mkdir($target, 0744);

		$zip->extractTo($target);

		// Load info.json
		$info = self::getJSON($target . '/info.json');

		// Since we have no installers yet, we emulate their core behavior
		switch ( $info->type ) {
			case 'joomla-component':
			case 'joomla-library':
				$installer = new JInstaller();

				$installer->install($target);
				break;
			case 'library':
				$path = JPATH_ROOT . '/libraries/' . $info->name;

				if ( !is_dir($path) ) mkdir($path, 0744, true);

				rename($target, $path);
				break;
			case 'mangrove-installer':
				$path = $this->com
					. str_replace( 'valanx/mangrove', '', $info->name );

				if ( !is_dir($path) ) mkdir($path, 0744, true);

				rename($target, $path);
				break;
		}

		self::rrmdir($target);

		$info->payload = (object) array(
			'installed_time' => (int) gmdate('U')
		);

		$this->registerPackage( $info );
	}

	function registerPackage( $info )
	{
		$this->payload->payload[$info->name] = $info;
	}

	static function getJSON( $path )
	{
		return json_decode( file_get_contents($path) );
	}

	static function putJSON( $data, $path )
	{
		return file_put_contents( $path, json_encode($data) );
	}

	static function rrmdir( $path )
	{
		if ( is_dir($path) ) {
			foreach ( glob($path . '/*') as $item ) {
				if ( is_dir($item) ) {
					self::rrmdir($item);
				} else {
					unlink($item);
				}
			}

			rmdir($path);
		}
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
