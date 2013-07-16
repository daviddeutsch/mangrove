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
}
