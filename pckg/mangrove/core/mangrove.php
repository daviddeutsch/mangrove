<?php

$mangrove = new Mangrove();
$mangrove->start();

if ( !empty( $_GET['task'] ) ) {
	$mangrove->resolve($_GET['task']);
} else {
	$mangrove->resolve("");
}

class Mangrove
{
	function start()
	{
		$this->update();
	}

	function update()
	{

	}

	function resolve($task)
	{
		$method = 'get'.ucfirst($task);

		if ( method_exists( $this, $method ) ) {
			return $this->$method();
		} else {
			$this->showApp();
		}
	}

	function showApp()
	{
		$v = new JVersion();

		$csslink = '<link rel="stylesheet" type="text/css" media="all" href="' . JURI::root(true).'/media/com_mangrove/css/%s.css" />';

		$document =& JFactory::getDocument();

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
			'angular-ui-states.min',
			'ui-bootstrap-tpls-0.2.0.min',
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
}
