<?php

require_once __DIR__ . '/../config/appConfig.php';
use App\Core\Router;

$router = new Router();

// Define routes (could also load routes from a separate file).
$router->add('GET', '/', 'HomeController@index');
$router->add('GET', '/login', 'HomeController@loginForm');
$router->add('POST', '/login', 'HomeController@login');
$router->add('GET', '/logout', 'HomeController@logout');

// ========== Clients routes ===================================================

$router->add('GET', '/clients/', 'ClientController@index');
$router->add('GET', '/clients/add', 'ClientController@addClientForm');
$router->add('POST', '/clients/add', 'ClientController@addClient');
$router->add('GET', '/client/{client_id}', 'ClientController@view');
$router->add('GET', '/client/{client_id}/edit', 'ClientController@editClientForm');
$router->add('POST', '/client/{client_id}/edit', 'ClientController@editClient');

$router->add('GET', '/clients/addCountry', 'ClientController@addCountryForm');
$router->add('POST', '/clients/addCountry', 'ClientController@addCountry');
$router->add('GET', '/clients/addCity', 'ClientController@addCityForm');
$router->add('POST', '/clients/addCity', 'ClientController@addCity');
$router->add('GET', '/clients/addStreet', 'ClientController@addStreetForm');
$router->add('POST', '/clients/addStreet', 'ClientController@addStreet');
$router->add('GET', '/clients/search/', 'ClientController@search');
$router->add('GET', '/clients/advancedSearch', 'ClientController@advancedSearch');
$router->add('POST', '/clients/advancedSearch', 'ClientController@advancedSearch');

$router->add('GET', '/client/{client_id}/addContact', 'ClientController@view');
$router->add('POST', '/client/{client_id}/addContact', 'ClientController@addContact');

$router->add('GET', '/client/{client_id}/editContact', 'ClientController@view');
$router->add('POST', '/client/{client_id}/contact/{contact_id}/editContact', 'ClientController@editContact');
$router->add('GET', '/client/{client_id}/contact/{contact_id}/removeContact', 'ClientController@removeContact');

// ========== Documents routes =================================================

$router->add('GET', '/pidbs/', 'PidbController@index');
$router->add('GET', '/pidbs/preferences', 'PidbController@editPreferencesForm');
$router->add('POST', '/pidbs/preferences', 'PidbController@editPreferences');
$router->add('GET', '/pidbs/add', 'PidbController@addForm');
$router->add('POST', '/pidbs/add', 'PidbController@add');
$router->add('POST', '/pidbs/cacheInOut', 'PidbController@cacheInOut');
$router->add('GET', '/pidbs/search/', 'PidbController@search');
$router->add('GET', '/pidbs/transactions', 'PidbController@transactions');
$router->add('GET', '/pidbs/cashRegister', 'PidbController@cashRegister');
$router->add('GET', '/pidbs/printDailyCacheReport', 'PidbController@printDailyCacheReport');

$router->add('GET', '/pidb/{pidb_id}', 'PidbController@view');
$router->add('GET', '/pidb/{pidb_id}/edit', 'PidbController@editForm');
$router->add('POST', '/pidb/{pidb_id}/edit', 'PidbController@edit');
$router->add('GET', '/pidb/{pidb_id}/delete', 'PidbController@delete');

$router->add('POST', '/pidb/{pidb_id}/addArticle', 'PidbController@addArticle');

$router->add('GET', '/pidb/{pidb_id}/print', 'PidbController@printAccountingDocument');
$router->add('GET', '/pidb/{pidb_id}/printW', 'PidbController@printAccountingDocumentW');
$router->add('GET', '/pidb/{pidb_id}/printI', 'PidbController@printAccountingDocumentI');
$router->add('GET', '/pidb/{pidb_id}/printIW', 'PidbController@printAccountingDocumentIW');
$router->add('GET', '/pidb/{pidb_id}/exportProformaToDispatch', 'PidbController@exportProformaToDispatch');
$router->add('POST', '/pidb/{pidb_id}/addPayment', 'PidbController@addPayment');
$router->add('POST', '/pidb/{pidb_id}/article/{pidb_article_id}/edit', 'PidbController@editArticleInAccountingDocument');
$router->add('GET', '/pidb/{pidb_id}/article/{pidb_article_id}/change', 'PidbController@changeArticleInAccountingDocumentForm');
$router->add('POST', '/pidb/{pidb_id}/article/{pidb_article_id}/change', 'PidbController@changeArticleInAccountingDocument');
$router->add('GET', '/pidb/{pidb_id}/article/{pidb_article_id}/duplicate', 'PidbController@duplicateArticleInAccountingDocument');
$router->add('GET', '/pidb/{pidb_id}/article/{pidb_article_id}/delete', 'PidbController@deleteArticleInAccountingDocument');

