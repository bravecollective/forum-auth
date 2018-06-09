#!/usr/bin/env php
<?php
use Brave\ForumAuth\Model\Character;
use Brave\ForumAuth\Model\Group;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;

require_once __DIR__.'/../vendor/autoload.php';

/* @var $em EntityManager */
include __DIR__.'/../config/cli-config.php';

$classes = [
    $em->getClassMetadata(Character::class),
    $em->getClassMetadata(Group::class)
];

$tool = new SchemaTool($em);
$tool->updateSchema($classes);

echo "Schema updated.\n";
