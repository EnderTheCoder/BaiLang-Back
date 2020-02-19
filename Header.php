<?php
header('Access-Control-Allow-Origin:*');
header('Content-Type:application/json; charset=utf-8');
session_start();
require "config/site_config.php";
require "class/Lib_SMTP.php";
require "function/Return.php";
require "function/Email.php";
require "function/Security.php";
require "class/MySQL_API.php";
require "class/Redis_API.php";
require "class/SMS_API.php";
require "class/Error_Core.php";
$mysql = new MySQL_API();
$redis = new Redis_API();
$sms = new SMS_API();
$return = new Error_Core();