<?php

require_once 'dbConfig.php';
require_once __DIR__."/../vendor/autoload.php";

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

// Create a simple "default" Doctrine ORM configuration for Annotations.
$paths = [__DIR__."/../src"];
$isDevMode = true;

// Database configuration parameters.
$connectionParams = [
  'dbname' => DB_NAME,
  'user' => DB_USERNAME,
  'password' => DB_PASSWORD,
  'host' => DB_SERVER,
  'driver' => 'mysqli',
  'charset' => 'UTF8',
];

$config = ORMSetup::createAnnotationMetadataConfiguration($paths, $isDevMode);
$entityManager = EntityManager::create($connectionParams, $config);
