<?php

/**
 * Required configuration for vendor/bin/doctrine
 * and bin/update-db-schema.php
 */

use Brave\ForumAuth\Bootstrap;
use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Doctrine\ORM\Tools\Setup;
use Symfony\Component\Console\Helper\HelperSet;

require __DIR__ . '/../vendor/autoload.php';

define('BRAVE_ROOT_DIR', realpath(__DIR__ . '/../'));
$bootstrap = new Bootstrap();
$bootstrap->enableRoutes(); // reads configuration

$config = Setup::createAnnotationMetadataConfiguration([BRAVE_ROOT_DIR . '/src/Model'], true);
$em = EntityManager::create(['url' => $bootstrap->dbUrl()], $config);

/* @var $helpers HelperSet */
$helpers = new HelperSet(array(
    'db' => new ConnectionHelper($em->getConnection()),
    'em' => new EntityManagerHelper($em)
));
