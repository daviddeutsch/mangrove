<?php

// Dont allow direct linking
defined('_JEXEC') or die( 'Direct Access to this location is not allowed.' );

require_once( dirname(__FILE__).'/autoload.php' );
include_once( JPATH_ROOT . '/libraries/redbean/redbean-tiny/rb.php' );
include_once( JPATH_ROOT . '/libraries/valanx/jredbean/src/jRedBean/jR.php' );
include_once( JPATH_ROOT . '/libraries/valanx/jredbean/src/jRedBean/jMysqlQueryWriter.php' );
include_once( JPATH_ROOT . '/libraries/valanx/jredbean/src/jRedBean/jPostgreSqlQueryWriter.php' );
include_once( JPATH_ROOT . '/libraries/valanx/jredbean/src/jRedBean/jSQLiteTQueryWriter.php' );

jR::create();
jR::context('mangrove');

$mangrove = new MangroveApp();

if ( !empty( $_GET['task'] ) ) {
	echo $mangrove->resolve($_GET['task']);
} else {
	echo $mangrove->resolve('app');
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
