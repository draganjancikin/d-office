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
