<?php
require_once("settings.php");

class Validator
{
    public function validateMail($email)
    {
        $email = trim($email);
        $pattern = "|^[_A-Za-z0-9-\\+]+(\\.[_A-Za-z0-9-]+)*@[A-Za-z0-9-]+(\\.[A-Za-z0-9]+)*(\\.[A-Za-z]{2,})$|";
        if (preg_match($pattern, $email)){
            return true;
        }
        return false;
    }

    public function validateUser($username)
    {
        // TODO: use approved symbols for checking username
        $username = trim($username);
        if ((strlen($username) < ServerSettings::getMinUsernameLenght()) || (strlen($username) > ServerSettings::getMaxUsernameLength()))
        {
            return false;
        }
        return true;
    }

    public function validatePass($pass)
    {
        $pass = trim($pass);
        if ((strlen($pass) < ServerSettings::getMinPasswordLenght()) || (strlen($pass) > ServerSettings::getMaxPasswordLenght()))
        {
            return false;
        }
        return true;
    }
}

?>