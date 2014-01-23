<?php

class MangroveInstaller
{
	private $info;

	private $assets = array(
		'js' => array(),
		'css' => array()
	);

	private $autoload = array();

	public function __construct( $info=null )
	{
		$this->info = $info;
	}

	public function install( $source )
	{

	}

	public function writeAutoload()
	{

	}
}
