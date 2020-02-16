<?php
function isEmpty($str)
{
    return !(!empty($str) && isset($str));
}

function spawnKey()
{
    $randLength = 20;
    $chars = 'abcdefghijklmnopqrstuvwxyzQWERTYUIOPASDFGHJKLZXCVBNM';
    $len = strlen($chars);
    $randStr = '';
    for ($i = 0; $i < $randLength; $i++) {
        $randStr .= $chars[rand(0, $len - 1)];
    }
    $Key = $randStr . time();
    $Key = base64_encode($Key);
    return md5($Key);
}

function captchaCheck()
{
    $compare = $_SESSION['captcha'];
    $_SESSION['captcha'] = rand();
    return $_POST['captcha'] != $compare;
}

function signatureSpawn($params, $AppKey)
{
    ksort($params);
    $str = '';
    foreach ($params as $key => $value) {
        if ($value !== '') {
            $str .= $key . '=' . urlencode($value) . '&';
        }
    }
    $str .= 'app_key=' . $AppKey;
    $sign = strtoupper(md5(base64_encode(md5($str))));
    return $sign;
}

function signatureCheck()
{
    if (time() - $_POST['timestamp'] > MAX_SIGN_LIFETIME) stdJqReturn('ERROR-501连接超时');
    $sign = $_POST['sign'];
    unset($_POST['sign']);
    $sql = new MySQL_API();
    $AppKey = $sql->getApp($_POST['app_id']);
    if (signatureSpawn($_POST, $AppKey) != $sign)
        stdJqReturn('ERROR-500不错的尝试');
}