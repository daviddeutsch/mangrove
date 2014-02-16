<?php

class RequireIterator extends ArrayIterator
{
	public function __construct( $requires )
	{
		if ( is_array($requires) ) {
			parent::__construct($requires);
		} else {
			$r = array();

			foreach ( (array) $requires as $k => $v ) {
				$r[] = $k;
			}
		}

		$this->expand();
	}

	public function expand()
	{
		foreach ( $this as $require ) {
			$r = MangroveUtils::getPackageInfo($require);

			if ( !is_array($r) ) $r = array($r);

			foreach ( $r as $info ) {
				if ( empty($info->require) ) continue;

				$this->append( new RequireIterator($info->require) );
			}
		}
	}

	public function append( $array )
	{
		foreach ( $array as $entry ) {
			if ( array_search($entry, (array) $this) ) continue;

			$this[] = $entry;
		}
	}
}
