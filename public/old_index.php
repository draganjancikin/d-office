<?php

require_once __DIR__ . '/../config/appConfig.php';
use App\Core\Router;

$router = new Router();

// ========== Orders routes ====================================================
$router->add('GET', '/orders/search/', 'OrderController@search');
$router->add('GET', '/order/{order_id}/delete', 'OrderController@orderDelete');

// ========== Articles routes ==================================================
$router->add('GET', '/articles/', 'ArticleController@index');
$router->add('GET', '/articles/new', 'ArticleController@articleNewForm');
$router->add('POST', '/articles/add', 'ArticleController@articleAdd');
$router->add('GET', '/articles/price-list', 'ArticleController@priceList');
$router->add('GET', '/articles/search/', 'ArticleController@search');

$router->add('GET', '/articles/groups', 'ArticleController@groups');
$router->add('GET', '/articles/groups/new', 'ArticleController@groupNewForm');
$router->add('POST', '/articles/groups/add', 'ArticleController@groupAdd');
$router->add('GET', '/articles/group/{group_id}', 'ArticleController@groupView');
$router->add('GET', '/articles/group/{group_id}/edit', 'ArticleController@groupEditForm');
$router->add('POST', '/articles/group/{group_id}/edit', 'ArticleController@groupEdit');

$router->add('GET', '/article/{article_id}', 'ArticleController@articleViewForm');
$router->add('GET', '/article/{article_id}/edit', 'ArticleController@articleEditForm');
$router->add('POST', '/article/{article_id}/edit', 'ArticleController@articleEdit');

$router->add('POST', '/article/{article_id}/addProperty', 'ArticleController@addProperty');
$router->add('GET', '/article/{article_id}/property/{property_id}/delete', 'ArticleController@deleteProperty');

// ========== Admins routes ====================================================
$router->add('GET', '/admin/', 'AdminController@index');
$router->add('GET', '/admin/base-backup', 'AdminController@baseBackup');
$router->add('GET', '/admin/company-info', 'AdminController@companyInfoViewForm');
$router->add('GET', '/admin/company-info/edit', 'AdminController@companyInfoEditForm');
$router->add('POST', '/admin/company-info/edit', 'AdminController@companyInfoEdit');
$router->add('GET', '/admin/employees', 'AdminController@employeesList');
$router->add('GET', '/admin/employees/search/', 'AdminController@employeesSearch');
$router->add('GET', '/admin/employee/{employee_id}', 'AdminController@employeeViewForm');
$router->add('GET', '/admin/employee/{employee_id}/edit', 'AdminController@employeeEditForm');
$router->add('POST', '/admin/employee/{employee_id}/edit', 'AdminController@employeeEdit');


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
