<?php
require_once 'dbConfig.php';
require_once __DIR__."/../vendor/autoload.php";
//
//use Doctrine\ORM\Tools\Setup;
//use Doctrine\ORM\EntityManager;
//
//// Create a simple "default" Doctrine ORM configuration for Annotations.
//$isDevMode = true;
//$proxyDir = null;
//$cache = null;
//$useSimpleAnnotationReader = false;
//$config = Setup::createAnnotationMetadataConfiguration(array(__DIR__."/../src"), $isDevMode, $proxyDir, $cache, $useSimpleAnnotationReader);
//
//// Database configuration parameters.
//$connectionParams = array(
//  'dbname' => DB_NAME,
//  'user' => DB_USERNAME,
//  'password' => DB_PASSWORD,
//  'host' => DB_SERVER,
//  'driver' => 'mysqli',
//);
//$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams);
//
//// Obtaining the entity manager.
//$entityManager = EntityManager::create($conn, $config);

// =====================================================================================================================

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

$paths = [__DIR__."/../src"];
$isDevMode = false;
$proxyDir = null;
$cache = null;

// the connection configuration
$dbParams = array(
    'host' => DB_SERVER,
    'driver'   => 'pdo_mysql',
    'user'     => DB_USERNAME,
    'password' => DB_PASSWORD,
    'dbname'   => DB_NAME,
);

$config = ORMSetup::createAnnotationMetadataConfiguration($paths, $isDevMode, $proxyDir, $cache);
$entityManager = EntityManager::create($dbParams, $config);