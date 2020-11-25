<?php
$page = "articles";
require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/../config/appConfig.php';
require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/autoload.php';
session_start();
if(isset($_SESSION['username'])):
    $username = $_SESSION['username'];
    $userlevel = $_SESSION['user_level'];
    $article = new ArticleController();
    include filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/../templates/article/index.php';
else:
    header('Location: /');
endif;
