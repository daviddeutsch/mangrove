<?php

jimport('joomla.installer.installer');

// Use MangroveShoot

// Compat for Joomla <2.x
if ( !function_exists( 'com_install' ) ) {
	function com_install()
	{
		$installer = new Com_MangroveInstallerScript;

		return $installer->install();
	}
}
