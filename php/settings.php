<?php
class DBSettings {
    private static $host = "localhost";
    private static $login = "root";
    private static $password = "Qq12345";
    private static $base = "shaurmap";

    function getHost()
    {
        return self::$host;
    }

    function getLogin()
    {
        return self::$login;
    }

    function getPassword()
    {
        return self::$password;
    }

    function getBase()
    {
        return self::$base;
    }
}

class ServerSettings
{
    public static $showDebugInfo = true;
    private static $approvedUsernameSymbols = "abcdefghijklmnopqrstuvwxyz0123456789_";
    private static $maxUsernameLength = 32;
    private static $minUsernameLenght = 3;
    private static $maxPasswordLenght = 32;
    private static $minPasswordLenght = 6;
    private static $sessionLiveTime = 2592000;// 60*60*24*30 = 30 days
    private static $maxMarketMark = 5;
    private static $maxCommentLength = 2000;
    private static $minCommentLength = 0;
    private static $maxCommentsForMarket = 10;


    function getSessionLiveTime()
    {
        return self::$sessionLiveTime;
    }

    function getApprovedUsernameSymbols()
    {
        return self::$approvedUsernameSymbols;
    }

    function getMaxUsernameLength()
    {
        return self::$maxUsernameLength;
    }

    function getMinUsernameLenght()
    {
        return self::$minUsernameLenght;
    }

    function getMaxPasswordLenght()
    {
        return self::$maxPasswordLenght;
    }

    function getMinPasswordLenght()
    {
        return self::$minPasswordLenght;
    }

    function getMaxMarketMark()
    {
        return self::$maxMarketMark;
    }

    function getMinCommentLength()
    {
        return self::$minCommentLength;
    }

    function getMaxCommentLength()
    {
        return self::$maxCommentLength;
    }

    function getmaxCommentsForMarket()
    {
        return self::$maxCommentsForMarket;
    }
}

?>