<?php
$page = "orders";

require_once '../../config/bootstrap.php';

session_start();
if(isset($_SESSION['username'])):
    $username = $_SESSION['username'];
    $user_role_id = $_SESSION['user_role_id'];
    include_once '../../templates/order/index.php';
else:
    header('Location: /');
endif;
