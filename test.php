<?php
require 'Header.php';
$params = array(
    'sql' => 'SELECT MobilePhone,Passwords,Email FROM Users WHERE MobilePhone = \'18208157618\'',
    'timestamp' => 1581961380,
    'app_id' => 1,
);
ksort($params);
$str = '';
foreach ($params as $key => $value) {
    if ($value !== '') {
        $str .= $key.'='.     rawurlencode($value).'&';
    }
}
$str .= 'app_key='.'bbc9a26ded02af12866784448a8cb0e5';
var_dump($str);
var_dump(md5('app_id=1&sql=SELECT%20MobilePhone%2CPasswords%2CEmail%20FROM%20Users%20WHERE%20MobilePhone%20%3D%20%2718208157618%27&timestamp=1581961380&app_key=bbc9a26ded02af12866784448a8cb0e5'));
$sign = strtoupper(md5($str));
stdJqReturn($sign);