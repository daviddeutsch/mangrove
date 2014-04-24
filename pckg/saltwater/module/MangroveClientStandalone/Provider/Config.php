<?php

namespace MangroveClientStandalone\Provider;

use Saltwater\Common\Config as AbstractConfig;

class Config extends AbstractConfig
{
	static $config;

	public static function get()
	{
		if ( empty(self::$config) ) {
			self::$config = (object) array(
				'type'     => MangroveClientConfig::$db_type,
				'host'     => MangroveClientConfig::$db_host,
				'name'     => MangroveClientConfig::$db_name,
				'user'     => MangroveClientConfig::$db_user,
				'password' => MangroveClientConfig::$db_password,
				'prefix'   => MangroveClientConfig::$db_prefix
			);
		}

		return self::$config;
	}
}
