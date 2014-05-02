<?php

namespace MangroveClient\Context;

use Saltwater\Thing\Context;

class MangroveClient extends Context
{
	public $namespace = 'MangroveClient';

	public $services = array(
		'app', 'source', 'stream'
	);
}
