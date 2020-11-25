<?php
$page = "materials";
require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/../config/appConfig.php';
require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/autoload.php';
session_start();
if(isset($_SESSION['username'])):
    $username = $_SESSION['username'];
    $userlevel = $_SESSION['user_level'];
    $client = new Client();
    $material = new Material();
    include '../../src/materials/index.php';
else:
    header('Location: /');
endif;
