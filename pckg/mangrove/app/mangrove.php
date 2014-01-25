<?php

// Dont allow direct linking
defined('_JEXEC') or die( 'Direct Access to this location is not allowed.' );

require_once( dirname(__FILE__) . '/autoload.php' );

MangroveApp::init();

if ( $_GET['task'] == 'nuke' ) {
	$db = JFactory::getDBO();

	$db->setQuery(
		'SELECT extension_id'
		. ' FROM #__extensions'
		. ' WHERE `name` = \'com_mangrove\''
	);

	$id = $db->loadResult();

	$installer = JInstaller::getInstance();

	$installer->uninstall('component', $id);

	MangroveUtils::rrmdir(MangroveApp::$temp);

	MangroveUtils::rrmdir(JPATH_ROOT . '/libraries/valanx');
	MangroveUtils::rrmdir(JPATH_ROOT . '/libraries/redbean');
}

if ( !empty( $_GET['task'] ) ) {
	echo MangroveApp::resolve($_GET['task']);
} else {
	MangroveApp::getApp();

	include dirname(__FILE__) . '/templates/main.html';
}
