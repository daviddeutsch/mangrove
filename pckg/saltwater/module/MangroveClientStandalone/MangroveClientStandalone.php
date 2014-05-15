<?php

namespace MangroveClientStandalone;

use Saltwater\Thing\Module;

class MangroveClientStandalone extends Module
{
	public static $name = 'mangrove-client-standalone';

	public static $namespace = 'MangroveClientStandalone';

	protected $require = array(
		'module' => array('MangroveClient\MangroveClient')
	);

	protected $provide = array(
		'provider' => array('config')
	);
}
