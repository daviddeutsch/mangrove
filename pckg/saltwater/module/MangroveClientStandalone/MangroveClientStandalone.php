<?php

namespace MangroveClient;

use Saltwater\Thing\Module;

class MangroveClient extends Module
{
	protected $dependencies = array(
		'Saltwater\Root\Root'
	);

	protected $contexts = array('MangroveClient');

}
