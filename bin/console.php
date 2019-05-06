#!/usr/bin/env php
<?php
/**
 * Console app.
 *
 * Note: phpBB needs and creates global variables, so better
 * prefix anything else here with "brave";
 */

error_reporting(E_ALL);
#ini_set('display_errors', '1');

$braveAutoloader = require_once __DIR__.'/../vendor/autoload.php';

define('BRAVE_ROOT_DIR', realpath(__DIR__ . '/../'));
$braveBootstrap = new \Brave\ForumAuth\Bootstrap();

// include phpBB
include BRAVE_ROOT_DIR . '/config/phpbb.inc.php';

// fix autoloader loads classes from phpBB vendor dir instead of this vendor dir
spl_autoload_unregister(array($braveAutoloader, 'loadClass'));
spl_autoload_register(array($braveAutoloader, 'loadClass'), true, true);

// run app
$braveBootstrap->enableCommands()->run();
