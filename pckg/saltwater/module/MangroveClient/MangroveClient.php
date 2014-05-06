<?php

namespace MangroveClient;

use Saltwater\Thing\Module;

class MangroveClient extends Module
{
	public $namespace = 'MangroveClient';

	protected $dependencies = array(
		'Saltwater\Root\Root'
	);

	protected $providers = array('http', 'uuid');

	protected $contexts = array('MangroveClient');

	protected $services = array(
		'app', 'source', 'stream'
	);
}
