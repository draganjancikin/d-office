<?php
$page = "projects";
require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/../config/appConfig.php';
require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/autoload.php';
session_start();
if(isset($_SESSION['username'])):
    $username = $_SESSION['username'];
    $userlevel = $_SESSION['user_level'];
    $client = new ClientController();
    $contact = new ContactController();
    $order = new OrderController();
    $pidb = new PidbController();
    $project = new ProjectController();
    include filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/../templates/project/index.php';
else:
    header('Location: /');
endif;
