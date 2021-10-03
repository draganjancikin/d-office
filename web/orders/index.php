<?php
$page = "orders";

require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/../config/bootstrap.php';

session_start();
if(isset($_SESSION['username'])):
    $username = $_SESSION['username'];
    $user_role_id = $_SESSION['user_role_id'];
    include filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/../templates/order/index.php';
else:
    header('Location: /');
endif;
