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
		$li = array();
		$li[] = JHTML::_('select.option', 0, "--- --- ---" );

		if ( !empty( $this->settings['api_key'] ) ) {
			$MCAPI = new MCAPI( $this->settings['api_key'] );

			$lists = $MCAPI->lists();

			if ( !empty( $lists ) ) {
				foreach( $lists as $list ) {
					$li[] = JHTML::_('select.option', $list['id'], $list['name'] );
				}
			}
		}

		if ( !isset( $this->settings['list'] ) ) {
			$this->settings['list'] = 0;
		}

		if ( !isset( $this->settings['list_exp'] ) ) {
			$this->settings['list_exp'] = 0;
		}

		$settings = array();
		$settings['explanation']	= array( 'fieldset', JText::_('MI_MI_MANGROVE_ACCOUNT_ID_NAME_NAME'), JText::_('MI_MI_MANGROVE_ACCOUNT_ID_NAME_DESC') );
		$settings['api_key']		= array( 'inputC' );
		$settings['account_name']	= array( 'inputC' );
		$settings['account_id']		= array( 'inputC' );

		$settings['custom_details']	= array( 'inputD' );

		$settings['lists']['list']				= JHTML::_( 'select.genericlist', $li, 'list', 'size="4"', 'value', 'text', $this->settings['list'] );
		$settings['lists']['list_unsub']		= JHTML::_( 'select.genericlist', $li, 'list_unsub', 'size="4"', 'value', 'text', $this->settings['list_unsub'] );
		$settings['lists']['list_exp']			= JHTML::_( 'select.genericlist', $li, 'list_exp', 'size="4"', 'value', 'text', $this->settings['list_exp'] );
		$settings['lists']['list_exp_unsub']	= JHTML::_( 'select.genericlist', $li, 'list_exp_unsub', 'size="4"', 'value', 'text', $this->settings['list_exp_unsub'] );

		$settings['list']			= array( 'list' );
		$settings['list_unsub']		= array( 'list' );
		$settings['list_exp']		= array( 'list' );
		$settings['list_exp_unsub']	= array( 'list' );
		$settings['user_checkbox']	= array( 'toggle' );
		$settings['custominfo']		= array( 'inputD' );

		$rewriteswitches			= array( 'cms', 'user', 'expiration', 'subscription', 'plan', 'invoice' );
		$settings					= AECToolbox::rewriteEngineInfo( $rewriteswitches, $settings );

		return $settings;
	}

	function Defaults()
	{
		$settings = array();

		$settings['user_checkbox']		= 1;

		return $settings;
	}


	function profile_info( $request )
	{
		if ( !empty( $this->settings['api_key'] ) && !empty( $this->settings['account_id'] ) && !empty( $this->settings['account_name'] ) )  {
			$MCAPI = new MCAPI( $this->settings['api_key'] );

			$mcuser = $MCAPI->listMemberInfo( $this->settings['list'], $request->metaUser->cmsUser->email);

			if ( empty( $mcuser['id'] ) ) {
				$mcuser = $MCAPI->listMemberInfo( $this->settings['list_exp'], $request->metaUser->cmsUser->email);

				$listid = $this->settings['list_exp'];
			} else {
				$listid = $this->settings['list'];
			}

			$server = explode( '-', $this->settings['api_key'] );

			if ( !empty( $mcuser['id'] ) ) {
				$message = '<p><a href="http://' . $this->settings['account_name'] . '.' . $server[1] . '.list-manage.com/unsubscribe?u=' . $this->settings['account_id'] . '&id=' . $listid . '">Unsubscribe from our newsletter</a></p>';
				return $message;
			}
		}

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
