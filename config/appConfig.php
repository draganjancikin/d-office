<?php
require_once 'dbConfig.php';

/* TODO: 
- dbConfig on LIVE change to dbConfig.live.php
- dbConfig on DEV change to dbConfig.dev.php
- dbConfig on LOCAL change to dbConfig.local.php

# Check enviroment and include dbConfig file
if (file_exists(__DIR__ . '/dbConfig.live.php')) {
  include '/dbConfig.live.php';
} else if (file_exists(__DIR__ . '/dbConfig.dev.php')) {
  include '/dbConfig.dev.php';
} else if (file_exists(__DIR__ . '/dbConfig.local.php')) {
  include '/dbConfig.local.php';
} 
*/

define("VERSION","5.3.5-6.0.5");

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
