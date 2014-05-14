<?php

namespace MangroveClientStandalone;

use Saltwater\Thing\Module;

class MangroveClientStandalone extends Module
{
	public $namespace = 'MangroveClientStandalone';

	protected $require = array(
		'module' => array('MangroveClient\MangroveClient')
	);

	protected $provide = array(
		'provider' => array('config')
	);
}
