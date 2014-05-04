<?php
namespace MangroveClient\Service;

use Saltwater\Server as S;
use Saltwater\Root\Service\Rest;

class Source extends Rest
{
	public function getInit()
	{
		$source = $this->context->data;



		if ( empty($source->token) ) {
			$server = S::$n->http->get($source->url . '/hook/updates');

			return $server;
		}
	}

	public function postAuthenticate( $details )
	{
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

		S::$n->http->post(
			$source->url . '/client',
			$auth
		);

		$source->token = S::$n->http->get(
			$source->url . '/session',
			$auth
		);

		if ( $source->token ) {

		}

		S::$n->db->_($source);
	}

}
