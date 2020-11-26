<?php
function myAutoLoader($class) {
    include $_SERVER['DOCUMENT_ROOT'] . '/../src/Controller/' . $class . ".php";
}

spl_autoload_register('myAutoLoader');
