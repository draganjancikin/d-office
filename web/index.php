<?php
$page = "home";
require_once __DIR__ . '/../config/bootstrap.php';

session_start();
if(isset($_SESSION['username'])):
    $username = $_SESSION['username'];
    $user_role_id = $_SESSION['user_role_id'];
    include '../app/index.php';
else:
    include '../app/formLogin.php';
endif;
