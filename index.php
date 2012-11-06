<?php

class City
{
    private $id;
    private $name;
    private $latitude;
    private $longitude;
    function __construct($_id, $_name, $_latitude, $_longitude)
    {
        $this->id = $_id;
        $this->name = $_name;
        $this->latitude = $_latitude;
        $this->longitude = $_longitude;
    }

    function getCity()
    {
        return array("id"=>$this->id, "name"=>$this->name, "latitude"=>$this->latitude, "longitude"=>$this->longitude);
    }

    function printCity()
    {
        printf("[%s] - %s (%f, %f)", $this->id, $this->name, $this->latitude, $this->longitude);
    }
}

class Market
{
    private $id;
    private $name;
    private $cityID;
    private $latitude;
    private $longitude;
    private $addedDate;
    private $closedDate;

    function __construct($_id, $_name, $_cityID, $_latitude, $_longitude, $_added, $_closed)
    {
        $this->id = $_id;
        $this->name = $_name;
        $this->cityID = $_cityID;
        $this->latitude = $_latitude;
        $this->longitude = $_longitude;
        $this->addedDate = $_added;
        $this->closedDate = $_closed;
    }

    function getMarket()
    {
        return array("id" => $this->id, "name" => $this->name, "cityID" => $this->cityID,
                     "latitude" => $this->latitude, "longitude" => $this->longitude,
                     "addedDate" => $this->addedDate, "closedDate" => $this->closedDate);
    }

    function printMarket()
    {
        printf("[%s] - %s (%f, %f)", $this->id, $this->name, $this->latitude, $this->longitude);
    }
}

class MarketDesc
{
    private $id;
    private $text;
    private $photo;
    private $addedBy;
    private $addedDate;
    private $pig;
    private $chicken;
    private $gloves;
    private $lemonade;
    private $cigarettes;
    private $beer;


    function __construct($_id, $_addedDate, $_text, $_photo, $_addedBy, $_pig,
                         $_chicken, $_gloves, $_lemonade, $_cigarettes, $_beer)
    {
        $this->id =         $_id;
        $this->text =       $_text;
        $this->photo =      $_photo;
        $this->addedBy =    $_addedBy;
        $this->addedDate =  $_addedDate;
        $this->pig =        $_pig;
        $this->chicken =    $_chicken;
        $this->gloves =     $_gloves;
        $this->lemonade =   $_lemonade;
        $this->cigarettes = $_cigarettes;
        $this->beer =       $_beer;
    }

    function getMarketDesc()
    {
        return array("id" => $this->id, "text" => $this->text, "photo" => $this->photo,
                     "addedBy" => $this->addedBy, "addedDate" => $this->addedDate,
                     "pig" => $this->pig, "chicken" => $this->chicken, "gloves" => $this->gloves,
                     "lemonade" => $this->lemonade, "cigarettes" => $this->cigarettes,
                     "beer" => $this->beer);
    }
}

class WrapperDB
{
    private $connection = NULL;

    function Connect()
    {
        $this->connection = new mysqli('localhost', 'root', '', 'shaurmap');
        if($this->connection->connect_errno)
        {
            echo "Fuck you!<br />";
        }
        else
        {
            echo "Connected!<br />";
        }
    }

    function Disconnect()
    {
        $this->connection->close();
        echo "Connection closed!<br />";
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

    function checkUser($username, $password)
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
}

$a = new WrapperDB();

$a->Connect();

//$a->getCities();
//$a->getMarkets();
//$a->checkUser("Admin", "Qq12345");

print_r($a->getMarketDesc(1));

$a->Disconnect();

?>