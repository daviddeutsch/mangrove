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
		$url = $_SERVER['SERVER_NAME'];

		if ( !empty($details->username) ) {
			$server = S::$n->http->get(
				$source->url . '/stream',
				array(
					'Authorization: Basic '
					. $details->username . ':' . $details->password
				)
			);

			return $server;
		} elseif ( !empty($details->passphrase ) ) {

		}
	}

}
