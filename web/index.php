<?php
// web/index.php
require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$request = Request::createFromGlobals();

$map = [
    '/' => 'index',

    '/clients/' =>'client/index',

    '/pidb/' => 'pidb/index',
    '/pidb/printAccountingDocument' => 'pidb/printAccountingDocument',
    '/pidb/printAccountingDocumentW' => 'pidb/printAccountingDocumentW',
    '/pidb/printAccountingDocumentI' => 'pidb/printAccountingDocumentI',
    '/pidb/printAccountingDocumentIW' => 'pidb/printAccountingDocumentIW',
    '/pidb/printDailyCashReport' => 'pidb/printDailyCashReport',

    '/cutting/' => 'cutting/index',
    '/cutting/printCutting' => 'cutting/printCutting',

    '/materials/' => 'material/index',

    '/orders/' => 'order/index',
    '/orders/printOrder' => 'order/printOrder',

    '/articles/' => 'article/index',

    '/projects/' => 'project/index',
    '/projects/printProjectTask' => 'project/printProjectTask',
    '/projects/printInstallationRecord' => 'project/printInstallationRecord',
    '/projects/printProjectTaskWithNotes' => 'project/printProjectTaskWithNotes',

    '/admin/' => 'admin/index',
];

$path = $request->getPathInfo();
if (isset($map[$path])) {

    require_once __DIR__ . '/../config/bootstrap.php';
    session_start();
    if (isset($_SESSION['username'])){
        $username = $_SESSION['username'];
        $user_role_id = $_SESSION['user_role_id'];
        ob_start();
        extract($request->query->all(), EXTR_SKIP);
        include sprintf(__DIR__.'/../templates/%s.php', $map[$path]);
    } else {
        include '../templates/formLogin.php';
    }
    $response = new Response(ob_get_clean());

} else {
    $response = new Response('Not Found', 404);
}

$response->send();

// https://symfony.com/doc/5.4/create_framework/routing.html