$router->add('GET', '/pidb/{pidb_id}/transactions', 'PidbController@transactionsByDocument');
$router->add('GET', '/pidb/{pidb_id}/transaction/{transaction_id}/edit', 'PidbController@formEditTransaction');
$router->add('POST', '/pidb/{pidb_id}/transaction/{transaction_id}/edit', 'PidbController@editTransaction');
$router->add('GET', '/pidb/{pidb_id}/transaction/{transaction_id}/delete', 'PidbController@deleteTransaction');

// ========== Cuttings routes ==================================================
$router->add('GET', '/cuttings/', 'CuttingController@index');
$router->add('GET', '/cuttings/add', 'CuttingController@formAdd');
$router->add('POST', '/cuttings/add', 'CuttingController@add');
$router->add('GET', '/cuttings/search/', 'CuttingController@search');
$router->add('GET', '/cutting/{cutting_id}', 'CuttingController@view');
$router->add('GET', '/cutting/{cutting_id}/edit', 'CuttingController@edit');
$router->add('GET', '/cutting/{cutting_id}/print', 'CuttingController@print');
$router->add('POST', '/cutting/{cutting_id}/addArticle', 'CuttingController@addArticle');
$router->add('POST', '/cutting/{cutting_id}/article/{article_id}/edit', 'CuttingController@editArticle');
$router->add('GET', '/cutting/{cutting_id}/article/{article_id}/delete', 'CuttingController@deleteArticle');
$router->add('GET', '/cutting/{cutting_id}/exportToAccountingDocument', 'CuttingController@exportToAccountingDocument');
$router->add('GET', '/cutting/{cutting_id}/delete', 'CuttingController@delete');

// ========== Materials routes =================================================
$router->add('GET', '/materials/', 'MaterialController@index');
$router->add('GET', '/materials/add', 'MaterialController@addForm');
$router->add('POST', '/materials/add', 'MaterialController@add');
$router->add('GET', '/materials/search/', 'MaterialController@search');

$router->add('GET', '/material/{material_id}', 'MaterialController@view');
$router->add('GET', '/material/{material_id}/edit', 'MaterialController@editForm');
$router->add('POST', '/material/{material_id}/edit', 'MaterialController@edit');

$router->add('POST', '/material/{material_id}/addSupplier', 'MaterialController@addSupplier');
$router->add('POST', '/material/{material_id}/supplier/{supplier_id}/edit', 'MaterialController@editSupplier');
$router->add('GET', '/material/{material_id}/supplier/{supplier_id}/delete', 'MaterialController@deleteSupplier');

$router->add('POST', '/material/{material_id}/addProperty', 'MaterialController@addProperty');
$router->add('GET', '/material/{material_id}/property/{property_id}/delete', 'MaterialController@deleteProperty');

// ========== Orders routes ====================================================
$router->add('GET', '/orders/', 'OrderController@index');
$router->add('GET', '/orders/add', 'OrderController@addForm');
$router->add('POST', '/orders/add', 'OrderController@add');
$router->add('GET', '/orders/search/', 'OrderController@search');

$router->add('GET', '/order/{order_id}', 'OrderController@view');
$router->add('GET', '/order/{order_id}/edit', 'OrderController@editForm');
$router->add('POST', '/order/{order_id}/edit', 'OrderController@edit');
$router->add('GET', '/order/{order_id}/delete', 'OrderController@delete');

