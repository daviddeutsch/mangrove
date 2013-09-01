<?php
defined('_JEXEC') or die();

jimport('joomla.plugin.plugin');

/**
 * Tinyhook
 *
 * @author David Deutsch <skore@valanx.org> - http://www.valanx.org
 */
class plgSystemTinyhook extends JPlugin
{
	function plgSystemTinyhook( &$subject, $config ) {
		parent::__construct( $subject, $config );
	}

	function onAfterRoute()
	{
		// Check if frontend
		if ( strpos( JPATH_BASE, '/administrator' ) ) return true;

		$uri = JURI::getInstance();

		// Check if hook exists
		if ( !$uri->hasVar('tinyhook') ) return true;

		$hook = preg_replace( '`[^a-z0-9]+`', '', $uri->getVar( 'tinyhook' ) );

		$utils = new TinyHookUtils();

		$hook = $utils->get( $hook );

		$secret = preg_replace( '`[^a-z0-9]+`', '', $_POST['secret'] );

		// Check if secret is valid
		if ( sha1($secret) != $hook->secret ) return true;

		$payload = json_decode( $_POST['payload'] );

		// Check if hook is callable
		if ( !$utils->check( $hook->path, $hook->callable ) ) return true;

		// All Systems On Go
		call_user_func( $hook->callable, $payload );

		return true;
	}


}

class TinyHookUtils
{
	function get( $hash )
	{
		$db = &JFactory::getDBO();

		$sql = 'SELECT *'
			. ' FROM #__plg_tinyhook'
			. ' WHERE `hash` = \'' . $hash . '\'';

		$db->setQuery($sql);

		return $db->loadObject();
	}

	function create( $path, $callable )
	{
		$db = &JFactory::getDBO();

		$secret = sha1( mt_rand().mt_rand().mt_rand() );

		$hash = sha1( $path.$callable.mt_rand() );

		$url = JURI::root() . '/index.php?tinyhook=' . $hash;

		$sql = 'INSERT INTO #__plg_tinyhook'
			. ' (`created_date`, `hash`, `secret`, `path`, `callable`)'
			. ' VALUES ('
			. '\'' . date( 'Y-m-d H:i:s', ( (int) gmdate('U') ) ) . '\','
			. '\'' . $hash . '\','
			. '\'' . sha1($secret) . '\','
			. '\'' . $path . '\','
			. '\'' . $callable . '\''
			. ')'
		;

		$db->setQuery($sql);

		if ( !$db->query() ) return false;

		return array(
			'url' => $url,
			'hash' => $hash,
			'secret' => $secret,
		);
	}

	function check( $path, $callable )
	{
		if ( !file_exists( $path ) ) return false;

		include_once( $path );

		if ( !is_callable( $callable ) ) return false;

		return true;
	}
}

?>
