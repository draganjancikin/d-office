<?php
$page = "home";
require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/../app/config/conf.php';
session_start();
if(isset($_SESSION['username'])):
    $username = $_SESSION['username'];
    $userlevel = $_SESSION['user_level'];
    $conf = new Conf();
    $database = new Database();
    include '../app/index.php';
else:
    include '../app/formLogin.php';
endif;
