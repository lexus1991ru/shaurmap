<?php

require_once("types.php");
require_once("settings.php");
require_once("common.php");
require_once("errors.php");

class WrapperDB
{
    private $connection = NULL;
    private $settings = NULL;
    private $connectionStatus = false;
    private $data = NULL;

    function __construct()
    {
        if($this->Connect())
        {
            $connectionStatus = true;
        }
    }

    function __destruct()
    {
        $this->Disconnect();
    }

    function Connect()
    {
        $this->connection = new mysqli(DBSettings::getHost(), DBSettings::getLogin(), DBSettings::getPassword(), DBSettings::getBase());
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

    function Disconnect()
    {
        $this->connection->close();
    }

    private function clearData()
    {
        $this->data = NULL;
    }

    private function setData($_data)
    {
        $this->clearData();
        if($_data != NULL)
        {
            $this->data = $_data;
        }
    }

    function getData()
    {
        if($this->data != NULL)
        {
            return $this->data;
        }
    }

    // -------------------------------- Check block ----------------------------------- //

    function checkUser($username)
    {
        $username = trim($username);
        if(!validateUser($username))
        {
            return ERRORS::LOGIN_BAD_FORMAT;
        }
        $query = "SELECT userID FROM users WHERE userName='".$this->connection->real_escape_string($username)."'";
        $result = $this->connection->query($query);
        if($result->num_rows)
        {
            return ERRORS::LOGIN_ALREADY_USED;
        }
        else
        {
            return ERRORS::NO_ERROR;
        }
    }

    function checkMail($email)
    {
        $email = trim($email);
        if(!validateMail($email))
        {
            return ERRORS::EMAIL_BAD_FORMAT;
        }
        $query = "SELECT userID FROM users WHERE email='".$this->connection->real_escape_string($email)."'";
        $result = $this->connection->query($query);
        if($result->num_rows)
        {
            return ERRORS::EMAIL_ALREADY_USED;
        }

        $query = "SELECT activationID FROM regactivations WHERE email='".$this->connection->real_escape_string($email)."'";
        $result = $this->connection->query($query);
        if($result->num_rows)
        {
            return ERRORS::EMAIL_ALREADY_USED_REG;
        }
        return ERRORS::NO_ERROR;
    }

    // ------------------------------ Register block ---------------------------------- //

    function submitActivationRequest($email, $password)
    {
        $res = $this->checkMail($email);
        if($res != 0)
            return $res;

        $key = generateActivationKey();
        $email = $this->connection->real_escape_string($email);
        $password = generatePassword($email, $password);

        $query = "INSERT INTO regactivations(activationID, activationKey, email, password, registerTime, activated)".
                 " VALUES (NULL,'".$key."','".$email."','".$password."', FROM_UNIXTIME('".time()."'), '0')";
        $this->connection->query($query);

        if($this->connection->errno)
            return ERRORS::ACTIVATION_REQUEST_MYSQL_ERROR;
        return ERRORS::NO_ERROR;
    }

    function checkActivationLink($email, $key)
    {
        $key = $this->connection->real_escape_string($key);
        $email = $this->connection->real_escape_string($email);

        $query = "SELECT activated FROM regactivations WHERE activationKey='".$key."' AND email='".$email."'";
        $result = $this->connection->query($query);

        if($this->connection->errno)
            return ERRORS::ACTIVATION_LINK_MYSQL_ERROR;

        if($result->num_rows)
        {
            $row = $result->fetch_assoc();
            if($row['activated'])
            {
                return ERRORS::ACTIVATION_LINK_ACTIVATED;
            }
            else
            {
                return ERRORS::NO_ERROR;
            }
        }
        else
        {
            return ERRORS::ACTIVATION_LINK_NOT_FOUND;
        }
    }

    function confirmActivation($email, $key, $login)
    {
        $key = $this->connection->real_escape_string($key);
        $email = $this->connection->real_escape_string($email);
        $login = $this->connection->real_escape_string($login);

        $query = "SELECT activationID, activated, password FROM regactivations WHERE activationKey='".$key."' AND email='".$email."'";
        $result = $this->connection->query($query);

        if($this->connection->errno)
            return ERRORS::ACTIVATION_CONFIRM_MYSQL_ERROR;

        if($result->num_rows)
        {
            $row = $result->fetch_assoc();
            if(!$row['activated'])
            {
                $activId = $row['activationID'];
                $password = $row['password'];

                $query = "INSERT INTO users (userID, userName, email, password, cityID, registerDate, vk_placeholder)".
                         " VALUES (NULL, '".$login."', '".$email."', '".$password."', NULL, DATE(FROM_UNIXTIME('".time()."')), NULL)";

                $result = $this->connection->query($query);
                if($this->connection->errno)
                {
                    return ERRORS::ACTIVATION_CONFIRM_MYSQL_ERROR;
                }

                $query = "UPDATE regactivations SET activated = '1' WHERE activationID = '".$activId."'";
                $result = $this->connection->query($query);
                if($this->connection->errno)
                {
                    return ERRORS::ACTIVATION_CONFIRM_MYSQL_ERROR;
                }
            }
            else
            {
                return ERRORS::ACTIVATION_LINK_ACTIVATED;
            }
        }
        else
        {
            return ERRORS::ACTIVATION_LINK_NOT_FOUND;
        }
        return ERRORS::NO_ERROR;
    }

    // --------------------------------------- Login block --------------------------------------

    function loginUser($login, $password)
    {
        // $login - email or password
        $login = $this->connection->real_escape_string(trim($login));
        $password = $this->connection->real_escape_string(trim($password));

        $query = "SELECT userID, userName, email, password FROM users WHERE email='".$login."' OR userName='".$login."'";
        $result = $this->connection->query($query);

        if($this->connection->errno)
            return ERRORS::LOGIN_MYSQL_ERROR;

        if($result->num_rows)
        {
            $row = $result->fetch_assoc();
            $email = $row['email'];
            $passHash = generatePassword($email, $password);
            if($passHash == $row['password'])
            {
                $sessionKey = "";
                for($i = 0; $i < 5; $i++)
                {
                    $sessionKey = generateSession($email, $row['userName']);
                    $query = "SELECT sessionID FROM sessions WHERE sessionKey='".$sessionKey."'";
                    $result = $this->connection->query($query);
                    if(!$this->connection->errno)
                        break;
                }

                $ts = time();
                $query = "INSERT INTO sessions (sessionID, userID, createdTime, lastLogin, sessionKey, closed) ".
                         "VALUES (NULL, '".$row['userID']."', FROM_UNIXTIME('".$ts."'), FROM_UNIXTIME('".$ts."'), '".$sessionKey."', '0')";

                $result = $this->connection->query($query);
                $this->setData($sessionKey);
                if($this->connection->errno)
                    return ERRORS::LOGIN_MYSQL_ERROR;
                return ERRORS::NO_ERROR;
            }
        }
        return ERRORS::BAD_USERNAME_OR_PASSWORD;
    }

    function logout($token)
    {
        $token = $this->connection->real_escape_string($token);
        $query = "SELECT sessionID FROM sessions WHERE sessionKey='".$token."' AND closed='0'";
        $result = $this->connection->query($query);
        echo $query;
        if($this->connection->errno)
            return ERRORS::CHECK_TOKEN_MYSQL_ERROR;

        if($result->num_rows)
        {
            $row = $result->fetch_assoc();
            $query = "UPDATE sessions SET closed='1' WHERE sessionID='".$row['sessionID']."'";
            $result = $this->connection->query($query);
            echo $query;
            if($this->connection->errno)
                return ERRORS::CHECK_TOKEN_MYSQL_ERROR;
        }
        return ERRORS::NO_ERROR;
    }

    // ---------------------

    function checkToken($userID, $token)
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
            if(($ts - $row['createdTime'] > ServerSettings::getSessionLiveTime()) || $row['closed'])
            {
                return ERRORS::SESSION_EXPIRED_ERROR;
            }
            else
            {
                $query = "UPDATE sessions SET lastLogin=FROM_UNIXTIME('".$ts."') WHERE sessionID='".$row['sessionID']."'";
                $result = $this->connection->query($query);
                if($this->connection->errno)
                    return ERRORS::CHECK_TOKEN_MYSQL_ERROR;
                return ERRORS::NO_ERROR;
            }
        }
        return ERRORS::BAD_TOKEN_ERROR;
    }

    // ---------------------

    // --------------------------------------- Comment block --------------------------------------

    function postComment($marketID, $userID, $mark, $text, $token)
    {
        $res = $this->checkToken($userID, $token);
        $marketID = $this->connection->real_escape_string($marketID);
        $userID = $this->connection->real_escape_string($userID);
        $mark = $this->connection->real_escape_string($mark);
        $text = $this->connection->real_escape_string($text);
        if($res == ERRORS::NO_ERROR)
        {
            $ts = time();
            if(($mark > 0) && ($mark <= ServerSettings::getMaxMarketMark()))
            {
                if(strlen(($text) >= ServerSettings::getMinCommentLength()) && (strlen($text) <= ServerSettings::getMaxCommentLength()))
                {
                    $query = "INSERT INTO comments (commentID, marketID, userID, commentTime, text, mark, photos, approved, thumbsUp, thumbsDown) ".
                         "VALUES (NULL, '".$marketID."', '".$userID."', FROM_UNIXTIME('".$ts."'), '".$text."', '".$mark."', NULL, '0', '0', '0')";
                    $result = $this->connection->query($query);
                    echo $query;
                    if($this->connection->errno)
                        return ERRORS::POST_COMMENT_MYSQL_ERROR;
                    return ERRORS::NO_ERROR;
                }
                else
                {
                    return ERRORS::BAD_COMMENT_LENGTH;
                }
            }
            else
            {
                return ERRORS::BAD_MARKET_MARK;
            }
        }
        else
        {
            return $res;
        }
    }

    function getCommentsByMarketID($marketID, $start, $count)
    {
        $marketID = $this->connection->real_escape_string($marketID);
        $start = $this->connection->real_escape_string($start);
        $count = $this->connection->real_escape_string($count);

        $query = "SELECT * FROM comments WHERE marketID='".$marketID."' LIMIT ".$start.", ".$count."";
        $result = $this->connection->query($query);
        if($this->connection->errno)
            return ERRORS::GET_COMMENTS_MYSQL_ERROR;

        if($result->num_rows)
        {
            $comments = array();
            for($i = 0; $i < $result->num_rows; $i++)
            {
                $row = $result->fetch_assoc();
                $comment = new Comment($row['commentID'], $row['marketID'], $row['userID'],
                                       $row['commentTime'], utf8_decode($row['text']), $row['mark'],
                                       $row['photos'], $row['approved']);
                $comments[$i] = $comment;
            }
            print_r($comments);
            $this->setData($comments);
            return ERRORS::NO_ERROR;
        }
        return ERRORS::NO_ERROR;
    }


    //----------------------
}

?>