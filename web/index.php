<?php
/**
 * Web index.
 *
 * phpBB uses global variables, so everything that is not for phpBB in here
 * is prefixed with "brave".
 */

error_reporting(E_ALL);
#ini_set('display_errors', '1');

require_once __DIR__ . '/../vendor/autoload.php';

define('BRAVE_ROOT_DIR', realpath(__DIR__ . '/../'));

$braveBootstrap = new \Brave\ForumAuth\Bootstrap();
$braveApp = $braveBootstrap->enableRoutes();

// include necessary phpBB functions - tested with phpBB 3.1.x
define('IN_PHPBB', true);
$phpbb_root_path = rtrim($braveBootstrap->phpBBDir(), '/') . '/';
$phpEx = "php";
require_once $phpbb_root_path . 'common.'.$phpEx;
require_once $phpbb_root_path . 'includes/functions_user.'.$phpEx;

// phpBB overwrites superglobals, but the Slim-Framework needs them.
/* @var $request phpbb\request\request */
$request->enable_super_globals();

// run app
$braveApp->run();
