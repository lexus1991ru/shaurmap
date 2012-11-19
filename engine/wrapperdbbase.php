<?php

require_once("settings.php");
require_once("errors.php");
require_once("validator.php");

class WrapperDBBase
{
    protected $connection = NULL;
    protected $connectionStatus = false;
    private $data = NULL;

    protected function __construct()
    {
        if($this->Connect())
        {
            $connectionStatus = true;
        }
    }

    protected function __destruct()
    {
        $this->Disconnect();
    }

    private function Connect()
    {
        $this->connection = new mysqli(DBSettings::host, DBSettings::login, DBSettings::password, DBSettings::base);
        if($this->connection->connect_errno)
        {
            return false;
        }
        else
        {
            $query = "SET CHARACTER SET 'utf8'";
            $this->connection->query($query);
            return true;
        }
    }

    private function Disconnect()
    {
        $this->connection->close();
    }

    protected function clearData()
    {
        $this->data = NULL;
    }

    protected function setData($_data)
    {
        $this->clearData();
        if($_data != NULL)
        {
            $this->data = $_data;
        }
    }

    public function getData()
    {
        if($this->data != NULL)
        {
            return $this->data;
        }
    }

    protected function checkToken($userID, $token)
    {
        if(Validator::validateUserId($userID) && Validator::validateToken($token))
        {
            $userID = $this->connection->real_escape_string($userID);
            $token = $this->connection->real_escape_string($token);
            $query = "SELECT sessionID, UNIX_TIMESTAMP(createdTime) as createdTime FROM sessions WHERE sessionKey='".$token."' AND userID = '".$userID."'";
            $result = $this->connection->query($query);
            if($this->connection->errno)
                return ERRORS::CHECK_TOKEN_MYSQL_ERROR;
            if($result->num_rows)
            {
                $row = $result->fetch_assoc();
                $ts = time();
                $passedTime = $ts - $row['createdTime'];
                if(Validator::validateTokenIsAlive($passedTime) && !Validator::validateTokenIsClosed($row['closed']))
                {
                    $query = "UPDATE sessions SET lastLogin=FROM_UNIXTIME('".$ts."') WHERE sessionID='".$row['sessionID']."'";
                    $result = $this->connection->query($query);
                    if($this->connection->errno)
                        return ERRORS::CHECK_TOKEN_MYSQL_ERROR;
                    return ERRORS::NO_ERROR;
                }
                else
                {
                    return ERRORS::SESSION_EXPIRED_ERROR;
                }
            }
        }
        else
        {
            return ERRORS::BAD_TOKEN_ERROR;
        }
    }

    protected function isUserAdmin($userID)
    {
        $rights = $this->getUserRights($userID);
        if($rights == 1)
            return true;
        return false;
    }

    protected function isUserModerator($userID)
    {
        $rights = $this->getUserRights($userID);
        if(($rights == 1) || ($rights == 2))
            return true;
        return false;
    }

    private function getUserRights($userID)
    {
        // 0 - User
        // 1 - Admin
        // 2 - Moderator
        $userID = $this->connection->real_escape_string($userID);
        $query = "SELECT permission FROM permissions WHERE userID='".$userID."'";
        $result = $this->connection->query($query);
        if($this->connection->errno)
            return 0;

        if($result->num_rows)
        {
            $row = $result->fetch_assoc();
            return $row['permission'];
        }
        return 0;
    }

};

?>
