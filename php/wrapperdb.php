<?php

require_once("types.php");
require_once("settings.php");

class WrapperDB
{
    private $connection = NULL;
    private $settings = NULL;

    function __construct()
    {
        $this->settings = new DBSettings();
        if(!$this->Connect())
        {
            die("Connection failed!");
        }
    }

    function Connect()
    {
        $this->connection = new mysqli($this->settings->getHost(), $this->settings->getLogin(), $this->settings->getPassword(), $this->settings->getBase());
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

    function checkUser($username)
    {
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
        $query = "SELECT userID FROM users WHERE email='".$this->connection->real_escape_string($email)."'";
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

    function getMarketsFromLocation($x, $y, $w, $h)
    {
        $query = "SELECT * FROM marketdesc WHERE ((latitude < '".$y."') AND (latitude > ('".$y."' - '".$h."')) AND (longitude > '".$x."') AND (lon1itude < ('".$x."' + '".$w."')))";
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

    function getCommentsByUserId($userID)
    {
        $query = "SELECT * FROM comments WHERE userID='".$userID."'";
        $result = $this->connection->query($query);
        $comments = array();
        if($result->num_rows)
        {
            for($i = 0; $i < $result->num_rows; $i++)
            {
                $row = $result->fetch_assoc();
                $comment = new Comment($row['commentID'], $row['marketID'], $row['userID'],
                                       $row['commentTime'], $row['text'], $row['mark'],
                                       $row['photos'], $row['approved']);
                $comments[$i] = $comment;
            }
        }
        return $comments;
    }

    function getCommentsByMarketId($marketID)
    {
      $query = "SELECT * FROM comments WHERE marketID='".$marketID."'";
      $result = $this->connection->query($query);
      $comments = array();
      if($result->num_rows)
      {
          for($i = 0; $i < $result->num_rows; $i++)
          {
              $row = $result->fetch_assoc();
              $comment = new Comment($row['commentID'], $row['marketID'], $row['userID'],
                                     $row['commentTime'], $row['text'], $row['mark'],
                                     $row['photos'], $row['approved']);
                $comments[$i] = $comment;
            }
        }
        return $comments;
    }

}

?>