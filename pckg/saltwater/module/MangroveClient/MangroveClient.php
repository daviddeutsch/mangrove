<?php

namespace MangroveClient;

use Saltwater\Thing\Module;

class MangroveClient extends Module
{
	protected $require = array(
		'module' => array('Saltwater\Root\Root')
	);

	protected $provide = array(
		'provider' => array('http', 'uuid'),
		'context' => array('MangroveClient'),
		'service' => array('app', 'source', 'stream')
	);

}
