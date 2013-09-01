<?php

include_once( __DIR__ . '/lib/redbean/rb.php' );

$mangrove = new Mangrove();

if ( !empty( $_GET['task'] ) ) {
	$mangrove->resolve($_GET['task']);
} else {
	$mangrove->resolve("");
}

class Mangrove
{
	function __construct()
	{
		$this->update();
	}

	function update()
	{

	}

	function resolve( $task )
	{
		$method = strtolower( $_SERVER['REQUEST_METHOD'] ).ucfirst($task);

		if ( method_exists( $this, $method ) ) {
			return $this->$method();
		} else {
			return $this->getApp();
		}
	}

	function getApp()
	{
		$csslink = '<link rel="stylesheet" type="text/css" media="all" href="' . JURI::root(true).'/media/com_mangrove/css/%s.css" />';

		$document =& JFactory::getDocument();

		$v = new JVersion();
		if ( $v->isCompatible('3.0') ) {
			$document->addCustomTag( sprintf( $csslink, 'joomla3-override' ) );
		} else {
			$document->addCustomTag( sprintf( $csslink, 'bootstrap-combined.min' ) );
			$document->addCustomTag( sprintf( $csslink, 'joomla-override' ) );
		}

		$document->addCustomTag( sprintf( $csslink, 'mangrove' ) );

		$document->addCustomTag( sprintf( $csslink, '../js/angular-ui.min' ) );

		$jsfiles = array(
			'jquery.min',
			'angular.min',
			'angular-resource',
			'angular-ui.min',
			'angular-ui-router.min',
			//'ui-bootstrap-tpls-0.2.0.min',
			'mangroveApp'
		);

		foreach ( $jsfiles as $file ) {
			$document->addScript( JURI::root(true).'/media/com_mangrove/js/'.$file.'.js' );
		}

		return include dirname(__FILE__).'/templates/main.html';
	}

	function getPackages()
	{
		echo '[{"repo1":{"test1":"test"}},{"repo2":{"test2":"test"}}]';
	}

	function getPackage()
	{
		echo '[{"repo1":{"test1":"test"}},{"repo2":{"test2":"test"}}]';
	}

	function staticGet( $package )
	{

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
	private function makeSiteHash()
	{
		$app = JFactory::getApplication();

		return sha1( JURI::root().$app->getCfg('dbprefix') );
	}

	static function getJSON( $path )
	{
		return json_decode( file_get_contents( $path ) );
	}

	static function storeJSON( $path, $content )
	{
		return file_put_contents(
			$path,
			json_encode(
				$content,
				JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
			)
		);
	}

	private static function db()
	{
		if ( empty( R::$toolboxes ) ) {
			R::setup(
				'mysql:host='.self::$config->database->host.';'
				.'dbname='.self::$config->database->name,
				self::$config->database->user,
				self::$config->database->password
			);
		}

		if ( !isset( R::$toolboxes[self::$config->database->name] ) ) {
			R::addDatabase(
				self::$config->database->name,
				'mysql:host='.self::$config->database->host.';'
				.'dbname='.self::$config->database->name,
				self::$config->database->user,
				self::$config->database->password
			);
		}

		R::selectDatabase( self::$config->database->name );
		//R::$writer->setUseCache(true);
	}

	public static function hook( $payload )
	{

	}

	private function makeCallback()
	{
		include_once( 'path/to/plugin.php');

		$utils = new TinyHookUtils();

		$utils->create(
			'path/to/mangrove.php',
			'Mangrove::hook'
		);
	}
}

class mangroveConnector
{
	public function getToken( $sha )
	{
		$v = new JVersion();

		return array(
			'sha' => $sha,
			'client' => array(
				'php' => PHP_VERSION,
				'joomla' => array(
					'short' => $v->getShortVersion(),
					'long' => $v->getLongVersion()
				)
			)
		);
	}

	public function registerHook()
	{

	}
}
