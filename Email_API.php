<?php
require 'Header.php';
signatureCheck();
if (
    isEmpty($_POST['email']) ||
    isEmpty($_POST['head']) ||
    isEmpty($_POST['body'])
) $return->retMsg('emptyVal');
if (sendMail($_POST['email'], $_POST['head'], $_POST['body']))
    $return->retMsg('success');
else $return->retMsg('emailServerErr');