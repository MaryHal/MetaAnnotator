<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require_once 'MainApi.php';

$api = new \meta\MainApi($_REQUEST);
echo $api->process();

?>