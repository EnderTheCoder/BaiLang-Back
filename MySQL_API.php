<?php
require 'Header.php';
signatureCheck();
if (isEmpty($_POST['sql']))
    stdJqReturn('ERROR-201:参数不完整');
try {
    $result = $mysql->API_Query($_POST['sql'], $_POST['param_cnt'], $_POST['params'], $_POST['app_id']);
} catch (Exception $exception) {
    stdJqReturn('操作失败，错误代码：'.$exception->getCode().'错误信息：'.$exception->getMessage());
}
stdJqReturn($result);