<?php
error_reporting(E_ALL);
#ini_set('display_errors', '1');

require_once __DIR__ . '/../vendor/autoload.php';

define('ROOT_DIR', realpath(__DIR__ . '/../'));

$bootstrap = new \Brave\ForumAuth\Bootstrap();
$app = $bootstrap->enableRoutes();
$app->run();
