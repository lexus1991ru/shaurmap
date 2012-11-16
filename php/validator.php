<?php
require_once("settings.php");

class Validator
{
    static public function validateMail($email)
    {
        $email = trim($email);
        $pattern = "|^[_A-Za-z0-9-\\+]+(\\.[_A-Za-z0-9-]+)*@[A-Za-z0-9-]+(\\.[A-Za-z0-9]+)*(\\.[A-Za-z]{2,})$|";
        if (preg_match($pattern, $email)){
            return true;
        }
        return false;
    }

    static public function validateUser($username)
    {
        // TODO: use approved symbols for checking username
        $username = trim($username);
        if ((strlen($username) < ServerSettings::getMinUsernameLenght()) || (strlen($username) > ServerSettings::getMaxUsernameLength()))
        {
            return false;
        }
        return true;
    }

    static public function validatePass($pass)
    {
        $pass = trim($pass);
        if ((strlen($pass) < ServerSettings::getMinPasswordLenght()) || (strlen($pass) > ServerSettings::getMaxPasswordLenght()))
        {
            return false;
        }
        return true;
    }

    static public function validateCommentLength($text)
    {
        if ((strlen($text) >= ServerSettings::minCommentLength()) && (strlen($text) <= ServerSettings::maxCommentLength()))
        {
            return true;
        }
        return false;
    }

    static public function isAdmin($userRights)
    {
        if ($userRights == 1)
        {
            return true;
        }
        return false;
    }

    static public function isModerator($userRights)
    {
        if ($userRights == 2)
        {
            return true;
        }
        return false;
    }

    static public function validateMark($mark)
    {
        if (($mark > 0) && ($mark <= ServerSettings::maxMarketMark()))
        {
            return true;
        }
        return false;
    }

    static public function validateToken($token)
    {
        if (strlen($token) == ServerSettings::getTokenLength())
        {
            return true;
        }
        return false;
    }

    static  public function validateUserId($userID)
    {
        if (strlen($userID) > 0)
        {
            return true;
        }
        return false;
    }

    static public function validateTokenDateCreate($passedTime)
    {
        if ($passedTime <= ServerSettings::getSessionLiveTime())
        {
            return true;
        }
        return false;
    }

    static public function validateTokenClosed($closed)
    {
       if ($closed)
       {
           return false;
       }
        return true;
    }
}

?>