<?php

namespace MangroveConnector;

use Saltwater\Thing\Module;

class MangroveConnector extends Module
{
	public $namespace = 'MangroveConnector';

	protected $dependencies = array(
		'MangroveClient\MangroveClient'
	);

	protected $contexts = array('Connector');

	protected $services = array(
		'application', 'source', 'stream', 'branch'
	);

}
