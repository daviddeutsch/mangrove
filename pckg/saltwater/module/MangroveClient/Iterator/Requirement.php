<?php

namespace Hotbox\Iterator;

/**
 * Iterator that resolves recursive dependencies into a flat list of packages
 *
 * @package Hotbox\Boxer
 */
class Requirement extends \ArrayIterator
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
			$res = $this->resolveRequirement($res, $require);
		}

		return $res;
	}

	private function resolveRequirement( $res, $require )
	{
		$this->addName($require, $res);

		$res = $this->resolvePackage($res, $require);

		return $res;
	}

	private function resolvePackage( $res, $name )
	{
		$r = Hb::getPackageInfo($name);

		if ( !is_array($r) ) $r = array($r);

		foreach ( $r as $info ) {
			$this->addName($info->name, $res);

			$res = $this->resolveChildRequirements($res, $info);
		}

		return $res;
	}

	private function addName( $name, &$res )
	{
		if ( !strpos($name, '*') && !in_array($name, $res) ) {
			$res[] = $name;
		}
	}

	private function resolveChildRequirements( $res, $info )
	{
		if ( empty($info->require) ) return $res;

		$children = new Requirement($info->require);

		foreach ( $children as $child ) {
			if ( !in_array($child, $res) ) {
				$res[] = $child;
			}
		}

		return $res;
	}

}
