<?php
$page = "admin";
require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/../config/appConfig.php';
// require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/autoload.php';
require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') .'/../vendor/autoload.php';
session_start();
if(isset($_SESSION['username'])):
    $username = $_SESSION['username'];
    $user_role_id = $_SESSION['user_role_id'];
    $admin = new \Roloffice\Controller\AdminController();
    include filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/../templates/admin/index.php';
else:
    header('Location: /');
endif;
