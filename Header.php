<?php
header('Access-Control-Allow-Origin:*');
header('Content-Type:application/json; charset=utf-8');
session_start();
require "config/site_config.php";
require "class/LibSMTP.php";
require "function/Email.php";
require "function/Security.php";
require "class/LibMySQL.php";
require "class/LibRedis.php";
require "class/LibSMS.php";
require "class/LibReturn.php";
$mysql = new LibMySQL();
$redis = new LibRedis();
$sms = new LibSMS();
$return = new LibReturn();