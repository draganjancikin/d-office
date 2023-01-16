<?php
$content = file_get_contents(__DIR__ . '/../composer.json');
$content = json_decode($content,true);
define("APP_VERSION", $content['version']);

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
