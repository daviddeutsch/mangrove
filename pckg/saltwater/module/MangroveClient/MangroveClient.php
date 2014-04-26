<?php

namespace MangroveClient;

use Saltwater\Thing\Module;

class MangroveClient extends Module
{
	public $namespace = 'MangroveClient';

	protected $dependencies = array(
		'Saltwater\Root\Root',
		'MangroveConnector\MangroveConnector'
	);

	protected $contexts = array('MangroveClient');

	protected $services = array(
		'application', 'source', 'stream'
	);
}
