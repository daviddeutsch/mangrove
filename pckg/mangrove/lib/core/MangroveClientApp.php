<?php

class MangroveClientApp
{
	/**
	 * Path to mangrove tmp directory
	 *
	 * @var string
	 */
	public static $temp;

	/**
	 * Path to mangrove installation directory
	 *
	 * @var string
	 */
	private static $com;

	/**
	 * List of installed payload for use on bootup in mangrove
	 *
	 * @var object
	 */
	private static $payload;

	/**
	 * Load paths and payload json
	 */
	public static function init()
	{
		$japp = JFactory::getApplication();

		self::$temp = $japp->getCfg('tmp_path') . '/mangrove';

		// 755 because apache needs that sometimes
		if ( !is_dir(self::$temp) ) mkdir(self::$temp, 0755);

		self::$com = dirname(__FILE__);

		if ( file_exists(self::$temp . '/payload.json') ) {
			self::$payload = MangroveUtils::getJSON(self::$temp . '/payload.json');

			self::bootstrap();

			//unlink(self::temp . '/payload.json');
		}
	}

	private static function bootstrap()
	{
		if ( empty(self::$payload) ) return;

		$packages = array();
		foreach ( self::$payload->payload as $file ) {
			$sha = str_replace('.zip', '', $file);

			if ( empty($sha) ) continue;

			$package = self::$r->x->one->package->sha($sha)->find();

			if ( !empty($package->id) ) {
				$packages[] = $package;

				continue;
			}

			$package = self::$r->_('package');

			$package->fromSource( $sha );

			$package->status = 'downloaded';

			self::$r->_($package);

			$packages[] = $package;
		}

		foreach ( $packages as $package ) {
			$installer = $package->getInstaller();

			$installer->process();
		}
	}

	public static function getApp()
	{
		$v = new JVersion();

		self::addAssets(
			'css',
			array(
				'font-awesome.min',
				'joomla' . ($v->isCompatible('3.0') ? '3' : '') . '-override',
				'mangrove'
			)
		);

		self::addAssets(
			'js',
			array(
				'jquery-1.7.2.min',
				'angular.min', 'angular-animate.min', 'angular-resource.min', 'angular-route.min',
				'ui-bootstrap-tpls.min', 'angular-ui-router.min',
				'lodash.min', 'restangular.min',
				'observe', 'omnibinder',
				'spinners.min',
				'mangroveBase',
				'mangroveApp'
			)
		);

		return null;
	}

	/**
	 * Make a unique hash for this site.
	 *
	 * Uses the URI root of the server as a unique identifier and
	 * the dbprefix as a salt to make it harder to bruteforce
	 *
	 * dbprefix was chosen because it's unlikely to change unless the
	 * site is done over (in which case it's a "new" site anyhow)
	 *
	 * @return string
	 */
	private static function makeSiteHash()
	{
		$japp = JFactory::getApplication();

		return sha1( JURI::root() . $japp->getCfg('dbprefix') );
	}

	public static function hook( $payload )
	{

	}

	private static function makeCallback()
	{
		include_once( 'path/to/plugin.php');

		$utils = new TinyHookUtils();

		$utils->create(
			'path/to/mangrove.php',
			'Mangrove::hook'
		);
	}

}
