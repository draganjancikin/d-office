<?php

require_once __DIR__ . '/../config/bootstrap.php';

use App\Router;

$router = new Router();

// Define routes (could also load routes from a separate file).
$router->add('GET', '/', 'HomeController@index');
$router->add('GET', '/login', 'HomeController@login');
$router->add('POST', '/login', 'HomeController@loginPost');
$router->add('GET', '/logout', 'HomeController@logout');

$router->add('GET', '/clients/', 'ClientController@index');
$router->add('GET', '/clients/add', 'ClientController@add');
$router->add('POST', '/clients/add', 'ClientController@add');
$router->add('GET', '/clients/addCountry', 'ClientController@addCountry');
$router->add('POST', '/clients/addCountry', 'ClientController@addCountry');
$router->add('GET', '/clients/addCity', 'ClientController@addCity');
$router->add('POST', '/clients/addCity', 'ClientController@addCity');
$router->add('GET', '/clients/addStreet', 'ClientController@addStreet');
$router->add('POST', '/clients/addStreet', 'ClientController@addStreet');
$router->add('GET', '/clients/advancedSearch', 'ClientController@advancedSearch');
$router->add('POST', '/clients/advancedSearch', 'ClientController@advancedSearch');
$router->add('GET', '/client/{client_id}', 'ClientController@view');
$router->add('GET', '/client/{client_id}/edit', 'ClientController@edit');
$router->add('POST', '/client/{client_id}/edit', 'ClientController@edit');
$router->add('GET', '/client/{client_id}/addContact', 'ClientController@view');
$router->add('POST', '/client/{client_id}/addContact', 'ClientController@view');
$router->add('GET', '/client/{client_id}/editContact', 'ClientController@view');
$router->add('POST', '/client/{client_id}/editContact', 'ClientController@view');
$router->add('GET', '/client/{client_id}/contact/{contact_id}/removeContact', 'ClientController@view');

$router->add('GET', '/pidbs/', 'PidbController@index');
$router->add('GET', '/pidbs/add', 'PidbController@add');
$router->add('POST', '/pidbs/add', 'PidbController@add');
$router->add('GET', '/pidb/{pidb_id}', 'PidbController@view');
$router->add('GET', '/pidb/{pidb_id}/print', 'PidbController@printAccountingDocument');
$router->add('GET', '/pidb/{pidb_id}/printW', 'PidbController@printAccountingDocumentW');
$router->add('GET', '/pidb/{pidb_id}/printI', 'PidbController@printAccountingDocumentI');
$router->add('GET', '/pidb/{pidb_id}/printIW', 'PidbController@printAccountingDocumentIW');
$router->add('GET', '/pidb/{pidb_id}/edit', 'PidbController@edit');
$router->add('POST', '/pidb/{pidb_id}/edit', 'PidbController@edit');
$router->add('POST', '/pidb/{pidb_id}/addArticle', 'PidbController@view');
$router->add('GET', '/pidb/{pidb_id}/exportProformaToDispatch', 'PidbController@exportProformaToDispatch');
$router->add('POST', '/pidb/{pidb_id}/addPayment', 'PidbController@view');
$router->add('POST', '/pidb/{pidb_id}/article/{pidb_article_id}/edit', 'PidbController@editArticleInAccountingDocument');
$router->add('GET', '/pidb/{pidb_id}/transactions', 'PidbController@transactions');
$router->add('GET', '/pidb/{pidb_id}/transaction/{transaction_id}/edit', 'PidbController@formEditTransaction');
$router->add('POST', '/pidb/{pidb_id}/transaction/{transaction_id}/edit', 'PidbController@editTransaction');
$router->add('GET', '/pidb/{pidb_id}/transaction/{transaction_id}/delete', 'PidbController@deleteTransaction');

// ========== Cuttings routes ==================================================
$router->add('GET', '/cuttings/', 'CuttingController@index');
$router->add('GET', '/cuttings/add', 'CuttingController@formAdd');
$router->add('POST', '/cuttings/add', 'CuttingController@add');
$router->add('GET', '/cutting/{cutting_id}', 'CuttingController@view');
$router->add('GET', '/cutting/{cutting_id}/edit', 'CuttingController@edit');
$router->add('GET', '/cutting/{cutting_id}/print', 'CuttingController@print');
$router->add('POST', '/cutting/{cutting_id}/addArticle', 'CuttingController@addArticle');
$router->add('POST', '/cutting/{cutting_id}/article/{article_id}/edit', 'CuttingController@editArticle');
$router->add('GET', '/cutting/{cutting_id}/article/{article_id}/delete', 'CuttingController@deleteArticle');
$router->add('GET', '/cutting/{cutting_id}/exportToAccountingDocument', 'CuttingController@exportToAccountingDocument');
$router->add('GET', '/cutting/{cutting_id}/delete', 'CuttingController@delete');

// ========== Material routes ==================================================
$router->add('GET', '/materials/', 'MaterialController@index');
$router->add('GET', '/materials/add', 'MaterialController@addForm');
$router->add('POST', '/materials/add', 'MaterialController@add');

$router->add('GET', '/material/{material_id}', 'MaterialController@view');
$router->add('GET', '/material/{material_id}/edit', 'MaterialController@editForm');
$router->add('POST', '/material/{material_id}/edit', 'MaterialController@edit');

$router->add('POST', '/material/{material_id}/addSupplier', 'MaterialController@addSupplier');
$router->add('POST', '/material/{material_id}/supplier/{supplier_id}/edit', 'MaterialController@editSupplier');
$router->add('GET', '/material/{material_id}/supplier/{supplier_id}/delete', 'MaterialController@deleteSupplier');

$router->add('POST', '/material/{material_id}/addProperty', 'MaterialController@addProperty');
$router->add('GET', '/material/{material_id}/property/{property_id}/delete', 'MaterialController@deleteProperty');

// Get the current request URL.
$requestUri = $_SERVER['REQUEST_URI'];

session_start();
// get the current method.
$httpMethod = $_SERVER['REQUEST_METHOD'];

// Dispatch the route.
try {
  $router->dispatch($requestUri, $httpMethod);
}
catch (Exception $e) {
  // Handle errors (404, 500, etc.).
  http_response_code(500);
  echo "Error: " . $e->getMessage();
}
