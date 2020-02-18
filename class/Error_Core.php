<?php


class Error_Core
{
    protected $code = array(
        'success' => array(
            'code' => 100,
            'msg' => '请求成功',
        ),
        'jump' => array(),
        'signErr' => array(
            'code' => 201,
            'msg' => '不错的尝试'
        ),
        'dbErr' => array(
            'code' => 202,
            'msg' => '数据库错误',
        ),
        'emptyVal' => array(
            'code' => 203,
            'msg' => '参数不完整',
        ),
        'smsMaxLimReached' => array(
            'code' => 204,
            'msg' => '短信请求次数达到上限！',
        ),
        'emailMaxLimReached' => array(
            'code' => 205,
            'msg' => '邮箱请求次数达到上线！',
        ),
        'signOvertime' => array(
            'code' => 206,
            'msg' => '签名已经失效'
        ),
        'passErr' => array(
            'code' => 207,
            'msg' => '密码错误',
        ),
        'unverifiedEmail' => array(
            'code' => 208,
            'msg' => '该邮箱未经认证',
        ),
        'emailServerErr' => array(
            'code' => 209,
            'msg' => '邮件服务器发生错误或邮件地址错误'
        ),
        'smsServerErr' => array(
            'code' => 210,
            'msg' => '短信服务发生错误'
        ),
        'customMsg' => array(
            'code' => 300,
            'msg' => null,
        ),
    );

    public function retMsg($type, $result = null, $msg = null)
    {
        $ret = $this->code[$type];
        if ($result)
            $ret = array_merge($ret, $result);
        if ($msg) $ret['msg'] = $msg;
        $this->jsonReturn($ret);
    }

    private function jsonReturn($res)
    {
        echo json_encode($res);
        exit;
    }
}