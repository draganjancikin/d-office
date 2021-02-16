<?php
$page = "articles";
require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/../config/appConfig.php';
// require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/autoload.php';
require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') .'/../vendor/autoload.php';
session_start();
if(isset($_SESSION['username'])):
    $username = $_SESSION['username'];
    $user_role_id = $_SESSION['user_role_id'];
    $article = new \Roloffice\Controller\ArticleController();
    include filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/../templates/article/index.php';
else:
    header('Location: /');
endif;
