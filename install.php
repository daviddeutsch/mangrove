<?php

if ( version_compare(PHP_VERSION, '5.2.0') <= 0 ) {
	exit;
}

// Check whether we already have mangrove installed

// If yes, hand bootstrap json over to mangrove

// Otherwise install mangrove first

// Temporarily install composer to get mangrove
error_reporting(E_ALL);

$bootstrap = __DIR__.'/bootstrap';

if ( !file_exists( $bootstrap.'/composer.php' ) ) {
	$installer = file_get_contents('https://getcomposer.org/installer');

	// Make it possible to include the installer instead of using the command line only
	$installer = str_replace(array("#!/usr/bin/env php","process(\$argv);"), '', $installer);

	file_put_contents($bootstrap.'/composer.php', $installer);
}

if ( !file_exists( $bootstrap.'/composer.phar' ) ) {
	require $bootstrap.'/composer.php';

	// Custom install instead of using the built in process()
	if ( checkPlatform(true) ) {
		installComposer($bootstrap, true);
	}
}

if ( !is_dir( $bootstrap.'/composer' ) ) {
	$composer = new Phar($bootstrap.'/composer.phar');
	$composer->extractTo($bootstrap.'/composer');
}

require $bootstrap.'/composer/src/bootstrap.php';
require $bootstrap.'/composer/vendor/autoload.php';

use Composer\Command\InstallCommand;
use Symfony\Console\Input;
use Symfony\Console\Output;

$input = new Symfony\Component\Console\Input\StringInput( "install" );
$output = new Symfony\Component\Console\Output\NullOutput();

$install = new InstallCommand( "install" );

$install->run($input, $output);

print_r($install);

print_r("test");
