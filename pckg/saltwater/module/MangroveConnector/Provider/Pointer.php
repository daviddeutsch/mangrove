<?php

namespace MangroveConnector\Provider;

use Saltwater\Thing\Provider;

class Pointer extends Provider
{
	private static $pointer;

	public static function getProvider()
	{
		return new Pointer();
	}

	public function setPointer( $id )
	{
		self::$pointer = $id;
	}

	public function getPointer()
	{
		return self::$pointer;
	}
}
