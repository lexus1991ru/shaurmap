<?php

require_once("settings.php");

class Validator
{
    public static function validateMail($email)
    {
        $email = trim($email);
        if(strlen($email) <= ServerSettings::maxEmailLength)
        {
            $pattern = ServerSettings::emailRegExp;
            if (preg_match($pattern, $email))
                return true;
        }
        return false;
    }

    public static function validateUser($username)
    {
        $username = trim($username);
        if(strlen($username) >= ServerSettings::minUsernameLength && strlen($username) <= ServerSettings::maxUsernameLength)
        {
            $pattern = ServerSettings::loginRegExp;
            if (preg_match($pattern, $username))
                return true;
        }
        return false;
    }

    public static function validatePass($pass)
    {
        $pass = trim($pass);
        if(strlen($pass) >= ServerSettings::minPasswordLength && strlen($pass) <= ServerSettings::maxPasswordLength)
        {
                return true;
        }
        return false;
    }

    public static function validateCommentLength($text)
    {
        if ((strlen($text) >= ServerSettings::minCommentLength) && (strlen($text) <= ServerSettings::maxCommentLength))
        {
            return true;
        }
        return false;
    }

    public static function validateMark($mark)
    {
        if (($mark > 0) && ($mark <= ServerSettings::maxMarketMark))
        {
            return true;
        }
        return false;
    }

    public static function validateToken($token)
    {
        if (strlen($token) == ServerSettings::tokenLength)
        {
            return true;
        }
        return false;
    }

    public static function validateUserId($userID)
    {
        if (strlen($userID) > 0)
        {
            return true;
        }
        return false;
    }

    public static function validateTokenIsAlive($passedTime)
    {
        if ($passedTime <= ServerSettings::sessionLiveTime)
        {
            return true;
        }
        return false;
    }

    public static function validateTokenIsClosed($closed)
    {
        if ($closed)
        {
           return true;
        }
        return false;
    }
}

?>