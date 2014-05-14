<?php
namespace MangroveClientJoomla\Provider;

use MangroveClient\Provider\System as McSystem;

class System extends McSystem
{
	public static function getProvider()
	{
		$tech = parent::getProvider();

		$v = new JVersion();

		$tech->cms = array(
			'name' => 'joomla',
			'long' => $v->getShortVersion()
		);

		return $tech;
	}
}
