<?php
/**
 * @version $Id: mi_mangrove.php
 * @package AEC - Account Control Expiration - Membership Manager
 * @subpackage Micro Integrations - Mangrove
 * @copyright 2013 Copyright (C) David Deutsch
 * @author David Deutsch <skore@valanx.org> & Team AEC - http://www.valanx.org
 * @license GNU/GPL v.3 http://www.gnu.org/licenses/gpl.html or, at your option, any later version
 */

// Dont allow direct linking
defined('_JEXEC') or die( 'Direct Access to this location is not allowed.' );

class mi_mangrove
{
	function Info()
	{
		$info = array();
		$info['name'] = JText::_('AEC_MI_NAME_MANGROVE');
		$info['desc'] = JText::_('AEC_MI_DESC_MANGROVE');
		$info['type'] = array( 'system.software', 'vendor.mangrove' );

		return $info;
	}

	function Settings()
	{
		$settings = array();

		return $settings;
	}

	function Defaults()
	{
		$settings = array();

		return $settings;
	}


	function profile_info( $request )
	{


		return '';
	}

	function getMIform( $request )
	{
		$settings = array();

		if ( empty( $this->settings['user_checkbox'] ) ) {
			return $settings;
		}


	}

	function expiration_action( $request )
	{
		if ( empty( $this->settings['list_exp'] ) ) {
			return null;
		}


	}

	function action( $request )
	{
		if ( empty( $this->settings['list'] ) ) {
			return null;
		}


	}

	/**
	 * Return all the data for this client account:
	 *
	 * · Account details with permissions
	 * · Registered sites
	 */
	function getData()
	{

	}

	/**
	 * Remove a site from your registered sites list
	 *
	 * @param $hash
	 */
	function unregisterSite( $hash )
	{

	}

}
