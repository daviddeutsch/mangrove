<?php

// Dont allow direct linking
defined('_JEXEC') or die( 'Direct Access to this location is not allowed.' );

require_once( dirname(__FILE__) . '/autoload.php' );

MangroveApp::add( dirname(__FILE__), 'mangrove', array('todo'), 'MangroveClientApp' );

MangroveApp::start();
