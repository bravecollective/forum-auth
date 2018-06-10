<?php
/**
 * This in included in web/index.php and bin/console.php
 *
 * @var $braveBootstrap \Brave\ForumAuth\Bootstrap
 */

// include necessary phpBB functions - tested with phpBB 3.1.x
define('IN_PHPBB', true);
$phpbb_root_path = rtrim($braveBootstrap->phpBBDir(), '/') . '/';
$phpEx = "php";
require_once $phpbb_root_path . 'common.'.$phpEx;
require_once $phpbb_root_path . 'includes/functions_user.'.$phpEx;

// phpBB overwrites superglobals, but the Slim-Framework needs them.
/* @var $request phpbb\request\request */
$request->enable_super_globals();
