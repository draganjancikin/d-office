<?php

/**
 * Bootstrap file for setting up Doctrine ORM with MySQL.
 */

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

require_once __DIR__ . "/../../vendor/autoload.php";

// Create a simple "default" Doctrine ORM configuration for Annotations.
$paths = [__DIR__ . "/../src"];
$isDevMode = true;

$config = ORMSetup::createAttributeMetadataConfiguration($paths, $isDevMode);

// Database configuration parameters.
$connectionParams = [
  'dbname' => 'default',
  'user' => 'user',
  'password' => 'user',
  'host' => 'db',
  'driver' => 'mysqli',
  'charset' => 'UTF8',
];

$connection = DriverManager::getConnection($connectionParams, $config);

$entityManager = new EntityManager($connection, $config);

return $entityManager;
