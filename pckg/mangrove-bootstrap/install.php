<?php

jimport('joomla.installer.installer');

if ( !class_exists('Com_MangroveInstallerScript') ) {

/**
 * Mangrove Bootstrap installer
 *
 * Does the following:
 *
 * · Comes with a payload
 * · Payload needs to include RedBean and Joomla Installers
 * · Sets up RedBean, mangrove and basic installers
 * · Hands over data to the actual mangrove install
 *
 * @package Mangrove
 * @copyright 2013-2014 Copyright (C) David Deutsch
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
	private $base;

	/**
	 * Path to CMS temp directory
	 *
	 * @var string
	 */
	private $jtemp;

	/**
	 * Path to mangrove temp directory
	 *
	 * @var string
	 */
	private $temp;

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
		$app = JFactory::getApplication();

		$this->jtemp = $app->getCfg('tmp_path');

		$dirs = glob($this->jtemp.'/install*', GLOB_ONLYDIR);

		$this->base = array_pop($dirs);

		$this->temp = $this->jtemp . '/mangrove';

		if ( !is_dir($this->temp) ) mkdir($this->temp, 0744);

		if ( file_exists($this->temp.'/payload.json') ) {
			// Recover from an inbetween bootstrap/first setup exit
			$this->payload = self::merge(
				self::getJSON($this->base . '/info.json'),
				self::getJSON($this->temp . '/payload.json')
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
		// Unload payload to mangrove temp directory
		foreach ( glob($this->base.'/payload/*.zip') as $zip ) {
			rename($zip, $this->temp.'/'.basename($zip));
		}

		// Cleanup
		foreach(
			array(
				$this->base,
				JPATH_ROOT.'/administrator/components/com_mangrovebootstrap',
				JPATH_ROOT.'/components/com_mangrovebootstrap',
			) as $dir
		) {
			self::rrmdir($dir);
		}

		unlink($this->jtemp.'/mangrove-bootstrap.zip');

		if ( !$this->detectMangrove() ) {
			$this->installMangrove();
		}

		// Write payload.json so that the mangrove app can take it from there
		self::putJSON($this->temp.'/payload.json', $this->payload);

		// Head for the mangroves!
		JFactory::getApplication()->redirect('index.php?option=com_mangrove');
	}

	private function detectMangrove()
	{
		foreach (
			array(
				'libraries/redbean/redbean-adaptive',
				'administrator/components/com_mangrove',
				'libraries/valanx/mangrove/lib/core',
				'libraries/valanx/mangrove/lib/installers'
			) as $dir
		) {
			if ( !is_dir(JPATH_ROOT.'/'.$dir) ) return false;
		}

		return true;
	}

	/**
	 * Install Core and its dependencies
	 */
	private function installMangrove()
	{
		foreach (
			// Partial names work just fine
			array(
				'mangrove/lib/core',
				'mangrove/app',
				'installers/',
				'redbean/redbean'
			) as $install
		) {
			foreach ( $this->payload->payload as $k => $v ) {
				if ( strpos($k, $install) === false ) continue;

				// Don't install previously installed objects (install recovery)
				if ( !is_string($v) ) continue;

				$this->installPackage($this->temp.'/'.$v);
			}
		}
	}

	private function installPackage( $path )
	{
		$path = $this->unzip($path);

		$info = self::getJSON($path . '/info.json');

		$this->mockInstaller($info, $path);
	}

	private function unzip( $path )
	{
		$file = pathinfo($path);

		$target = $this->temp . '/' . $file['filename'];

		if ( !is_dir($target) ) mkdir($target, 0744);

		$zip = new ZipArchive();

		$zip->open($path);

		$zip->extractTo($target);

		return $target;
	}

	/**
	 * Emulate the behavior of actual mangrove installers for now
	 *
	 * @param object $info
	 * @param string $source
	 */
	private function mockInstaller( $info, $source )
	{
		switch ( $info->type ) {
			// Standard Joomla Installer
			case 'joomla-component':
				$installer = new JInstaller();

				$installer->install($source);print_r($installer);exit;
				break;

			// Composer-style library in cms/libraries
			case 'library':
			default:
				$path = JPATH_ROOT . '/libraries/' . $info->name;

				// If we have any version, that'll be good enough for now
				if ( is_dir($path) ) return;

				mkdir($path, 0744, true);

				rename($source, $path);
				break;
		}
	}

	private static function getJSON( $path )
	{
		return json_decode( file_get_contents($path) );
	}

	private static function putJSON( $path, $data )
	{
		if ( version_compare(phpversion(), '5.4.0', '>') ) {
			file_put_contents(
				$path,
				json_encode(
					$data,
					JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
				)
			);
		} else {
			file_put_contents($path, json_encode($data));
		}
	}

	/**
	 * Recursive removal of an entire directory
	 *
	 * @param string $path
	 */
	private static function rrmdir( $path )
	{
		if ( !is_dir($path) ) return;

		foreach ( glob($path . '/*') as $item ) {
			if ( is_dir($item) ) {
				self::rrmdir($item);
			} else {
				unlink($item);
			}
		}

		rmdir($path);
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
