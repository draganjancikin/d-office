<?php

require_once __DIR__ . '/../config/bootstrap.php';

use App\Router;

$router = new Router();

// Define routes (could also load routes from a separate file).
$router->add('/', 'HomeController@index');

$router->add('/clients/', 'ClientController@index');
$router->add('/clients/add', 'ClientController@add');
$router->add('/clients/addCountry', 'ClientController@addCountry');
$router->add('/clients/addCity', 'ClientController@addCity');
$router->add('/clients/addStreet', 'ClientController@addStreet');
$router->add('/clients/advancedSearch', 'ClientController@advancedSearch');
$router->add('/client/{client_id}', 'ClientController@view');
$router->add('/client/{client_id}/edit', 'ClientController@edit');
$router->add('/client/{client_id}/edit', 'ClientController@edit');
$router->add('/client/{client_id}/addContact', 'ClientController@view');
$router->add('/client/{client_id}/editContact', 'ClientController@view');
$router->add('/client/{client_id}/contact/{contact_id}/removeContact', 'ClientController@view');

// Get the current request URL.
$requestUri = $_SERVER['REQUEST_URI'];

session_start();
if (isset($_SESSION['username'])):
  // Dispatch the route.
  try {
    $router->dispatch($requestUri);
  }
  catch (Exception $e) {
    // Handle errors (404, 500, etc.).
    http_response_code(500);
    echo "Error: " . $e->getMessage();
  }
else:
  include '../templates/formLogin.php';
endif;
