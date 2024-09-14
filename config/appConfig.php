<?php
require_once 'dbConfig.php';

$content = file_get_contents(__DIR__ . '/../composer.json');
$content = json_decode($content,true);
define("APP_VERSION", $content['version']);

// Company data
//define("COMPANY_NAME", "PREDRAG GAJIĆ PR ROLOSTIL");
//define("COMPANY_STREET", "Vojvode Živojina Mišića 237");
//define("COMPANY_CITY", "21400 Bačka Palanka");
//define("COMPANY_PIB", "100754526");
//define("COMPANY_MB", "55060100");
//define("COMPANY_BANK_ACCOUNT_1", "160-438797-72, Banca Intesa");
//define("COMPANY_BANK_ACCOUNT_2", "330-11001058-98, Credit Agricole");

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
