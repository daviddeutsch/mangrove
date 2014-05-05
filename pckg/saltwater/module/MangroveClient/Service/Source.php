<?php
namespace MangroveClient\Service;

use Saltwater\Server as S;
use Saltwater\Root\Service\Rest;

class Source extends Rest
{
	public function getInit()
	{
		$source = $this->context->data;

		if ( strlen($source->token) < 30 ) {
			return '401';
		}
	}

	public function postAuthenticate( $details )
	{
		$db = S::$n->db;

		$source = $this->context->data;

		// TODO: This should also take subdirectories into consideration
		$uuid = S::$n->uuid($_SERVER['SERVER_NAME']);

		if ( !empty($details->username) ) {
			$auth = array(
				'Authorization: Basic '
				. $details->username . ':' . $details->password
			);
		} elseif ( !empty($details->passphrase ) ) {
			$auth = array(
				'Authorization: Basic ' . $details->passphrase
			);
		} else {
			return null;
		}

		$driver = $db->adapter->getDatabase();

		$dbversion = trim( $driver->getDatabaseVersion() );

		if ( strpos($dbversion, ' ') ) {
			$dbversion = explode(' ', $dbversion);

			foreach ( $dbversion as $particle ) {
				if ( strpos($particle, '.') === false ) continue;

				$dbversion = $particle; break;
			}
		}

		$tech = (object) array(
			'php' => PHP_VERSION,
			'cms' => '',
			'database' => (object) array(
				'name' => $driver->getDatabaseType(),
				'version' => $dbversion
			)
		);

		$client = (object) array(
			'uuid' => $uuid,
			'tech' => $tech
		);

		$tech = sha1( json_encode($tech) );

		if ( empty($source->tech) || ($source->tech != $tech) ) {
			S::$n->http->post(
				$source->url . '/client',
				$client,
				$auth
			);

			$source->tech = $tech;
		}

		$source->token = S::$n->http->post(
			$source->url . '/session',
			new \stdClass(),
			$auth
		);

		if ( strlen($source->token) > 30 ) {
			$source->status = 'connected';
		}

		$db->_($source);

		return $source->status;
	}

}
