<?php

require_once(__DIR__ . '/autoload.php');

use Saltwater\Server as S;

S::init( array('MangroveClientStandalone\MangroveClientStandalone') );

S::$n->route->go();
