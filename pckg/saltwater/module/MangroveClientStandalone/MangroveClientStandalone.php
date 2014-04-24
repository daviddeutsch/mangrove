<?php

namespace MangroveClientStandalone;

use Saltwater\Thing\Module;

class MangroveClientStandalone extends Module
{
	protected $dependencies = array(
		'MangroveClient\MangroveClient'
	);

	protected $providers = array(
		'config'
	);

}
