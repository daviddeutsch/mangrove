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
			$server = S::$n->http->get($source->url.'/hook/updates');

			return $server;
		}
	}

	public function postAuthenticate( $details )
	{
		if ( !empty($details->username) ) {

		} elseif ( !empty($details->passphrase ) ) {

		}
	}

}
