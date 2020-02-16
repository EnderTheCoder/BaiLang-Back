<?php
class Redis_API
{
    private function STD_REDIS_CONNECT()
    {
        $redis = new Redis();
        $redis->connect('127.0.0.1', 6379);//serverip port
//      $redis->auth('mypassword');//my redis password
        return $redis;
    }

    public function phoneCntInc($Phone)
    {
        $conn = $this->STD_REDIS_CONNECT();
        $result = $conn->get('PhoneCnt-'.$Phone);
        if ($result == null) $conn->set('PhoneCnt-'.$Phone, 1);
        else $conn->set('PhoneCnt-'.$Phone, $result + 1);
    }

    public function phoneCntGet($Phone)
    {
        $conn = $this->STD_REDIS_CONNECT();
        return $conn->get('PhoneCnt-'.$Phone);
    }

    public function IPCntInc($IP)
    {
        $conn = $this->STD_REDIS_CONNECT();
        $result = $conn->get('IPCnt-'.$IP);
        if ($result == null) $conn->set('IPCnt-'.$IP, 1);
        else $conn->set('IPCnt-'.$IP, $result + 1);
    }

    public function IPCntGet($IP)
    {
        $conn = $this->STD_REDIS_CONNECT();
        return $conn->get('IPCnt-'.$IP);
    }

    public function IPSMSTimeSet($IP)
    {
        $conn = $this->STD_REDIS_CONNECT();
        return $conn->set('IPSMSTime-'.$IP, time());
    }

    public function IPSMSTimeGet($IP)
    {
        $conn = $this->STD_REDIS_CONNECT();
        return $conn->get('IPSMSTime-'.$IP);
    }

    public function phoneCapSet($Phone, $Key)
    {
        $conn = $this->STD_REDIS_CONNECT();
        return $conn->setex('PhoneCap-'.$Phone, SMS_LIVE, $Key);
    }
    public function phoneCapGet($Phone)
    {
        $conn = $this->STD_REDIS_CONNECT();
        return $conn->get('PhoneCap-'.$Phone);
    }
}