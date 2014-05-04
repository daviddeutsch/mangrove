<?php
namespace MangroveClient\Factory;

use Saltwater\Thing\Factory;

/**
 * Class Uuid
 *
 * Portable UUIDv5-from-URL factory, adopted from:
 *
 * https://github.com/ramsey/uuid/blob/master/src/Uuid.php
 *
 * @package Saltwater\Root\Factory
 */
class Uuid extends Factory
{
	public static function getFactory( $url, $data=null )
	{
		/*
		 * http://tools.ietf.org/html/rfc4122#appendix-C
		 *
		 * 6ba7b811-9dad-11d1-80b4-00c04fd430c8
		 *
		 * converted to decimals so we only do the last step of producing
		 * the namespace characterstring instead of always doing the full
		 * conversion from the UUID namespce above
		 */
		$hexdec = array(
			107, 167, 184, 17, 157, 173, 17, 209,
			128, 180, 0, 192, 79, 212, 48, 200
		);

		$namespace = '';
		foreach ($hexdec as $int) {
			$namespace = chr( $int ) . $namespace;
		}

		$hash = sha1($namespace . $url);

		return self::fromHash($hash);
	}

	private static function fromHash( $hash )
	{
		// Set the version number to 5
		$timeHi = hexdec(substr($hash, 12, 4)) & 0x0fff;
		$timeHi &= ~(0xf000);
		$timeHi |= 5 << 12;

		// Set the variant to RFC 4122
		$clockSeqHi = hexdec(substr($hash, 16, 2)) & 0x3f;
		$clockSeqHi &= ~(0xc0);
		$clockSeqHi |= 0x80;

		return vsprintf(
			'%08s-%04s-%04s-%02s%02s-%012s',
			array(
				substr($hash, 0, 8),
				substr($hash, 8, 4),
				sprintf('%04x', $timeHi),
				sprintf('%02x', $clockSeqHi),
				substr($hash, 18, 2),
				substr($hash, 20, 12),
			)
		);
	}

}
