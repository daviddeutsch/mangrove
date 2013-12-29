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

	/**
	 * Load paths and payload json
	 */
	public function __construct()
	{
		$this->temp = JFactory::getApplication()->getCfg('tmp_path');

		$installs = glob($this->temp.'/install*', GLOB_ONLYDIR);

		$this->base = array_pop($installs);

		$this->mangrove = $this->temp . '/mangrove';

		if ( !is_dir($this->mangrove) ) mkdir($this->mangrove, 0744);

		$this->com = JPATH_ROOT . '/administrator/components/com_mangrove';

		if ( file_exists($this->mangrove.'/payload.json') ) {
			// Recover from an inbetween bootstrap/first setup exit
			$this->payload = self::merge(
				self::getJSON($this->base . '/info.json'),
				self::getJSON($this->mangrove . '/payload.json')
			);
		} else {
			$this->payload = self::getJSON($this->base . '/info.json');
		}
	}

	public function postflight( $type, $parent )
	{
		$this->install();
	}

	public function install()
	{
		// Move payload to mangrove temp directory
		foreach ( glob($this->base.'/payload/*.zip') as $zip ) {
			rename($zip, $this->mangrove.'/'.basename($zip));
		}

		self::rrmdir($this->base);

		if ( !$this->detectMangrove() ) {
			$this->installMangrove();
		}

		// Write payload.json so that the mangrove app can take it from there
		self::putJSON( $this->mangrove.'/payload.json', $this->payload );

		// Head for the mangroves!
		JFactory::getApplication()->redirect('index.php?option=com_mangrove');
	}

	/**
	 * Install Core and its dependencies
	 */
	private function installMangrove()
	{
		foreach (
			array(
				'mangrove/core',
				'installers/',
				'redbean/redbean',
				'valanx/jredbean'
			) as $install
		) {
			foreach ( $this->payload->payload as $k => $v ) {
				if ( strpos($k, $install) === false ) continue;

				// Don't install previously installed objects (install recovery)
				if ( !is_string($v) ) continue;

				$this->installPackage($this->mangrove.'/'.$v);
			}
		}
	}

	private function installPackage( $path )
	{
		$target = $this->unzip($path);

		$info = self::getJSON($target . '/info.json');

		$this->mockInstaller($info, $target);

		$info->payload = (object) array(
			'installed_time' => (int) gmdate('U')
		);

		$this->registerPackage( $info );
	}

	private function unzip( $path )
	{
		$file = pathinfo($path);

		$target = $this->mangrove . '/' . $file['filename'];

		if ( !is_dir($target) ) mkdir($target, 0744);

		$zip = new ZipArchive();

		$zip->open($path);

		$zip->extractTo($target);

		return $target;
	}

	private function mockInstaller( $info, $source )
	{
		switch ( $info->type ) {
			// Standard Joomla Installer
			case 'joomla-component':
			case 'joomla-library':
				$installer = new JInstaller();

				$installer->install($source);
				break;

			// Composer-style library in cms/libraries
			case 'library':
				$path = JPATH_ROOT . '/libraries/' . $info->name;

				if ( !is_dir($path) ) mkdir($path, 0744, true);

				rename($source, $path);
				break;

			//  Basic file copying
			case 'mangrove-installer':
				$path = $this->com
					. str_replace( 'valanx/mangrove', '', $info->name );

				if ( !is_dir($path) ) mkdir($path, 0744, true);

				rename($source, $path);
				break;
		}

		self::rrmdir($source);
	}

	private function registerPackage( $info )
	{
		$this->payload->payload->{$info->name} = $info;
	}

	private function detectMangrove()
	{
		foreach (
			array(
				'libraries/redbean/redbean',
				'libraries/valanx/jredbean',
				'administrator/components/com_mangrove',
				'administrator/components/com_mangrove/installers'
			) as $dir
		) {
			if ( !is_dir(JPATH_ROOT.'/'.$dir) ) return false;
		}

		return true;
	}

	private static function getJSON( $path )
	{
		return json_decode( file_get_contents($path) );
	}

	private static function putJSON( $path, $data )
	{
		return file_put_contents( $path, json_encode($data) );
	}

	private static function rrmdir( $path )
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

	private static function merge( $a, $b )
	{
		foreach ( $b->payload as $k => $v ) {
			if ( is_object($v) ) {
				$a->payload->$k = $v;
			}
		}

		return $a;
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
