<?php
if(!isset($_GET['cmd'])){
	exit('invalid request');
}

$cmd = $_GET['cmd'];
if(!preg_match('/^\w+$/', $cmd)){
	exit('invalid request');
}

$file = "api/{$cmd}.php";
if(!file_exists($file)){
	exit('api not found');
}

define('APP_PATH', dirname(__FILE__));
date_default_timezone_set('Asia/Shanghai');

header('Access-Control-Allow-Origin: *');
require($file);