<?php

class MangroveApp
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
	 * @var RedBean_Instance
	 */
	public static $r;

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

		self::getDB($japp);

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

	public static function resolve( $task )
	{
		if ( empty($task) ) return self::getApp();

		$method = strtolower($_SERVER['REQUEST_METHOD']) . ucfirst($task);

		$service = ucfirst($_REQUEST['service']) . 'Service';

		$input = @file_get_contents('php://input');

		if ( !$input ) {
			$input = '';
		} else {
			$input = json_decode($input);
		}

		if ( class_exists($service) ) {
			$service = new $service();

			$result = $service->call($method, $_REQUEST['path'], $input);

			echo json_encode($result);

			exit;
		}

		return null;
	}

	public static function getApp()
	{
		$csslink = '<link rel="stylesheet" type="text/css" media="all" href="' . JURI::root() . 'media/com_mangrove/css/%s.css" />';

		$document = JFactory::getDocument();

		$v = new JVersion();
		if ( $v->isCompatible('3.0') ) {
			$document->addCustomTag( sprintf($csslink, 'joomla3-override') );
		} else {
			$document->addCustomTag( sprintf($csslink, 'bootstrap.min') );
			$document->addCustomTag( sprintf($csslink, 'font-awesome.min') );
			$document->addCustomTag( sprintf($csslink, 'joomla-override') );
		}

		$document->addCustomTag( sprintf($csslink, 'mangrove') );

		$jsfiles = array(
			'jquery-1.7.2.min',
			'angular.min',
			'angular-animate.min',
			'angular-resource.min',
			'angular-route.min',
			'ui-bootstrap-tpls.min',
			'angular-ui-router.min',
			'spinners.min',
			'observe',
			'omnibinder',
			'lodash.min',
			'restangular.min',
			'mangroveApp'
		);

		foreach ( $jsfiles as $file ) {
			if ( $file == 'angular.min' ) {
				$document->addScript( 'https://ajax.googleapis.com/ajax/libs/angularjs/1.2.10/angular.js' );
 			} else {
				$document->addScript( JURI::root() . 'media/com_mangrove/js/' . $file . '.js' );
			}
		}

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

	/**
	 * @param object $japp JApplication
	 */
	private static function getDB( $japp )
	{
		self::$r = new RedBean_Instance();

		if ( $japp->getCfg('dbtype') == 'mysqli' ) {
			$type = 'mysql';
		} else {
			$type = $japp->getCfg('dbtype');
		}

		self::$r->addDatabase(
			'joomla',
			$type . ':'
			. 'host=' . $japp->getCfg('host') . ';'
			. 'dbname=' . $japp->getCfg('db'),
			$japp->getCfg('user'),
			$japp->getCfg('password')
		);

		self::$r->selectDatabase('joomla');

		self::$r->prefix($japp->getCfg('dbprefix') . 'mangrove_');

		self::$r->setupPipeline($japp->getCfg('dbprefix'));

		self::$r->redbean->beanhelper->setModelFormatter(new MangroveModelFormatter);
	}

}
