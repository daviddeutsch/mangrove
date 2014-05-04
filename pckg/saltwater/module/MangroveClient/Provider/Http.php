<?php
namespace MangroveClient\Provider;

use Saltwater\Server as S;
use Saltwater\Thing\Provider;

class Http extends Provider
{
	public static function getProvider()
	{
		return new Http();
	}

	public function get( $url, $header=null )
	{
		return $this->connect($url, null, $header);
	}

	public function post( $url, $content, $header=null )
	{
		return $this->connect($url, $content, $header);
	}

	private function connect( $url, $content=null, $header=null )
	{
		if ( !empty($header) ) {
			$header = array_merge(
				array('Content-Type: text/json'),
				$header
			);
		} else {
			$header = array('Content-Type: text/json');
		}

		$curl_calls = array(
			CURLOPT_URL =>            $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER =>     $header,
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
