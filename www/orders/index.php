<?php
$page = "orders";
require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/../app/config/conf.php';
session_start();
if(isset($_SESSION['username'])):

    $username = $_SESSION['username'];
    $userlevel = $_SESSION['user_level'];

    $conf = new Conf();
    $client = new Client();
    $contact = new Contact();
    $material = new Material();
    $order = new Order();
    $project = new Project();

    include '../../src/orders/index.php';
else:
    header('Location: /');
endif;
