<?php
require_once 'dbConfig.php';
require_once __DIR__ . '/../config/packages/doctrine.php';

$content = file_get_contents(__DIR__ . '/../composer.json');
$content = json_decode($content,true);
define("APP_VERSION", $content['version']);

if (empty($page)) {
    $page = "";
}
