<?php
$version = "RolOffice 4.3.2 - 5.0.4-alphas";

// folders with CSS, JS, ...
switch($page){
    case ("home"):
        $stylesheet = ".lib/";
        break;
    default:
        $stylesheet = "../.lib/";
        break;
}
