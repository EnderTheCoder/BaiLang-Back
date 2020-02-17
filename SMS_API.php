<?php
require 'Header.php';
signatureCheck();

if (isEmpty($_POST['phone']) || isEmpty($_POST['msg']))
    stdJqReturn('必填参数存在空');
stdJqReturn($sms->sendSMS($_POST['phone'], $_POST['msg']));
