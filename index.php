<?php

$root = "/fountains";
$path = $_SERVER['REQUEST_URI'];
if(strcmp(substr($path, 0, strlen($root)), $root) == 0) { $path = substr($path, strlen($root)); }
if(strcmp($path, "/") == 0) { $path = "/index.php"; }
$local_path = dirname(__FILE__) . "/repo/var/www" . $path;
if(file_exists($local_path)) { include($local_path); }
