<?php
$version = "RolOffice 4.3.0 - 5.0.0";

// folders with CSS, JS, ...
switch($page){
  case ("home"):
      $stylesheet = ".lib/";
      break;
  default:
      $stylesheet = "../.lib/";
      break;
}