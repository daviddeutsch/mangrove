<?php

namespace MangroveConnector;

use Saltwater\Thing\Module;

class MangroveConnector extends Module
{
	public $namespace = 'MangroveConnector';

	protected $contexts = array('Connector');

	protected $providers = array(
		'db', 'pointer'
	);

	protected $services = array(
		'application', 'source', 'stream', 'branch'
	);

}
