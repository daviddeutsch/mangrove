<?php

class MangroveClientApp extends MangroveAppInstance
{
	public $name = 'mangrove';

	public $base_path;

	/**
	 * Path to mangrove tmp directory
	 *
	 * @var string
	 */
	public $temp;

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
		$japp = JFactory::getApplication();

		$this->temp = JPATH_ROOT . '/administrator/components/com_mangrove';

		$this->temp = $japp->getCfg('tmp_path') . '/mangrove';

		// 755 because apache needs that sometimes
		if ( !is_dir($this->temp) ) mkdir($this->temp, 0755);

		$this->com = dirname(__FILE__);
	}

	public function ready()
	{
		if ( file_exists($this->temp . '/payload.json') ) {
			$this->payload = MangroveUtils::getJSON($this->temp . '/payload.json');

			$this->bootstrapMangrove();

			unlink($this->temp . '/payload.json');
		}
	}

	private function bootstrapMangrove()
	{
		if ( empty($this->payload) ) return;

		$packages = array();
		foreach ( $this->payload->payload as $file ) {
			$sha = str_replace('.zip', '', $file);

			if ( empty($sha) ) continue;

			$package = MangroveApp::$r->x->one->package->sha($sha)->find();

			if ( !empty($package->id) ) {
				$packages[] = $package;

				continue;
			}

			$package = MangroveApp::$r->_('package');

			$package->fromSource($sha);

			$package->status = 'downloaded';

			MangroveApp::$r->_($package);

			$packages[] = $package;
		}

		foreach ( $packages as $package ) {
			$installer = $package->getInstaller();

			$installer->process();
		}
	}

	public function getApp()
	{
		$v = new JVersion();

		$this->addAssets(
			'css',
			array(
				'font-awesome.min',
				'joomla' . ($v->isCompatible('3.0') ? '3' : '') . '-override',
				'mangrove'
			)
		);

		$this->addAssets(
			'js',
			array(
				'jquery-1.7.2.min',
				'angular.min', 'angular-animate.min',
				'ui-bootstrap-tpls.min', 'angular-ui-router.min',
				'observe', 'omnibinder',
				'spinners.min',
				'mangroveBase',
				'mangroveApp'
			)
		);

		return null;
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
		$japp = JFactory::getApplication();

		return sha1( JURI::root() . $japp->getCfg('dbprefix') );
	}

	public function hook( $payload )
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
