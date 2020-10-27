<?php
$page = "clients";
require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/../app/config/conf.php';
session_start();
if(isset($_SESSION['username'])):
    $username = $_SESSION['username'];
    $userlevel = $_SESSION['user_level'];
    $database = new Database();
    $client = new Client();
    $contact = new Contact();
    include '../../src/clients/index.php';
else:
    header('Location: /');
endif;
