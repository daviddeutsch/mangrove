<?php

class MangroveUtils
{
	public static function getJSON( $path )
	{
		return json_decode( file_get_contents($path) );
	}

	public static function putJSON( $path, $data )
	{
		if ( version_compare(phpversion(), '5.4.0', '>') ) {
			file_put_contents(
				$path,
				json_encode(
					$data,
					JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
				)
			);
		} else {
			file_put_contents( $path, self::prettyJSON(json_encode($data)) );
		}
	}

	/**
	 * Adapted from http://stackoverflow.com/a/9776726
	 *
	 * @param $json
	 *
	 * @return string
	 */
	public static function prettyJSON( $json )
	{
		$result = '';

		$previous    = '';
		$in_quotes   = false;
		$last_indent = null;
		$length      = strlen($json);

		$level = 0;
		for ( $i=0; $i<$length; $i++ ) {
			$current = $json[$i];

			$indent_level = null;

			$post = "";

			if ( $last_indent !== null ) {
				$indent_level = $last_indent;
				$last_indent = null;
			}

			if ( $current === '"' && $previous != '\\' ) {
				$in_quotes = !$in_quotes;
			} else if ( !$in_quotes ) {
				switch ( $current ) {
					case '}': case ']':
					$level--;

					$last_indent = null;
					$indent_level = $level;
					break;
					case '{': case '[':
					$level++;
					case ',':
						$last_indent = $level;
						break;
					case ':':
						$post = " ";
						break;
					case " ": case "\t": case "\n": case "\r":
					$current = "";

					$last_indent = $indent_level;
					$indent_level  = null;
					break;
				}
			}

			if( $indent_level !== null ) {
				$result .= "\n".str_repeat( "\t", $indent_level );
			}

			$result .= $current.$post;

			$previous = $current;
		}

		return $result;
	}

	public static function rrmdir( $path )
	{
		if ( is_dir($path) ) {
			foreach ( glob($path . '/*') as $item ) {
				if ( is_dir($item) ) {
					self::rrmdir($item);
				} else {
					unlink($item);
				}
			}

			rmdir($path);
		}
	}

	public static function explodeVersion( $version )
	{
		$return = array();

		$v = explode('.', $version, 3);

		$return->major = $v[0];
		$return->minor = $v[1];

		if ( is_numeric($v[2]) ) {
			$return->patch = $v[2];
			$return->meta = null;
		} else {
			preg_match('/\d+/', $v[2], $regs);

			$return->patch = $regs[0];

			$return->meta = substr( $v[2], strlen($regs[0]) );
		}

		return $return;
	}

}
