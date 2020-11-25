<?php
$page = "pidb";
require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/../config/appConfig.php';
require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/autoload.php';
session_start();
if(isset($_SESSION['username'])):
    $username = $_SESSION['username'];
    $userlevel = $_SESSION['user_level'];
    $client = new ClientController();
    $contact = new ContactController();
    $article = new ArticleController();
    $pidb = new PidbController();
    include '../../src/pidb/index.php';
else:
    header('Location: /');
endif;
