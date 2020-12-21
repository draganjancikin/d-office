<?php
$page = "materials";
require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/../config/appConfig.php';
// require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/autoload.php';
require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') .'/../vendor/autoload.php';
session_start();
if(isset($_SESSION['username'])):
    $username = $_SESSION['username'];
    $userlevel = $_SESSION['user_level'];
    $client = new \Roloffice\Controller\ClientController();
    $material = new \Roloffice\Controller\MaterialController();
    include filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/../templates/material/index.php';
else:
    header('Location: /');
endif;
