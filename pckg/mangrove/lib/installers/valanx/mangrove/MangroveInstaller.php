<?php

class MangroveInstaller
{
	private $info;

	private $assets = array(
		'js' => array(),
		'css' => array(),
		'img' => array()
	);

	private $autoload = array();

	public function __construct( $info=null )
	{
		$this->info = $info;
	}

	public function install( $source )
	{
		$this->ensureDependencies();
	}

	public function ensureDependencies()
	{
		if ( !empty($this->info->require) ) {
			foreach ( $this->info->require as $name ) {
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
