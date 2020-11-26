<?php
$page = "cutting";
require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/../app/config/conf.php';
session_start();
if(isset($_SESSION['username'])):
    $username = $_SESSION['username'];
    $userlevel = $_SESSION['user_level'];
    $client = new Client();
    $pidb = new Pidb();
    $article = new Article();
    $cutting = new Cutting();
    include '../../src/cutting/index.php';
else:
    header('Location: /');
endif;
