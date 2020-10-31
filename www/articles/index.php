<?php
$page = "articles";
require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/../app/config/conf.php';
session_start();
if(isset($_SESSION['username'])):

    $username = $_SESSION['username'];
    $userlevel = $_SESSION['user_level'];
    
    $material = new Material();
    $article = new Article();
    
    include '../../src/articles/index.php';
else:
    header('Location: /');
endif;