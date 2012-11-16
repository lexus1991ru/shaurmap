<?php

require_once("wrapperdbbase.php");
require_once("common.php");

interface IWrapperDBRegister
{
    public function checkUser($username);
    public function checkMail($email);
    public function submitActivationRequest($email, $password1, $password2);
    public function confirmActivation($email, $key, $login);
}

class WrapperDBRegister extends WrapperDBBase implements IWrapperDBRegister
{
    public function __construct()
    {
        parent::__construct();
    }

    public function __destruct()
    {
        parent::__destruct();
    }

    public function checkUser($username)
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

    public function checkMail($email)
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

    public function submitActivationRequest($email, $password1, $password2)
    {
        if (md5($password1) == md5($password2)) {
            if(validatePass($password1)){
                $res = $this->checkMail($email);
                if($res != 0)
                    return $res;

                $key = generateActivationKey();
                $email = $this->connection->real_escape_string($email);
                $password1 = generatePassword($email, $password1);

                $query = "INSERT INTO regactivations(activationID, activationKey, email, password, registerTime, activated)".
                         " VALUES (NULL,'".$key."','".$email."','".$password1."', FROM_UNIXTIME('".time()."'), '0')";
                $this->connection->query($query);

                if($this->connection->errno)
                    return ERRORS::ACTIVATION_REQUEST_MYSQL_ERROR;
                return ERRORS::NO_ERROR;
            }
            else
            {
                return ERRORS::BAD_PASSWORD_FORMAT;
            }
        }
        else
        {
            return ERRORS::PASSWORDS_NOT_EQUAL;
        }
    }

    private function checkActivationLink($email, $key)
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

    public function confirmActivation($email, $key, $login)
    {
        $key = $this->connection->real_escape_string($key);
        $email = $this->connection->real_escape_string($email);
        $login = $this->connection->real_escape_string($login);

        $res = $this->checkActivationLink($email, $key);
        if($res == ERRORS::NO_ERROR)
        {
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
        else
        {
            return $res;
        }
    }
}

?>