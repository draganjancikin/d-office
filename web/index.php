<?php
// web/index.php
require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$request = Request::createFromGlobals();
$response = new Response();

$map = [
    '/' => __DIR__.'/../templates/index.php',

    '/clients/' => __DIR__.'/../templates/client/index.php',

    '/pidb/' => __DIR__.'/../templates/pidb/index.php',
    '/pidb/printAccountingDocument' => __DIR__.'/../templates/pidb/printAccountingDocument.php',
    '/pidb/printAccountingDocumentW' => __DIR__.'/../templates/pidb/printAccountingDocumentW.php',
    '/pidb/printAccountingDocumentI' => __DIR__.'/../templates/pidb/printAccountingDocumentI.php',
    '/pidb/printAccountingDocumentIW' => __DIR__.'/../templates/pidb/printAccountingDocumentIW.php',
    '/pidb/printDailyCashReport' => __DIR__.'/../templates/pidb/printDailyCashReport.php',

    '/cutting/' => __DIR__.'/../templates/cutting/index.php',
    '/cutting/printCutting' => __DIR__.'/../templates/cutting/printCutting.php',

    '/materials/' => __DIR__.'/../templates/material/index.php',

    '/orders/' => __DIR__.'/../templates/order/index.php',
    '/orders/printOrder' => __DIR__.'/../templates/order/printOrder.php',
];

$path = $request->getPathInfo();
if (isset($map[$path])) {
    ob_start();
    include $map[$path];
    $response->setContent(ob_get_clean());
} else {
    $response->setStatusCode(404);
    $response->setContent('Not Found');
}

$response->send();

// https://symfony.com/doc/5.4/create_framework/routing.html
