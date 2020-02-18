<?php
require 'Header.php';
signatureCheck();
if (isEmpty($_POST['sql']))
    $return->retMsg('emptyVal');
try {
    $result = $mysql->API_Query($_POST['sql'], $_POST['app_id'], $_POST['params']);
} catch (Exception $exception) {
    $return->retMsg('dbErr', '错误代码：' . $exception->getCode() . '错误信息' . $exception->getMessage());
}
$return->retMsg('success', $result);