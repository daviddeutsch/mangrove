<?php
namespace MangroveClient\Provider;

use Saltwater\Server as S;
use Saltwater\Thing\Provider;

class System extends Provider
{
	public static function getProvider()
	{
		$driver = S::$n->db->adapter->getDatabase();

		$dbversion = trim( $driver->getDatabaseVersion() );

		if ( strpos($dbversion, ' ') ) {
			$dbversion = explode(' ', $dbversion);

			foreach ( $dbversion as $particle ) {
				if ( strpos($particle, '.') === false ) continue;

				$dbversion = $particle; break;
			}
		}

		return (object) array(
			'php' => PHP_VERSION,
			'cms' => '',
			'database' => (object) array(
					'name' => $driver->getDatabaseType(),
					'version' => $dbversion
				)
		);
	}
}
