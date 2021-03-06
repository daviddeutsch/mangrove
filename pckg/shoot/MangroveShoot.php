<?php

class MangroveShoot
{
/**
	 * Path to shoot directory
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

		if ( !is_dir($this->temp) ) mkdir($this->temp, 0755);

		if ( file_exists($this->temp.'/payload.json') ) {
			$this->payload = self::mergePayload(
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

		// Remove shoot directories

		foreach(
			array(
				$this->base,
				JPATH_ROOT.'/administrator/components/com_mangroveshoot',
				JPATH_ROOT.'/components/com_mangroveshoot',
			) as $dir
		) {
			self::rrmdir($dir);
		}

		unlink($this->jtemp.'/mangrove-shoot.zip');

		if ( !$this->detectMangrove() ) {
			$this->installMangrove();
		}

		// Write payload.json so that the mangrove app can take it from there
		self::putJSON($this->temp.'/payload.json', $this->payload);

		// Head for the mangroves!
		JFactory::getApplication()->redirect('index.php?option=com_mangrove');

		return true;
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
				'mangrove-base/',
				'mangrove/lib/core',
				'installers/',
				'redbean/redbean',
				'mangrove/app'
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

	/**
	 * @param string $path
	 */
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

		if ( !is_dir($target) ) mkdir($target, 0755);

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

				$installer->install($source);
				break;

			// Composer-style library in cms/libraries
			case 'library':
			default:
				$path = JPATH_ROOT . '/libraries/' . $info->name;

				// If we have any version, that'll be good enough for now
				if ( is_dir($path) ) return;

				mkdir($path, 0744, true);

				self::rcopy($source, $path);
				break;
		}
	}

	/**
	 * @param string $path
	 */
	private static function getJSON( $path )
	{
		return json_decode( file_get_contents($path) );
	}

	/**
	 * @param string $path
	 */
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

	/**
	 * @param string $source
	 * @param string $destination
	 */
	private static function rcopy( $source, $destination )
	{
		if ( is_dir($source) ) {
			if ( !is_dir($destination) ) mkdir($destination, 0755);

			foreach ( glob($source . '/*') as $item ) {
				if ( is_dir($item) ) {
					self::rcopy($item, $destination . '/' . basename($item));
				} else {
					copy($item, $destination . '/' . basename($item));
				}
			}
		} elseif ( file_exists($source) ) {
			copy($source, $destination . '/' . basename($source));
		}
	}

	/**
	 * Merge Payload from payload.json file into package info.json file
	 *
	 * @param object $a info.json
	 * @param object $b payload.json
	 *
	 * @return object
	 */
	private static function mergePayload( $a, $b )
	{
		foreach ( $b->payload as $k => $v ) {
			if ( is_object($v) ) {
				$a->payload->$k = $v;
			}
		}

		return $a;
	}
}