$router->add('POST', '/order/{order_id}/addMaterial', 'OrderController@addMaterial');
$router->add('GET', '/order/{order_id}/material/{order_material_id}/edit', 'OrderController@editMaterialForm');
$router->add('POST', '/order/{order_id}/material/{order_material_id}/edit', 'OrderController@editMaterial');
$router->add('GET', '/order/{order_id}/material/{order_material_id}/delete', 'OrderController@deleteMaterial');
$router->add('GET', '/order/{order_id}/material/{order_material_id}/duplicate', 'OrderController@duplicateMaterial');

$router->add('GET', '/order/{order_id}/print', 'OrderController@print');

// ========== Articles routes ==================================================
$router->add('GET', '/articles/', 'ArticleController@index');
$router->add('GET', '/articles/add', 'ArticleController@addForm');
$router->add('POST', '/articles/add', 'ArticleController@add');
$router->add('GET', '/articles/price-list', 'ArticleController@priceList');
$router->add('GET', '/articles/search/', 'ArticleController@search');

$router->add('GET', '/articles/groups', 'ArticleController@groups');
$router->add('GET', '/articles/groups/add', 'ArticleController@addGroupForm');
$router->add('POST', '/articles/groups/add', 'ArticleController@addGroup');
$router->add('GET', '/articles/group/{group_id}', 'ArticleController@viewGroup');
$router->add('GET', '/articles/group/{group_id}/edit', 'ArticleController@editGroupForm');
$router->add('POST', '/articles/group/{group_id}/edit', 'ArticleController@editGroup');

$router->add('GET', '/article/{article_id}', 'ArticleController@view');
$router->add('GET', '/article/{article_id}/edit', 'ArticleController@editForm');
$router->add('POST', '/article/{article_id}/edit', 'ArticleController@edit');

$router->add('POST', '/article/{article_id}/addProperty', 'ArticleController@addProperty');
$router->add('GET', '/article/{article_id}/property/{property_id}/delete', 'ArticleController@deleteProperty');

// ========== Admins routes ====================================================
$router->add('GET', '/admin/', 'AdminController@index');
$router->add('GET', '/admin/base-backup', 'AdminController@baseBackup');
$router->add('GET', '/admin/company-info', 'AdminController@companyInfoViewForm');
$router->add('GET', '/admin/company-info/edit', 'AdminController@companyInfoEditForm');
$router->add('POST', '/admin/company-info/edit', 'AdminController@companyInfoEdit');
$router->add('GET', '/admin/employees', 'AdminController@employees');
$router->add('GET', '/admin/employees/search/', 'AdminController@employeesSearch');
$router->add('GET', '/admin/employee/{employee_id}', 'AdminController@employeeView');
$router->add('GET', '/admin/employee/{employee_id}/edit', 'AdminController@employeeEditForm');
$router->add('POST', '/admin/employee/{employee_id}/edit', 'AdminController@employeeEdit');


// ========== Projects routes ====================================================
$router->add('GET', '/projects/', 'ProjectController@index');
$router->add('GET', '/projects/by-city', 'ProjectController@viewByCity');
$router->add('GET', '/projects/add', 'ProjectController@addForm');
$router->add('POST', '/projects/add', 'ProjectController@add');

$router->add('GET', '/project/{project_id}', 'ProjectController@view');
$router->add('GET', '/project/{project_id}/edit', 'ProjectController@editForm');
$router->add('POST', '/project/{project_id}/edit', 'ProjectController@edit');
$router->add('GET', '/project/{project_id}/printProjectTaskWithNotes', 'ProjectController@printProjectTaskWithNotes');
$router->add('GET', '/project/{project_id}/printProjectTask', 'ProjectController@printProjectTask');
$router->add('GET', '/project/{project_id}/printInstallationRecord', 'ProjectController@printInstallationRecord');
$router->add('POST', '/project/{project_id}/addFile', 'ProjectController@addFileToProject');

$router->add('POST', '/project/{project_id}/task/add', 'ProjectController@addTask');
$router->add('GET', '/project/{project_id}/task/{task_id}/edit', 'ProjectController@editTaskForm');
$router->add('POST', '/project/{project_id}/task/{task_id}/edit', 'ProjectController@editTask');
$router->add('GET', '/project/{project_id}/task/{task_id}/delete', 'ProjectController@deleteTask');

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
