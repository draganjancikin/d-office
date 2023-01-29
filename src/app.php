<?php
// src/app.php
use Symfony\Component\Routing;

$routes = new Routing\RouteCollection();

$routes->add('index', new Routing\Route('/', [
    '_controller' => function ($request) { return render_template($request); }
]));

$routes->add('client/index', new Routing\Route('/clients/', [
    '_controller' => function ($request) { return render_template($request); }
]));

$routes->add('pidb/index', new Routing\Route('/pidb/', [
    '_controller' => function ($request) { return render_template($request); }
]));
$routes->add('pidb/printAccountingDocument', new Routing\Route('/pidb/printAccountingDocument', [
    '_controller' => function ($request) { return render_template($request); }
]));
$routes->add('pidb/printAccountingDocumentW', new Routing\Route('/pidb/printAccountingDocumentW', [
    '_controller' => function ($request) { return render_template($request); }
]));
$routes->add('pidb/printAccountingDocumentI', new Routing\Route('/pidb/printAccountingDocumentI', [
    '_controller' => function ($request) { return render_template($request); }
]));
$routes->add('pidb/printAccountingDocumentIW', new Routing\Route('/pidb/printAccountingDocumentIW', [
    '_controller' => function ($request) { return render_template($request); }
]));
$routes->add('pidb/printDailyCashReport', new Routing\Route('/pidb/printDailyCashReport', [
    '_controller' => function ($request) { return render_template($request); }
]));

$routes->add('cutting/index', new Routing\Route('/cutting/', [
    '_controller' => function ($request) { return render_template($request); }
]));
$routes->add('cutting/printCutting', new Routing\Route('/cutting/printCutting', [
    '_controller' => function ($request) { return render_template($request); }
]));

$routes->add('material/index', new Routing\Route('/materials/', [
    '_controller' => function ($request) { return render_template($request); }
]));

$routes->add('order/index', new Routing\Route('/orders/', [
    '_controller' => function ($request) { return render_template($request); }
]));
$routes->add('order/printOrder', new Routing\Route('/orders/printOrder', [
    '_controller' => function ($request) { return render_template($request); }
]));

$routes->add('article/index', new Routing\Route('/articles/', [
    '_controller' => function ($request) { return render_template($request); }
]));

$routes->add('project/index', new Routing\Route('/projects/', [
    '_controller' => function ($request) { return render_template($request); }
]));
$routes->add('project/printProjectTask', new Routing\Route('/projects/printProjectTask', [
    '_controller' => function ($request) { return render_template($request); }
]));
$routes->add('project/printInstallationRecord', new Routing\Route('/projects/printInstallationRecord', [
    '_controller' => function ($request) { return render_template($request); }
]));
$routes->add('project/printProjectTaskWithNotes', new Routing\Route('/projects/printProjectTaskWithNotes', [
    '_controller' => function ($request) { return render_template($request); }
]));

$routes->add('admin/index', new Routing\Route('/admin/', [
    '_controller' => function ($request) { return render_template($request); }
]));

//$routes->add('hello', new Routing\Route('/hello/{name}', ['name' => 'World']));

//$routes->add('leap_year', new Routing\Route('/is_leap_year/{year}', [
//  'year' => null,
//  '_controller' => [new LeapYearController(), 'index'],
//]));

return $routes;
