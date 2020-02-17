<?php
function sendMail($remoteEmail, $title, $body)
{
$mail = new Lib_Smtp();
$mail->setServer(SMTP_SERVER_ADDR, SMTP_USERNAME, SMTP_PASSWORD,SMTP_SERVER_PORT,true); //参数1（qq邮箱使用smtp.qq.com，qq企业邮箱使用smtp.exmail.qq.com），参数2（邮箱登陆账号），参数3（邮箱登陆密码，也有可能是独立密码，就是开启pop3/smtp时的授权码），参数4（默认25，腾云服务器屏蔽25端口，所以用的465），参数5（是否开启ssl，用465就得开启）//$mail->setServer("XXXXX", "joffe@XXXXX", "XXXXX", 465, true);
$mail->setFrom(SMTP_USER_EMAIL); //发送者邮箱
$mail->setReceiver($remoteEmail); //接收者邮箱
//$mail->addAttachment(""); //Attachment 附件，不用可注释
$mail->setMail($title, $body); //标题和内容
return $mail->send();//可以var_dump一下，发送成功会返回true，失败false
}