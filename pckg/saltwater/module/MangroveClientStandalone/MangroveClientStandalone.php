<?php

namespace MangroveClientStandalone;

use Saltwater\Thing\Module;

class MangroveClientStandalone extends Module
{
	protected $require = array(
		'module' => array('MangroveClient\MangroveClient')
	);

	protected $provide = array(
		'provider' => array('config')
	);
}
