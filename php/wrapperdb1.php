<?php

require_once("types.php");
require_once("settings.php");
require_once("common.php");

class WrapperDB
{
    private $connection = NULL;
    private $settings = NULL;

    function __construct()
    {
        //$this->settings = new DBSettings();
        if(!$this->Connect())
        {
            die("Connection failed!");
        }
    }

    function __destruct()
    {
        $this->Disconnect();
    }

    function Connect()
    {
        //$this->connection = new mysqli($this->settings->getHost(), $this->settings->getLogin(), $this->settings->getPassword(), $this->settings->getBase());
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

    function getCities()
    {
        $query = "SELECT * FROM cities";
        $result = $this->connection->query($query);
        $cities = array();
        if($result->num_rows)
        {
            for($i = 0; $i < $result->num_rows; $i++)
            {
                $row = $result->fetch_assoc();
                $city = new City($row['cityID'], $row['cityName'], $row['latitude'], $row['longitude']);
                $city->printCity();
                echo "<br />";
                $cities[$i] = $city;
            }
        }
        return $cities;
    }

    function getCityById($id)
    {
        $query = "SELECT * FROM cities WHERE cityID='".$id."'";
        $result = $this->connection->query($query);
        if($result->num_rows)
        {
            $row = $result->fetch_assoc();
            $city = new City($row['cityID'], $row['cityName'], $row['latitude'], $row['longitude']);
            return $city;
        }
        else
        {
            return NULL;
        }
    }

    function loginUser($username, $password)
    {
        // TODO: Input parameters protection
        $query = "SELECT * FROM users WHERE (userName='".$username."' OR email='".$username."') AND password='".$password."'";
        $result = $this->connection->query($query);
        if($result->num_rows)
        {
            echo "Right login pass<br />";
            return true;
        }
        else
        {
            echo "Bad username/email or password!<br />";
            return false;
        }
    }

    function submitActivationRequest($email, $password)
    {
        /*
        @ Return codes:
        @ 0 - Ok
        @ 1 - Bad email
        @ n - MySQL Error
        */

        if(!$this->checkMail($email))
        {
            $key = generateActivationKey();
            $email = $this->connection->real_escape_string($email);
            $password = generatePassword($email, $password);
            $query = "INSERT INTO regactivations(activationID, activationKey, email, password, registerTime, activated)".
                     " VALUES (NULL,'".$key."','".$email."','".$password."', FROM_UNIXTIME('".time()."'), '0')";
            $this->connection->query($query);
            return $this->connection->errno;
        }
        return 1;
    }

    function checkActivationLink($email, $key)
    {
        $key = $this->connection->real_escape_string($key);
        $email = $this->connection->real_escape_string($email);
        $query = "SELECT activated FROM regactivations WHERE activationKey='".$key."' AND email='".$email."'";
        $result = $this->connection->query($query);
        if($result->num_rows)
        {
            $row = $result->fetch_assoc();
            if($row['activated'])
            {
                return false;
            }
        }
        else
        {
            return false;
        }
        return true;
    }

    function confirmActivation($email, $key, $login)
    {
        $key = $this->connection->real_escape_string($key);
        $email = $this->connection->real_escape_string($email);
        $login = $this->connection->real_escape_string($login);
        $query = "SELECT activationID, activated, password FROM regactivations WHERE activationKey='".$key."' AND email='".$email."'";
        $result = $this->connection->query($query);
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
                    return $this->connection->errno;
                }
                $query = "UPDATE regactivations SET activated = '1' WHERE activationID = '".$activId."'";
                $result = $this->connection->query($query);
                if($this->connection->errno)
                {
                    return $this->connection->errno;
                }
            }
        }
        else
        {
            return true;
        }
        return 0;
    }

    function checkUser($username)
    {
        $username = trim($username);
        if(!validateUser($username))
        {
            return false;
        }
        $query = "SELECT userID FROM users WHERE userName='".$this->connection->real_escape_string($username)."'";
        $result = $this->connection->query($query);
        if($result->num_rows)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    function checkMail($email)
    {
        /*
        @ Return codes:
        @ 0 - Email is not used in our database
        @ 1 - Email is already registered
        @ 2 - Email in process of registration
        @ 3 - Bad email format
        */

        $email = trim($email);
        if(!validateMail($email))
        {
            return 3;
        }
        $query = "SELECT userID FROM users WHERE email='".$this->connection->real_escape_string($email)."'";
        $result = $this->connection->query($query);
        if($result->num_rows)
        {
            return 1;
        }

        $query = "SELECT activationID FROM regactivations WHERE email='".$this->connection->real_escape_string($email)."'";
        $result = $this->connection->query($query);
        if($result->num_rows)
        {
            return 2;
        }
        return 0;
    }

    function getMarkets()
    {
        $query = "SELECT * FROM markets";
        $result = $this->connection->query($query);
        $markets = array();
        if($result->num_rows)
        {
            for($i = 0; $i < $result->num_rows; $i++)
            {
                $row = $result->fetch_assoc();
                $market = new Market($row['marketID'], $row['marketName'], $row['cityID'],
                                     $row['latitude'], $row['longitude'], $row['addedDate'],
                                     $row['closedDate']);
                $market->printMarket();
                echo "<br />";
                $markets[$i] = $market;
            }
        }
        return $markets;
    }

    function getMarketsByCity($id)
    {
        $query = "SELECT * FROM markets WHERE cityID='".$id."'";
        $result = $this->connection->query($query);
        $markets = array();
        if($result->num_rows)
        {
            for($i = 0; $i < $result->num_rows; $i++)
            {
                $row = $result->fetch_assoc();
                $market = new Market($row['marketID'], $row['marketName'], $row['cityID'],
                                     $row['latitude'], $row['longitude'], $row['addedDate'],
                                     $row['closedDate']);
                $market->printMarket();
                echo "<br />";
                $markets[$i] = $market;
            }
        }
        return $markets;
    }

    function getMarketById($id)
    {
        $query = "SELECT * FROM markets WHERE marketID='".$id."'";
        $result = $this->connection->query($query);
        if($result->num_rows)
        {
            $row = $result->fetch_assoc();
            $market = new Market($row['marketID'], $row['marketName'], $row['cityID'],
                                 $row['latitude'], $row['longitude'], $row['addedDate'],
                                 $row['closedDate']);
            $market->printMarket();
            return $market;
        }
        else
        {
            return NULL;
        }
    }

    function getMarketDesc($id)
    {
        $query = "SELECT * FROM marketdesc WHERE marketID='".$id."'";
        $result = $this->connection->query($query);
        if($result->num_rows)
        {
            $row = $result->fetch_assoc();
            $marketDesc = new MarketDesc($row['marketID'], $row['addedDate'], $row['marketText'],
                                     $row['marketPhoto'], $row['addedBy'], $row['pig'],
                                     $row['chicken'], $row['gloves'], $row['lemonade'],
                                     $row['cigarettes'], $row['beer']);
            return $marketDesc;
        }
        else
        {
            return NULL;
        }
    }

    function checkComment($commentID, $verdict)
    {
        $query = "";
        if($verdict)
        {
            $query = "UPDATE comments SET approved='1' WHERE commentID='".$commentID."'";
        }
        else
        {
            $query = "UPDATE comments SET approved='0' WHERE commentID='".$commentID."'";
        }
        $this->connection->query($query);
    }

?>