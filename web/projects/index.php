<?php
$page = "projects";
require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/../config/appConfig.php';
require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/../config/bootstrap.php';
require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') .'/../vendor/autoload.php';
session_start();
if(isset($_SESSION['username'])):
    $username = $_SESSION['username'];
    $userlevel = $_SESSION['user_level'];
    $client = new \Roloffice\Controller\ClientController();
    $order = new \Roloffice\Controller\OrderController();
    $pidb = new \Roloffice\Controller\PidbController();
    $project = new \Roloffice\Controller\ProjectController();
    include filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/../templates/project/index.php';
else:
    header('Location: /');
endif;
