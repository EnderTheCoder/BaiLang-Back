<?php
require 'Header.php';

signatureCheck();
if (
    isEmpty($_POST['email']) ||
    isEmpty($_POST['head']) ||
    isEmpty($_POST['body'])
) stdJqReturn('ERROR-201:参数不完整');
sendMail($_POST['email'], $_POST['head'], $_POST['body']);
stdJqReturn('SUCCESS-100:发送成功');