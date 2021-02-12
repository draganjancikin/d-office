<?php
$page = "clients";
require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/../config/appConfig.php';
require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') .'/../config/bootstrap.php';
require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') .'/../vendor/autoload.php';
session_start();
if(isset($_SESSION['username'])):
    $username = $_SESSION['username'];
    $userlevel = $_SESSION['user_level'];
    $client = new \Roloffice\Controller\ClientController();
    $contact = new \Roloffice\Controller\ContactController();
    include filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/../templates/client/index.php';
else:
    header('Location: /');
endif;
