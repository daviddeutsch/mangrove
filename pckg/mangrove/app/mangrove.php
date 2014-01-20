<?php

// Dont allow direct linking
defined('_JEXEC') or die( 'Direct Access to this location is not allowed.' );

require_once( dirname(__FILE__).'/autoload.php' );

$mangrove = new MangroveApp();

if ( !empty( $_GET['task'] ) ) {
	echo $mangrove->resolve($_GET['task']);
} else {
	$mangrove->resolve('app');

	include dirname(__FILE__).'/templates/main.html';
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
