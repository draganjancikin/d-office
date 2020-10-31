<?php
$page = "home";
require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/../app/config/conf.php';
session_start();
if(isset($_SESSION['username'])):
    $username = $_SESSION['username'];
    $userlevel = $_SESSION['user_level'];
    $db = new DB();
    include '../app/index.php';
else:
    include '../app/formLogin.php';
endif;
