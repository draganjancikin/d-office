<?php
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

require_once __DIR__. "/../vendor/autoload.php";

// Create a simple "default" Doctrine ORM configuration for Annotations
$isDevMode = true;
$proxyDir = null;
$cache = null;
$useSimpleAnnotationReader = false;
$config = Setup::createAnnotationMetadataConfiguration(array(__DIR__."/../src"), $isDevMode, $proxyDir, $cache, $useSimpleAnnotationReader);

// database configuration parameters
$connectionParams = array(
  'dbname' => 'roloffice-2020_dev',
  'user' => DB_USERNAME,
  'password' => DB_PASSWORD,
  'host' => DB_SERVER,
  'driver' => 'mysqli',
);
$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams);

// obtaining the entity manager
$entityManager = EntityManager::create($conn, $config);
