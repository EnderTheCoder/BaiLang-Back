<?php
require 'Header.php';
signatureCheck();
if (isEmpty($_POST['phone']) || isEmpty($_POST['msg']))
    $return->retMsg('emptyVal');
$result = $sms->sendSMS($_POST['phone'], $_POST['msg']);
$result = json_decode($result);
if ($result['code'] == 0)
    $return->retMsg('success', $result);
else $return->retMsg('smsServerErr', $result);