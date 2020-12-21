<?php
$page = "home";
require __DIR__ . '/../vendor/autoload.php';

require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/../config/appConfig.php';

session_start();
if(isset($_SESSION['username'])):
    $username = $_SESSION['username'];
    $userlevel = $_SESSION['user_level'];
    $db = new \Roloffice\Controller\DatabaseController();
    include '../app/index.php';
else:
    include '../app/formLogin.php';
endif;
