<?php
$page = "admin";

require_once '../../config/bootstrap.php';

session_start();
if(isset($_SESSION['username'])):
    $username = $_SESSION['username'];
    $user_role_id = $_SESSION['user_role_id'];
    $admin = new \Roloffice\Controller\AdminController();
    include_once '../../templates/admin/index.php';
else:
    header('Location: /');
endif;
