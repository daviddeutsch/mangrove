<?php
namespace MangroveClient\Provider;

use Saltwater\Thing\Provider;

class Http extends Provider
{
	private $headers = array(
		'Content-Type: text/json'
	);

	public static function getProvider() { return new Http(); }

	/**
	 * @param string $header
	 */
	public function setHeader( $header )
	{
		$this->headers[] = $header;
	}

	public function get( $url, $header=null )
	{
		return $this->connect($url, null, $header);
	}

	/**
	 * @param string $url
	 */
	public function post( $url, $content, $header=null )
	{
		return $this->connect($url, $content, $header);
	}

	private function connect( $url, $content=null, $header=null )
	{
		$headers = $this->headers;

		if ( !empty($header) ) {
			$headers = array_merge($headers, $header);
		}

		$curl_calls = array(
			CURLOPT_URL =>            $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER =>     $headers,
			CURLOPT_HEADER =>         false
		);

		if ( is_array($content) || is_object($content) ) {
			$content = json_encode($content);
		}

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
