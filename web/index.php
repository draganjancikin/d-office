<?php
$page = "home";
require __DIR__ . '/../vendor/autoload.php';

require_once __DIR__ . '/../config/dbConfig.php';

session_start();
if(isset($_SESSION['username'])):
    $username = $_SESSION['username'];
    $userlevel = $_SESSION['user_level'];
    $db = new \Roloffice\Core\Database();
    include '../app/index.php';
else:
    include '../app/formLogin.php';
endif;
