<?php
$page = "clients";
require_once __DIR__ .'../../../config/appConfig.php';
require_once __DIR__ .'../../../config/bootstrap.php';
require_once __DIR__ .'../../../vendor/autoload.php';
session_start();
if(isset($_SESSION['username'])):
    $username = $_SESSION['username'];
    $user_role_id = $_SESSION['user_role_id'];
    $client = new \Roloffice\Controller\ClientController();
    $contact = new \Roloffice\Controller\ContactController();
    include filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/../templates/client/index.php';
else:
    header('Location: /');
endif;
