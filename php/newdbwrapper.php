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
            return true;
        }
    }

    function Disconnect()
    {
        $this->connection->close();
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
        // TODO: Input parameters protection
        // $login - email or password
        $login = $this->connection->real_escape_string(trim($login));
        $password = $this->connection->real_escape_string(trim($password));

        $passHash = generatePassword($login, $password);

        $query = "SELECT * FROM users WHERE email='".$login."' AND password='".$passHash."'";
        echo $query;
        $result = $this->connection->query($query);
        if($result->num_rows)
        {
            echo "Right login pass<br />";
            return 100;
        }
        else
        {
            echo "Bad username/email or password!<br />";
            return 200;
        }
    }

}

?>