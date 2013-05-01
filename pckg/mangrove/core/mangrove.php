<?php

$mangrove = new Mangrove();
$mangrove->start();

if ( !empty( $_GET['task'] ) ) {error_log("trying to get task ".$_GET['task']);
	$mangrove->resolve($_GET['task']);
}

class Mangrove
{
	function start()
	{
		$this->update();
	}

	function update()
	{

	}

	function resolve()
	{
		$method = 'get'.ucfirst($task);

		if ( method_exists( $this, $method ) ) {
			return $this->$method();
		}
	}

	function getPackages()
	{
		echo '[{"repo1":{"test1":"test"}},{"repo2":{"test2":"test"}}]';
	}

	function getPackage()
	{
		echo '[{"repo1":{"test1":"test"}},{"repo2":{"test2":"test"}}]';
	}

	function staticGet( $package )
	{

	}
}