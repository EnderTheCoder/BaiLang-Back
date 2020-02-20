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
            $str .= $key . '=' . rawurlencode($value) . '&';
        }
    }
    $str .= 'app_key=' . $AppKey;
    $sign = strtoupper(md5($str));
    return $sign;
}

function signatureCheck()
{
    $return = new LibReturn();
    if (isEmpty($_POST['timestamp']) ||
        isEmpty($_POST['app_id']) ||
        isEmpty($_POST['sign'])
    ) $return->retMsg('emptyVal');
    if (time() - $_POST['timestamp'] > MAX_SIGN_LIFETIME) $return->retMsg('signOvertime');
    $sign = $_POST['sign'];
    unset($_POST['sign']);
    $sql = new LibMySQL();
    $AppKey = $sql->getApp($_POST['app_id']);
    if (signatureSpawn($_POST, $AppKey['AppKey']) != $sign)
        $return->retMsg('signErr');
}