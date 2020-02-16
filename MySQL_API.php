<?php
require 'Header.php';
signatureCheck();
if (
    isEmpty($_POST['sql']) ||
    isEmpty($_POST['ret_val'])
) stdJqReturn('ERROR-201:参数不完整');
stdJqReturn($mysql->API_Query($_POST['sql'], $_POST['ret_val'], $_POST['param_cnt'], $_POST['params']));