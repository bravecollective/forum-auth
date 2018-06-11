<?php
/**
 * Web app.
 *
 * Note: phpBB needs and creates global variables, so better
 * prefix anything else here with "barve";
 */

error_reporting(E_ALL);
ini_set('display_errors', '0');

require_once __DIR__.'/../vendor/autoload.php';

define('BRAVE_ROOT_DIR', realpath(__DIR__ . '/../'));
$braveBootstrap = new \Brave\ForumAuth\Bootstrap();

// include phpBB
include BRAVE_ROOT_DIR . '/config/phpbb.inc.php';

// run app
$braveBootstrap->enableRoutes()->run();
