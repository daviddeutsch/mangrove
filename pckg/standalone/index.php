<?php

error_reporting(E_ALL);

require_once(__DIR__ . '/config.php');

require_once(__DIR__ . '/autoload.php');

use Saltwater\Server as S;

S::init( array('MangroveClientStandalone\MangroveClientStandalone') );

S::$n->route->go();
