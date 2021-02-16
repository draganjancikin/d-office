<?php
$page = "orders";
require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/../config/appConfig.php';
require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/../config/bootstrap.php';
require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') .'/../vendor/autoload.php';
session_start();
if(isset($_SESSION['username'])):
    $username = $_SESSION['username'];
    $userlevel = $_SESSION['user_level'];
    $material = new \Roloffice\Controller\MaterialController();
    $project = new \Roloffice\Controller\ProjectController();
    $order = new \Roloffice\Controller\OrderController();
    include filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/../templates/order/index.php';
else:
    header('Location: /');
endif;
