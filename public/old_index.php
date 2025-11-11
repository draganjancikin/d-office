<?php

require_once __DIR__ . '/../config/appConfig.php';
use App\Core\Router;

$router = new Router();

// ========== Projects routes ====================================================
$router->add('GET', '/projects/', 'ProjectController@index');
$router->add('GET', '/projects/by-city', 'ProjectController@projectByCityView');
$router->add('GET', '/projects/new', 'ProjectController@projectNewForm');
$router->add('POST', '/projects/add', 'ProjectController@add');

$router->add('GET', '/project/{project_id}', 'ProjectController@projectViewForm');
$router->add('GET', '/project/{project_id}/edit', 'ProjectController@projectEditForm');
$router->add('POST', '/project/{project_id}/edit', 'ProjectController@projectEdit');
$router->add('GET', '/project/{project_id}/printProjectTaskWithNotes', 'ProjectController@printProjectTaskWithNotes');
$router->add('GET', '/project/{project_id}/printProjectTask', 'ProjectController@printProjectTask');
$router->add('GET', '/project/{project_id}/printInstallationRecord', 'ProjectController@printInstallationRecord');
$router->add('POST', '/project/{project_id}/addFile', 'ProjectController@addFileToProject');

$router->add('POST', '/project/{project_id}/task/add', 'ProjectController@taskAdd');
$router->add('GET', '/project/{project_id}/task/{task_id}/edit', 'ProjectController@taskEditForm');
$router->add('POST', '/project/{project_id}/task/{task_id}/edit', 'ProjectController@taskEdit');
$router->add('GET', '/project/{project_id}/task/{task_id}/delete', 'ProjectController@taskDelete');

$router->add('POST', '/project/{project_id}/note/add', 'ProjectController@addNote');
$router->add('GET', '/project/{project_id}/note/{note_id}/delete', 'ProjectController@deleteNote');

$router->add('GET', '/project/{project_id}/task/{task_id}/setStartDate', 'ProjectController@setStartDate');
$router->add('GET', '/project/{project_id}/task/{task_id}/setEndDate', 'ProjectController@setEndDate');
$router->add('POST', '/project/{project_id}/task/{task_id}/addNote', 'ProjectController@addTaskNote');
$router->add('GET', '/project/{project_id}/task/{task_id}/note/{note_id}/delete', 'ProjectController@deleteTaskNote');

$router->add('GET', '/projects/search/', 'ProjectController@search');
$router->add('GET', '/projects/advancedSearch', 'ProjectController@advancedSearch');
$router->add('POST', '/projects/advancedSearch', 'ProjectController@advancedSearch');

// Add exception folders
$router->addExceptionFolder('upload'); // Exclude the "static" folder

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
