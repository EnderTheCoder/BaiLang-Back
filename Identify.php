<?php
require "Header.php";
signatureCheck();
switch ($_POST['type']) {
    case 'login':
        if (isEmpty($_POST['id']) ||
            isEmpty($_POST['password'])) {
            $return->retMsg('emptyVal');
        }
        if (!strstr($_POST['id'], '@'))
            if ($_POST['id'] >= 10000000000) $type = 'MobilePhone';
            else $type = 'Uid';
        else $type = 'Email';
        $reference = $mysql->getUserInf($type, $_POST['id']);
        if ($reference == null || $reference['Password'] != md5($_POST['password'])) {
            $return->retMsg('passErr');
        }
        if ($reference['EmailProve'] == 0) {
            $return->retMsg('unverifiedEmail');
        }
        $return->retMsg('success');
        break;
    case 'register':
        if (isEmpty($_POST['nick_name']) ||
            isEmpty($_POST['password']) ||
            isEmpty($_POST['phone']) ||
            isEmpty($_POST['email']) ||
            isEmpty($_POST['sms_captcha']))
            $return->retMsg('emptyVal');
        if ($redis->phoneCapGet($_POST['phone']) != $_POST['sms_captcha']) {
            $return->retMsg('captchaErr');
        }
        $SMS_Captcha = mt_rand(100000, 999999);
        $redis->phoneCapSet($_POST['phone'], $SMS_Captcha);
        $emailKey = md5(spawnKey());
        $link = URL . "/EmailReturn.html?token=" . $emailKey;
        $message = "新用户" . $_POST['nick_name'] . "您好，轻点击下方链接来完成您在<a href='" . URL . "'>【白浪轻腾】</a>的注册<br>" . "<a href='$link'>$link</a>";
        sendMail($_POST['email'], "完成您在白浪轻腾的注册", $message);
        $Uid = $mysql->regNewUser($_POST['phone'], $_POST['email'], $_POST['nick_name'], md5($_POST['password']), time(), $_SERVER['REMOTE_ADDR']);
        $mysql->saveEmailToken($Uid, $emailKey, "register");
        $return->retMsg('success');
        break;
    case 'retrieve':

        break;
    case 'phone_verify':
        if (isEmpty($_POST['captcha']) ||
            isEmpty($_POST['phone']) ||
            isEmpty($_POST['nick_name'])) {
            $return->retMsg('emptyVal');
        }
        if ($mysql->getUserInf('MobilePhone', $_POST['phone']) != null) {
            $return->setType('duplicateVal');
            $return->setMsg('该手机号已被注册，请更换');
            $return->run();
        }
        if (!captchaCheck()) {
            $return->retMsg('captchaErr');
        }
        if (strlen($_POST['phone']) != 11) {
            $return->retMsg('formatErr');
        }
        if ($redis->phoneCntGet($_POST['phone']) > SMS_MAX_SING_PHONE_REQUESTS ||
            $redis->IPCntGet($_SERVER['REMOTE_ADDR']) > SMS_MAX_SING_IP_REQUESTS) {
            $return->retMsg('smsMaxLimReached');
        }
        $latestTime = $redis->IPSMSTimeGet($_SERVER['REMOTE_ADDR']);
        if (time() - $latestTime < SMS_REQUEST_INTERVAL) {
            $return->retMsg('requestTooFast', time());
        }
        $SMS_Captcha = mt_rand(100000, 999999);
        $redis->phoneCapSet($_POST['phone'], $SMS_Captcha);
        $message = "【白浪轻腾】尊敬的" . $_POST['nick_name'] . "，您注册白浪轻腾通行证的验证码是：" . $SMS_Captcha . "，为了您的账号安全，请勿泄露给他人。";
        $sms->sendSMS($_POST['phone'], $message);
        $redis->phoneCntInc($_POST['phone']);
        $redis->IPSMSTimeSet($_SERVER['REMOTE_ADDR']);
        $redis->IPCntInc($_SERVER['REMOTE_ADDR']);
        $return->retMsg('success', '验证码已经发出，' . SMS_LIVE . '秒内有效，请注意查收');
        break;
    case 'emailVerify':
        if (isEmpty($_POST['token'])) {
            $return->retMsg('emptyVal');
        }
        $token = $mysql->getEmailToken($_POST['token']);
        if ($token['Token'] == null) {
            $return->retMsg('passErr');
        }
        switch ($token['Action']) {
            case 'register':
                $mysql->enableUser($token['Uid']);
                $mysql->deleteEmailToken($token['Token']);
                $return->retMsg('success');
                break;
            default:
                $return->retMsg('dbgMsg');
                break;
        }
        break;
    case 'duplicateCheck':
        if ($mysql->getUserInf('Email', $_POST['email']) != null) {
            $return->setType('duplicateVal');
            $return->setMsg('该邮箱已被注册，请更换');
            $return->run();
        }
        $return->retMsg('success');
        break;
    default:
        $return->retMsg('dbgMsg');
        break;
}