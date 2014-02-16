<?php

class MangroveInstaller
{
	protected $assets = array(
		'js' => array(),
		'css' => array(),
		'img' => array(),
		'fonts' => array()
	);

	protected $autoload = array();

	protected $package;

	public function __construct( $package )
	{
		$this->package = $package;
	}

	public function process()
	{
		if ( !is_dir($this->package->source) ) return false;

		$this->beforeInstall();

		$this->install();

		$this->afterInstall();

		return true;
	}

	public function beforeInstall()
	{
		$this->ensureDependencies();
	}

	public function install()
	{
		if ( $this->package->status == 'installed' ) return;
	}

	public function afterInstall()
	{
		if ( $this->package->status == 'installed' ) return;

		$this->package->status = 'installed';

		MangroveApp::$r->_($this->package);
	}

	protected function ensureDependencies()
	{
		if ( empty($this->package->info->require) ) return;

		$list = new RequireIterator($this->package->info->require);

		foreach ( $list as $name => $info ) {
			$packages = MangroveUtils::findPackages($name);

			if ( empty($packages) ) continue;

			foreach ( $packages as $package ) {
				$installer = $package->getInstaller();

				$installer->process();

				$this->mergeAssets($installer->getAssets());
			}
		}
	}

	public function mergeAssets( $assets )
	{
		if ( empty($assets) ) return;

		foreach ( $assets as $type => $a ) {
			foreach ( $a as $path ) {
				$this->assets[$type][] = $path;
			}
		}
	}

	public function getAssets()
	{
		return $this->assets;
	}

	public function writeAutoload()
	{

	}
}
