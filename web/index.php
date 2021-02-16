<?php
$page = "home";
require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/dbConfig.php';
require_once __DIR__ . '/../config/bootstrap.php';

session_start();
if(isset($_SESSION['username'])):
    $username = $_SESSION['username'];
    $user_role_id = $_SESSION['user_role_id'];
    $db = new \Roloffice\Core\Database();
    include '../app/index.php';
else:
    include '../app/formLogin.php';
endif;
