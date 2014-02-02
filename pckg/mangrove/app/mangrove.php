<?php

// Dont allow direct linking
defined('_JEXEC') or die( 'Direct Access to this location is not allowed.' );

require_once( dirname(__FILE__) . '/autoload.php' );

MangroveTodoApp::init( dirname(__FILE__), 'mangrove', array('todo') );

MangroveTodoApp::start();
