<?php
/**
 * @version $Id: install.php
 * @package Mangrove
 * @copyright 2013 Copyright (C) David Deutsch
 * @author David Deutsch <skore@valanx.org> http://www.valanx.org
 * @license GNU/GPL v.3 http://www.gnu.org/licenses/gpl.html or, at your option, any later version
 */

// Dont allow direct linking
defined('_JEXEC') or die();

if ( !class_exists( 'Com_MangroveInstallerScript' ) ) {
	class Com_MangroveInstallerScript
	{
		function postflight( $type, $parent )
		{
			$this->install();
		}

		function install()
		{
			$app = JFactory::getApplication();

			$app->redirect( 'index.php?option=com_mangrove', $msg, $class );
		}
	}
}

if ( !function_exists( 'com_install' ) ) {
	function com_install()
	{
		$installer = new Com_MangroveInstallerScript;
		$installer->install();
	}
}

?>
