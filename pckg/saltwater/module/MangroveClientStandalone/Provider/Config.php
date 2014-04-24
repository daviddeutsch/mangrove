<?php

namespace MangroveServer\Provider;

use Saltwater\Common\Config as AbstractConfig;

class Config extends AbstractConfig
{
	static $config;

	public static function get()
	{
		if ( empty(self::$config) ) {
			self::$config = json_decode(
				file_get_contents(__DIR__.'/../../../config/config.json')
			);
		}

		return self::$config;
	}
}
