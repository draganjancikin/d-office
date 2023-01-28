<?php
// src/app.php
use Symfony\Component\Routing;

$routes = new Routing\RouteCollection();

$routes->add('index', new Routing\Route('/'));

$routes->add('client/index', new Routing\Route('/clients/'));

$routes->add('pidb/index', new Routing\Route('/pidb/'));
$routes->add('pidb/printAccountingDocument', new Routing\Route('/pidb/printAccountingDocument'));
$routes->add('pidb/printAccountingDocumentW', new Routing\Route('/pidb/printAccountingDocumentW'));
$routes->add('pidb/printAccountingDocumentI', new Routing\Route('/pidb/printAccountingDocumentI'));
$routes->add('pidb/printAccountingDocumentIW', new Routing\Route('/pidb/printAccountingDocumentIW'));
$routes->add('pidb/printDailyCashReport', new Routing\Route('/pidb/printDailyCashReport'));

$routes->add('cutting/index', new Routing\Route('/cutting/'));
$routes->add('cutting/printCutting', new Routing\Route('/cutting/printCutting'));

$routes->add('material/index', new Routing\Route('/materials/'));

$routes->add('order/index', new Routing\Route('/orders/'));
$routes->add('order/printOrder', new Routing\Route('/orders/printOrder'));

$routes->add('article/index', new Routing\Route('/articles/'));

$routes->add('project/index', new Routing\Route('/projects/'));
$routes->add('project/printProjectTask', new Routing\Route('/projects/printProjectTask'));
$routes->add('project/printInstallationRecord', new Routing\Route('/projects/printInstallationRecord'));
$routes->add('project/printProjectTaskWithNotes', new Routing\Route('/projects/printProjectTaskWithNotes'));

$routes->add('admin/index', new Routing\Route('/admin/'));

//$routes->add('hello', new Routing\Route('/hello/{name}', ['name' => 'World']));

return $routes;
