<?php

class MangroveApp
{
	/**
	 * Path to mangrove tmp directory
	 *
	 * @var string
	 */
	private $temp;

	/**
	 * Path to mangrove installation directory
	 *
	 * @var string
	 */
	private $com;

	/**
	 * List of installed payload for use on bootup in mangrove
	 *
	 * @var object
	 */
	private $payload;

	/**
	 * Load paths and payload json
	 */
	public function __construct()
	{
		$this->temp = JFactory::getApplication()->getCfg('tmp_path')
			. '/mangrove';

		if ( !is_dir($this->temp) ) mkdir($this->temp, 0744);

		$this->com = dirname(__FILE__);

		if ( file_exists($this->temp . '/payload.json') ) {
			$this->payload = MangroveUtils::getJSON($this->temp . '/payload.json');

			unlink($this->temp . '/payload.json');
		}

		$this->update();
	}

	function update()
	{
		if ( !empty($this->payload) ) {
			foreach ( $this->payload->payload as $id => $package ) {
				if ( is_string($package) ) {
					$sha = $package;
				} else {
					$this->registerPackage($package);

					$sha = $package->sha;
				}

				// Double-check everything is nice and tidy
				if ( is_dir($this->temp.'/'.$sha) ) {
					MangroveUtils::rrmdir($this->temp.'/'.$sha);
				}

				if ( file_exists($this->temp.'/'.$sha.'.zip') ) {
					unlink($this->temp.'/'.$sha.'.zip');
				}
			}
		}
	}

	private function registerPackage( $package )
	{
		$entry = R::_('package');

		foreach ( get_object_vars($package) as $key => $value ) {
			switch ( $key ) {
				case 'payload':
					$entry->installed = $value->installed_time;
					break;
				case 'time':
					$entry->created = R::isoDateTime($value);
					break;
				case 'version':
					$version = $this->explodeVersion($value);

					foreach ( $version as $k => $v ) {
						$entry->{'version_'.$k} = $v;
					}
					break;
				default:
					if ( is_string($value) ) {
						$entry->$key = $value;
					}
			}
		}

		if ( empty($entry->installed) ) {
			$entry->installed = R::isoDateTime();
		}

		R::_($entry);
	}

	private function explodeVersion( $version )
	{
		$return = array();

		$v = explode('.', $version, 3);

		$return['major'] = $v[0];
		$return['minor'] = $v[1];

		if ( is_numeric($v[2]) ) {
			$return['patch'] = $v[2];
			$return['meta'] = null;
		} else {
			preg_match('/\d+/', $v[2], $regs);

			$return['patch'] = $regs[0];

			$return['meta'] = substr( $v[2], strlen($regs[0]) );
		}

		return $return;
	}

	function resolve( $task )
	{
		$method = strtolower($_SERVER['REQUEST_METHOD']).ucfirst($task);

		if ( method_exists($this, $method) ) {
			return $this->$method();
		} else {
			return $this->getApp();
		}
	}

	function getApp()
	{
		$csslink = '<link rel="stylesheet" type="text/css" media="all" href="' . JURI::root(true).'/media/com_mangrove/css/%s.css" />';

		$document = JFactory::getDocument();

		$v = new JVersion();
		if ( $v->isCompatible('3.0') ) {
			$document->addCustomTag( sprintf($csslink, 'joomla3-override') );
		} else {
			$document->addCustomTag( sprintf($csslink, 'bootstrap-combined.min') );
			$document->addCustomTag( sprintf($csslink, 'joomla-override') );
		}

		$document->addCustomTag( sprintf($csslink, 'mangrove') );

		$document->addCustomTag( sprintf($csslink, '../js/angular-ui.min') );

		$jsfiles = array(
			'jquery.min',
			'angular.min',
			'angular-resource',
			'angular-ui.min',
			'angular-ui-router.min',
			//'ui-bootstrap-tpls-0.2.0.min',
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

	/**
	 * Make a unique hash for this site.
	 *
	 * Uses the URI root of the server as a unique identifier and
	 * the dbprefix as a salt to make it harder to bruteforce
	 *
	 * dbprefix was chosen because it's unlikely to change unless the
	 * site is done over (in which case it's a "new" site anyhow)
	 *
	 * @return string
	 */
	private function makeSiteHash()
	{
		$app = JFactory::getApplication();

		return sha1( JURI::root().$app->getCfg('dbprefix') );
	}

	public static function hook( $payload )
	{

	}

	private function makeCallback()
	{
		include_once( 'path/to/plugin.php');

		$utils = new TinyHookUtils();

		$utils->create(
			'path/to/mangrove.php',
			'Mangrove::hook'
		);
	}

}
