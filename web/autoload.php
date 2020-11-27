<?php
function myAutoLoader($class) {
    $path = $_SERVER['DOCUMENT_ROOT'] . '/../src/Controller/' . $class . ".php";
    include $path;
}

spl_autoload_register('myAutoLoader');
