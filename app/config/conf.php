<?php
$version = "RolOffice 4.1.19";
$root = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/../";

// required classes
require_once $root . 'app/classes/Database.class.php'; // new class for Database
require_once $root . 'app/classes/DB.class.php'; // old class for Database
require_once $root . 'app/classes/Conf.class.php';
require_once $root . 'src/clients/classes/Client.class.php';
require_once $root . 'src/clients/classes/Contact.class.php';
require_once $root . 'src/pidb/classes/Pidb.class.php';
require_once $root . 'src/articles/classes/Article.class.php';
require_once $root . 'src/materials/classes/Material.class.php';
require_once $root . 'src/orders/classes/Order.class.php';
require_once $root . 'src/projects/classes/Project.class.php';
require_once $root . 'src/cutting/classes/Cutting.class.php';
require_once $root . 'src/admin/classes/Admin.class.php';

// folders with CSS, JS, ...
switch($page){
    case ("home"):
        $stylesheet = ".lib/";
        break;
    default:
        $stylesheet = "../.lib/";
        break;
}
