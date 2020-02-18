<?php


class MySQL_API
{
    private function STD_MYSQL_CONNECT()
    {
        $conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD);
        $conn->query("set names utf8");
        $conn->select_db(DB_NAME);
        return $conn;
    }

    private function STD_PDO_CONNECT($db_name)
    {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . $db_name;
        $conn = new pdo($dsn, DB_USERNAME, DB_PASSWORD);
        $conn->query('ser names utf8');
        return $conn;
    }

    public function getUserInf($type, $value)
    {
        $result = array();
        $sql = null;
        switch ($type) {
            case 'Uid':
                $sql = "SELECT Uid, MobilePhone, Email, NickName, Passwords, RegTime, Regip, LastTime, Lastip, OlTime, Money, Golds, Points, BlackList, Emailprove FROM Users WHERE Uid = ?";
                break;
            case 'Email':
                $sql = "SELECT Uid, MobilePhone, Email, NickName, Passwords, RegTime, Regip, LastTime, Lastip, OlTime, Money, Golds, Points, BlackList, Emailprove FROM Users WHERE Email = ?";
                break;
            case 'MobilePhone':
                $sql = "SELECT Uid, MobilePhone, Email, NickName, Passwords, RegTime, Regip, LastTime, Lastip, OlTime, Money, Golds, Points, BlackList, Emailprove FROM Users WHERE MobilePhone = ?";
                break;
        }
        $conn = $this->STD_MYSQL_CONNECT();
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $value);
        $stmt->bind_result(
            $result['Uid'],
            $result['MobilePhone'],
            $result['Email'],
            $result['NickName'],
            $result['Password'],
            $result['RegTime'],
            $result['RegIP'],
            $result['LastTime'],
            $result['LastIP'],
            $result['OlTime'],
            $result['Money'],
            $result['Golds'],
            $result['Points'],
            $result['BlackList'],
            $result['EmailProve']
        );
        $stmt->execute();
        $stmt->fetch();
        return $result;
    }

    public function regNewUser($Phone, $Email, $NickName, $Password, $RegTime, $RegIP)
    {
        $sql = "INSERT INTO Users (MobilePhone, Email, NickName, Passwords, RegTime, Regip) VALUES (?, ?, ?, ?, ?, ?);";
        $conn = $this->STD_MYSQL_CONNECT();
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssssss', $Phone, $Email, $NickName, $Password, $RegTime, $RegIP);
        $stmt->execute();
        return mysqli_insert_id($conn);
    }

    public function saveEmailToken($Uid, $Token, $Action)
    {
        $time = time();
        $sql = "INSERT INTO EmailToken (Uid, Token, Action, Time, State) VALUES (?, ?, ?, " . "$time" . ", 1)";
        $conn = $this->STD_MYSQL_CONNECT();
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sss', $Uid, $Token, $Action);
        $stmt->execute();
    }

    public function getEmailToken($Token)
    {
        $result = array();
        $sql = "SELECT Id, Uid, Token, Action, `Time`, State FROM EmailToken WHERE Token = ?";
        $conn = $this->STD_MYSQL_CONNECT();
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $Token);
        $stmt->bind_result($result['Id'], $result['Uid'], $result['Token'], $result['Action'], $result['Time'], $result['State']);
        $stmt->execute();
        $stmt->fetch();
        return $result;
    }

//    public function updateUserInf($Uid, $key, $value)
//    {
//        $key = '`'.$key.'`';
//        $sql = "UPDATE Users SET " . $key." = ? WHERE Uid = ?";
//        $conn = $this->STD_MYSQL_CONNECT();
//        $stmt = $conn->prepare($sql);
//        $stmt->bind_param('ss', $Uid, $value);
//        $stmt->execute();
//    }

    public function enableUser($Uid)
    {
        $sql = 'UPDATE Users SET Emailprove = 1 WHERE Uid = ' . $Uid;
        $conn = $this->STD_MYSQL_CONNECT();
        $conn->query($sql);
    }

    public function deleteEmailToken($Token)
    {
        $sql = "DELETE FROM EmailToken WHERE Token = ?";
        $conn = $this->STD_MYSQL_CONNECT();
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $Token);
        $stmt->execute();
    }

    public function getApp($AppID)
    {
        $result = array();
        $sql = "SELECT AppKey, Dbname FROM Apps WHERE Id = ?";
        $conn = $this->STD_MYSQL_CONNECT();
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $AppID);
        $stmt->bind_result($result['AppKey'], $result['DB_Name']);
        $stmt->execute();
        $stmt->fetch();
        return $result;
    }

    public function API_Query($sql, $paramCnt, $param, $app_id)
    {
        $db_name = $this->getApp($app_id);
        $conn = $this->STD_PDO_CONNECT($db_name['DB_Name']);
        $stmt = $conn->prepare($sql);
        for ($i = 1; $i <= $paramCnt; $i++) {
            $stmt->bindValue($i, $param[$i], PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }
}