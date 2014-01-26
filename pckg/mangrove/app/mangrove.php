<?php

// Dont allow direct linking
defined('_JEXEC') or die( 'Direct Access to this location is not allowed.' );

require_once( dirname(__FILE__) . '/autoload.php' );

MangroveApp::init();

if ( !empty( $_GET['task'] ) ) {
	echo MangroveApp::resolve($_GET['task']);
} else {
	MangroveApp::getApp();

	include dirname(__FILE__) . '/templates/main.html';
}
