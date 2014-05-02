<?php
namespace MangroveClient\Provider;

use Saltwater\Server as S;
use Saltwater\Thing\Provider;

class Http extends Provider
{
	public static function get()
	{
		return new Http();
	}

	public function _get( $url )
	{
		return $this->connect($url);
	}

	public function post( $url, $content )
	{
		return $this->connect($url, $content);
	}

	private function connect( $url, $content=null )
	{
		$curl_calls = array(
			CURLOPT_URL =>            $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER =>     array( 'Content-Type: text/json' ),
			CURLOPT_HEADER =>         false
		);

		if ( !empty($content) ) {
			$curl_calls[CURLOPT_POST]       = true;
			$curl_calls[CURLOPT_POSTFIELDS] = $content;
		}

		// Set cURL params
		$ch = curl_init();
		foreach ( $curl_calls as $name => $value ) {
			curl_setopt($ch, $name, $value);
		}

		$return = curl_exec($ch);

		$info = curl_getinfo($ch);

		if ( $return == false ) {
			return $info['http_code'];
		} else {
			return $return;
		}
	}
}
