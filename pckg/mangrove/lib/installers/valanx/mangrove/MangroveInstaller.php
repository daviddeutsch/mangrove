<?php

class MangroveInstaller
{
	protected $assets = array(
		'js' => array(),
		'css' => array(),
		'img' => array()
	);

	protected $autoload = array();

	protected $package;

	public function __construct( $package )
	{
		$this->package = $package;
	}

	public function process()
	{
		$this->beforeInstall();

		$this->install();

		$this->afterInstall();
	}

	public function beforeInstall()
	{
		$this->ensureDependencies();
	}

	public function install()
	{

	}

	public function afterInstall()
	{

	}

	protected function ensureDependencies()
	{
		if ( !empty($this->package->info->require) ) {
			foreach ( $this->package->info->require as $id => $name ) {
				$package = MangroveApp::$r->_('package');

				$package->fromSource($id);

				$installer = $package->getInstaller();

				$installer->process();

				$assets = $installer->getAssets();

				foreach ( $assets as $type => $a ) {
					foreach ( $a as $path ) {
						$this->assets[$type][] = $path;
					}
				}
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
