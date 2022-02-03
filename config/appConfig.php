<?php
require_once 'dbConfig.php';

define("VERSION","6.2.0");

// Company data
define("COMPANY_NAME", "PREDRAG GAJIĆ PR ROLOSTIL");
define("COMPANY_STREET", "Vojvode Živojina Mišića 237");

if(empty($page)) {
    $page = "";
}

// folders with CSS, JS, ...
switch($page){
    case ("home"):
        $stylesheet = ".lib/";
        break;
    default:
        $stylesheet = "../.lib/";
        break;
}
