<?php
$page = "cutting";
require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/../config/appConfig.php';
require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/autoload.php';
session_start();
if(isset($_SESSION['username'])):
    $username = $_SESSION['username'];
    $userlevel = $_SESSION['user_level'];
    $client = new ClientController();
    $pidb = new PidbController();
    $article = new ArticleController();
    $cutting = new CuttingController();
    include '../../src/cutting/index.php';
else:
    header('Location: /');
endif;
