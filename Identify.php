<?php
require "Header.php";
signatureCheck();
$result = array(
    'action' => null,
    'msg' => null,
    'value' => null,
);
switch ($_POST['type']) {
    case 'login':
        if (isEmpty($_POST['id']) ||
            isEmpty($_POST['password'])) {
            $result['action'] = 'alert';
            $result['msg'] = '输入存在空值！';
            stdJqReturn($result);
        }
//        if (!captchaCheck()) {
//            $result['action'] = 'alert';
//            $result['msg'] = '验证码错误！';
//            stdJqReturn($result);
//        }
        if (!strstr($_POST['id'], '@'))
            if ($_POST['id'] >= 10000000000) $type = 'MobilePhone';
            else $type = 'Uid';
        else $type = 'Email';
        $reference = $mysql->getUserInf($type, $_POST['id']);
        if ($reference == null || $reference['Password'] != md5($_POST['password'])) {
            $result['action'] = 'alert';
            $result['msg'] = '密码错误或该用户不存在';
            stdJqReturn($result);
        }
        if ($reference['EmailProve'] == 0) {
            $result['action'] = 'alert';
            $result['msg'] = '您尚未进入邮箱查收验证邮件，请验证后重试';
            stdJqReturn($result);
        }
        $return->retMsg('success');
        break;
    case 'register':
        if (isEmpty($_POST['nickName']) ||
            isEmpty($_POST['password']) ||
            isEmpty($_POST['phone']) ||
            isEmpty($_POST['email']) ||
            isEmpty($_POST['SMS_Captcha'])) {
            $result['action'] = 'alert';
            $result['msg'] = '输入存在空值！';
            stdJqReturn($result);
        }
        if ($redis->phoneCapGet($_POST['phone']) != $_POST['SMS_Captcha']) {
            $result['action'] = 'alert';
            $result['msg'] = '手机未通过验证！';
            stdJqReturn($result);
        }
        $SMS_Captcha = mt_rand(100000, 999999);
        $redis->phoneCapSet($_POST['phone'], $SMS_Captcha);
        $emailKey = md5(spawnKey());
        $link = URL . "/EmailReturn.html?token=" . $emailKey;
        $message = "新用户" . $_POST['nickName'] . "您好，轻点击下方链接来完成您在<a href='" . URL . "'>【白浪轻腾】</a>的注册<br>" . "<a href='$link'>$link</a>";
        sendMail($_POST['email'], "完成您在白浪轻腾的注册", $message);
        $Uid = $mysql->regNewUser($_POST['phone'], $_POST['email'], $_POST['nickName'], md5($_POST['password']), time(), $_SERVER['REMOTE_ADDR']);
        $mysql->saveEmailToken($Uid, $emailKey, "register");
        $result['action'] = 'jump';
        $result['msg'] = '注册成功，您的用户id(UID)为'.$Uid.'，轻注意进入邮箱查收验证邮件以启用账户';
        $result['value'] = URL . '/Identify.html';
        stdJqReturn($result);
        break;
    case 'retrieve':

        break;
    case 'phoneVerify':
        if (isEmpty($_POST['captcha']) ||
            isEmpty($_POST['phone']) ||
            isEmpty($_POST['nickName'])) {
            $result['action'] = 'alert';
            $result['msg'] = '必填项存在留空！';
            stdJqReturn($result);
        }
        if ($mysql->getUserInf('MobilePhone', $_POST['phone']) != null) {
            $result['action'] = 'alert';
            $result['msg'] = '该号码已被注册，请更换无重复的号码';
            stdJqReturn($result);
        }
        if (!captchaCheck()) {
            $result['action'] = 'alert';
            $result['msg'] = '验证码错误！';
            stdJqReturn($result);
        }
        if (strlen($_POST['phone']) != 11) {
            $result['action'] = 'alert';
            $result['msg'] = '号码格式错误！';
            stdJqReturn($result);
        }
        if ($redis->phoneCntGet($_POST['phone']) > SMS_MAX_SING_PHONE_REQUESTS ||
            $redis->IPCntGet($_SERVER['REMOTE_ADDR']) > SMS_MAX_SING_IP_REQUESTS) {
            $result['action'] = 'alert';
            $result['msg'] = '您的请求次数超过最大上限！';
            stdJqReturn($result);
        }
        $latestTime = $redis->IPSMSTimeGet($_SERVER['REMOTE_ADDR']);
        if (time() - $latestTime < SMS_REQUEST_INTERVAL) {
            $result['action'] = 'alert';
            $result['msg'] = '您的请求速度过快！请' . (time() - $latestTime) . '后重试';
            stdJqReturn($result);
        }
        $SMS_Captcha = mt_rand(100000, 999999);
        $redis->phoneCapSet($_POST['phone'], $SMS_Captcha);
        $message = "【白浪轻腾】尊敬的" . $_POST['nickName'] . "，您注册白浪轻腾通行证的验证码是：" . $SMS_Captcha . "，为了您的账号安全，请勿泄露给他人。";
        $sms->sendSMS($_POST['phone'], $message);
        $result['action'] = 'alert';
        $result['msg'] = '验证码已经发出，' . SMS_LIVE . '秒内有效，请注意查收';
        $redis->phoneCntInc($_POST['phone']);
        $redis->IPSMSTimeSet($_SERVER['REMOTE_ADDR']);
        $redis->IPCntInc($_SERVER['REMOTE_ADDR']);
        stdJqReturn($result);
        break;
    case 'emailVerify':
        if (isEmpty($_POST['token'])) {
            $result['action'] = 'alert';
            $result['msg'] = '链接缺少关键部分！';
            stdJqReturn($result);
        }
        $token = $mysql->getEmailToken($_POST['token']);
        if ($token['Token'] == null) {
            $result['action'] = 'alert';
            $result['msg'] = '链接不存在或已经失效！';
            stdJqReturn($result);
        }
        switch ($token['Action']) {
            case 'register':
                $mysql->enableUser($token['Uid']);
                $mysql->deleteEmailToken($token['Token']);
                $result['action'] = 'jump';
                $result['msg'] = '新用户' . $token['Uid'] . '您好，您的邮箱验证成功，将跳转至登录页';
                $result['value'] = URL . '/Identify.html';
                stdJqReturn($result);
                break;
            default:
                $result['action'] = 'alert';
                $result['msg'] = $token['Action'];
                stdJqReturn($result);
                break;
        }
        break;
    case 'duplicateCheck':
        if ($mysql->getUserInf('Email', $_POST['email']) != null) {
            $result['action'] = 'alert';
            $result['msg'] = '该邮箱已被注册，请更换无重复的邮箱';
            stdJqReturn($result);
        }
        $result['action'] = 'ok';
        stdJqReturn($result);
        break;
    default:
        $result['action'] = 'jump';
        $result['value'] = URL;
        stdJqReturn($result);
        break;
}