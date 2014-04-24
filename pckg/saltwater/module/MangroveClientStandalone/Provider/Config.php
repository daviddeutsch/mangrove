<?php

namespace MangroveClientStandalone\Provider;

use Saltwater\Common\Config as AbstractConfig;
use \MangroveClientConfig as ClientConfig;

class Config extends AbstractConfig
{
	static $config;

	public static function get()
	{
		if ( empty(self::$config) ) {
			self::$config = (object) array(
				'type'     => ClientConfig::$db_type,
				'host'     => ClientConfig::$db_host,
				'name'     => ClientConfig::$db_name,
				'user'     => ClientConfig::$db_user,
				'password' => ClientConfig::$db_password,
				'prefix'   => ClientConfig::$db_prefix
			);
		}

		return self::$config;
	}
}
