<?php
$page = "pidb";
require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/../config/appConfig.php';
require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') .'/../config/bootstrap.php';
require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') .'/../vendor/autoload.php';
session_start();
if(isset($_SESSION['username'])):
    $username = $_SESSION['username'];
    $user_role_id = $_SESSION['user_role_id'];
    $client = new \Roloffice\Controller\ClientController();
    $contact = new \Roloffice\Controller\ContactController();
    $article = new \Roloffice\Controller\ArticleController();
    $pidb = new \Roloffice\Controller\PidbController();
    include filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/../templates/pidb/index.php';
else:
    header('Location: /');
endif;
