<?php
$page = "projects";
require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/../app/config/conf.php';
session_start();
if(isset($_SESSION['username'])):
    
    $username = $_SESSION['username'];
    $userlevel = $_SESSION['user_level'];

    $client = new Client();
    $contact = new Contact();
    $order = new Order();
    $pidb = new Pidb();
    $project = new Project();
    
    include '../../src/projects/index.php';
else:
    header('Location: /');
endif;