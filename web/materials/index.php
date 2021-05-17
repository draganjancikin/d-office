<?php
$page = "materials";

require_once '../../config/appConfig.php';
require_once '../../config/bootstrap.php';
require_once '../../vendor/autoload.php';

session_start();
if(isset($_SESSION['username'])):
    $username = $_SESSION['username'];
    $user_role_id = $_SESSION['user_role_id'];
    include filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/../templates/material/index.php';
else:
    header('Location: /');
endif;
