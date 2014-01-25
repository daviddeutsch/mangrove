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

	public function install()
	{
		$this->ensureDependencies();
	}

	protected function ensureDependencies()
	{
		if ( !empty($this->package->info->require) ) {
			foreach ( $this->package->info->require as $name ) {
				$installer = MangroveApp::getInstaller($name);

				$installer->install();

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
