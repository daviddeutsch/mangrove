<?php

namespace MangroveConnector\Provider;

use Saltwater\Server as S;
use Saltwater\Thing\Provider;

class Db extends Provider
{
	/**
	 * @var \RedBean_Instance
	 */
	private static $db;

	private static $pointer;

	public static function get()
	{
		if ( empty(self::$db) ) {
			self::$db = clone S::$n->provider('db');

			self::$db->prefix(
				self::$db->writer->getPrefix() . '_connector'
			);
		}

		$pointer = S::$n->pointer->getPointer();

		// Hot-swap pointers
		if ( self::$pointer != $pointer ) {
			if ( empty(self::$pointer) ) {
				self::$db->prefix(
					self::$db->writer->getPrefix() . '_' . $pointer
				);
			} else {
				self::$db->prefix(
					str_replace(
						'_' . self::$pointer,
						'_' . $pointer,
						self::$db->writer->getPrefix()
					)
				);
			}

			self::$pointer = $pointer;
		}

		return self::$db;
	}
}
