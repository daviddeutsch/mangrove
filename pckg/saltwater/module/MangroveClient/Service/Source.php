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
			return S::$n->http->get($source->url . '/app');
		}
	}

	/**
	 * @param $data
	 * @param \Saltwater\Root\Provider\Db   $db
	 * @param \MangroveClient\Provider\Http $http
	 * @param \MangroveClient\Provider\Uuid $uuid
	 *
	 * @return null|string
	 */
	public function postAuthenticate( $data, $db, $http, $uuid )
	{
		$source = $this->context->data;

		$auth =  'Authorization: Basic ';
		if ( !empty($data->username) ) {
			$auth .= $data->username . ':' . $data->password;
		} elseif ( !empty($data->passphrase ) ) {
			$auth .= $data->passphrase;
		} else {
			return null;
		}

		$http->setHeader($auth);



		if (
			empty($source->tech)
			|| ( $source->tech != sha1(json_encode($tech)) )
		) {
			$http->post(
				$source->url . '/client',
				(object) array( 'uuid' => (string) $uuid, 'tech' => $tech )
			);

			$source->tech = $tech;
		}

		$source->token = $http->post(
			$source->url . '/session',
			new \stdClass()
		);

		if ( strlen($source->token) > 30 ) {
			$source->status = 'connected';
		} else {
			$source->token = '';
		}

		$db->_($source);

		return $source->status;
	}

}
