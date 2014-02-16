<?php

class RequireIterator extends ArrayIterator
{
	public function __construct( $requires )
	{
		if ( !is_array($requires) ) $requires = (array) $requires;

		parent::__construct( $this->expand($requires) );
	}

	public function expand( $requires )
	{
		$res = array();
		foreach ( $requires as $require ) {
			if ( !strpos($require, '*') && !in_array($require, $res) ) {
				$res[] = $require;
			}

			$r = MangroveUtils::getPackageInfo($require);

			if ( !is_array($r) ) $r = array($r);

			foreach ( $r as $info ) {
				if ( !strpos($info->name, '*') && !in_array($info->name, $res) ) {
					$res[] = $info->name;
				}

				if ( empty($info->require) ) continue;

				$children = new RequireIterator($info->require);

				foreach ( $children as $child ) {
					if( !in_array($require, $res) ) {
						$res[] = $child;
					}
				}
			}
		}

		return $res;
	}
}
