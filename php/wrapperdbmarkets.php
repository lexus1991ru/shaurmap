<?php

require_once("wrapperdbbase.php");
require_once("common.php");
require_once("types.php");

interface IWrapperDBMarkets
{
    public function getMarketsByCity($cityID, $start, $count);
    public function getMarketsByLocation($x, $y, $w, $h, $start, $count);
    public function getMarketsByUser($userID, $start, $count);
    public function getMarketByID($marketID);
    public function getMarketDesc($marketID);
    public function addMarket($marketName, $cityID, $latitude, $longitude, $marketPhoto,
                              $marketText, $addedBy, $addedDate, $pig, $chicken, $gloves,
                              $lemonade, $cigarettes, $beer, $coffee, $token, $userID);
    public function editMarketDesc($marketID, $marketPhoto, $marketText, $pig,
                                   $chicken, $gloves, $lemonade, $cigarettes, $beer,
                                   $coffee, $token, $userID);
    public function editMarket($marketID, $marketName, $cityID, $latitude, $longitude, $closed, $token, $userID);
    public function deleteMarket($marketID, $token, $userID);
}

class WrapperDBMarkets extends WrapperDBBase implements IWrapperDBMarkets
{
    public function __construct()
    {
        parent::__construct();
    }

    public function __destruct()
    {
        parent::__destruct();
    }

    private function fetchMarketsFromRequest($result)
    {
        if($result->num_rows)
        {
            $markets = array();
            for($i = 0; $i < $result->num_rows; $i++)
            {
                $row = $result->fetch_assoc();
                $market = new Market($row['marketID'], $row['marketName'], $row['cityID'],
                                     $row['latitude'], $row['longitude'], $row['addedDate'],
                                     $row['closedDate']);
                $market->printMarket();
                $markets[$i] = $market;
            }
            $this->setData($markets);
        }
        return ERRORS::NO_ERROR;
    }

    public function getMarketsByCity($cityID, $start, $count)
    {
        $cityID = $this->connection->real_escape_string($cityID);
        $start = $this->connection->real_escape_string($start);
        $count = $this->connection->real_escape_string($count);

        $query = "SELECT * FROM markets WHERE cityID='".$cityID."' LIMIT ".$start.", ".$count;
        $result = $this->connection->query($query);

        if($this->connection->errno)
            return ERRORS::GET_MARKETS_MYSQL_ERROR;
        return $this->fetchMarketsFromRequest($result);
    }

    public function getMarketsByLocation($x, $y, $w, $h, $start, $count)
    {
        $x = $this->connection->real_escape_string($x);
        $y = $this->connection->real_escape_string($y);
        $w = $this->connection->real_escape_string($w);
        $h = $this->connection->real_escape_string($h);
        $start = $this->connection->real_escape_string($start);
        $count = $this->connection->real_escape_string($count);

        $query = "SELECT * FROM marketdesc WHERE ((latitude < '".$y."') AND (latitude > ('".$y."' - '".$h."')) AND (longitude > '".$x."') AND (lon1itude < ('".$x."' + '".$w."'))) LIMIT 0,".$count;
        $result = $this->connection->query($query);

        if($this->connection->errno)
            return ERRORS::GET_MARKETS_MYSQL_ERROR;

        return $this->fetchMarketsFromRequest($result);
    }

    public function getMarketsByUser($userID, $start, $count)
    {
        $userID = $this->connection->real_escape_string($userID);
        $start = $this->connection->real_escape_string($start);
        $count = $this->connection->real_escape_string($count);

        $query = "SELECT * FROM markets WHERE userID='".$userID."' LIMIT ".$start.", ".$count;
        $result = $this->connection->query($query);

        if($this->connection->errno)
            return ERRORS::GET_MARKETS_MYSQL_ERROR;
        return $this->fetchMarketsFromRequest($result);
    }

   public function getMarketByID($marketID)
   {
        $marketID = $this->connection->real_escape_string($marketID);

        $query = "SELECT * FROM markets WHERE marketID='".$marketID."'";
        $result = $this->connection->query($query);

        if($this->connection->errno)
            return ERRORS::GET_MARKETS_MYSQL_ERROR;
        return $this->fetchMarketsFromRequest($result);
   }

   public function getMarketDesc($marketID)
   {
        $marketID = $this->connection->real_escape_string($marketID);

        $query = "SELECT * FROM marketdesc WHERE marketID='".$marketID."'";
        $result = $this->connection->query($query);

        if($this->connection->errno)
            return ERRORS::GET_MARKET_DESC_MYSQL_ERROR;

        if($result->num_rows)
        {
            $row = $result->fetch_assoc();
            $marketDesc = new MarketDesc($row['marketID'], $row['addedDate'], $row['marketText'],
                                     $row['marketPhoto'], $row['addedBy'], $row['pig'],
                                     $row['chicken'], $row['gloves'], $row['lemonade'],
                                     $row['cigarettes'], $row['beer'], $row['coffee']);
            $this->setData($marketDesc);
            EERORS::NO_ERROR;
        }
        return ERRORS::MARKET_DESC_NOT_FOUND;
   }

   public function addMarket($marketName, $cityID, $latitude, $longitude, $marketPhoto,
                              $marketText, $addedBy, $addedDate, $pig, $chicken, $gloves,
                              $lemonade, $cigarettes, $beer, $coffee, $token, $userID)
   {
        // whould not work
        $marketName = $this->connection->real_escape_string($marketName);
        $cityID = $this->connection->real_escape_string($cityID);
        $latitude = $this->connection->real_escape_string($latitude);
        $longitude = $this->connection->real_escape_string($longitude);
        $marketPhoto = $this->connection->real_escape_string($marketPhoto);
        $marketText = $this->connection->real_escape_string($marketText);
        $addedBy = $this->connection->real_escape_string($addedBy);
        $addedDate = $this->connection->real_escape_string($addedDate);
        $pig = $this->connection->real_escape_string($pig);
        $chicken = $this->connection->real_escape_string($chicken);
        $token = $this->connection->real_escape_string($token);
        $userID = $this->connection->real_escape_string($userID);
        $gloves = undefinedToBool($gloves);
        $lemonade = undefinedToBool($lemonade);
        $cigarettes = undefinedToBool($cigarettes);
        $beer = undefinedToBool($beer);
        $coffee = undefinedToBool($coffee);
        // TODO: Check values of parameters

        $query = "SHOW TABLE STATUS LIKE markets";
        $result = $this->connection->query($query);
        $row = $result->fetch_assoc();
        $nextID = $row['Auto_increment'];

        $query = "INSERT INTO markets(marketID, marketName, cityID, latitude, longitude, closedDate)".
                 " VALUES ('".$nextID."', '".$marketName."', '".$cityID."', '".$latitude."', '".$longitude."', 'NULL')";
        $result = $this->connection->query($query);
        if($this->connection->errno)
            return ERRORS::ADD_MARKET_MYSQL_ERROR;
        $query = "INSERT INTO marketdesc('".$nextID."', marketText, marketPhoto, addedBy, addedDate, ".
                 "pig, chicken, gloves, lemonade, cigarettes, beer, coffee) VALUES ('".$nextID ."', '".$marketText."', '".
                 $marketPhoto."', ".$userID."', DATE(FROM_UNIXTIME('".time()."')), '".$pig."','".$chicken."','".
                 $gloves."','".$lemonade."','".$cigarettes."'],'".$beer."','".$coffee."')";
        $result = $this->connection->query($query);
        if($this->connection->errno)
            return ERRORS::ADD_MARKET_MYSQL_ERROR;
        return ERRORS::NO_ERROR;
   }

   public function editMarket($marketID, $marketName, $cityID, $latitude, $longitude, $closed, $token, $userID)
   {
        $marketID = $this->connection->real_escape_string($marketID);
        $marketName = $this->connection->real_escape_string($marketName);
        $cityID = $this->connection->real_escape_string($cityID);
        $latitude = $this->connection->real_escape_string($latitude);
        $longitude = $this->connection->real_escape_string($longitude);
        $closed = $this->connection->real_escape_string($closed);
        $token = $this->connection->real_escape_string($token);
        $userID = $this->connection->real_escape_string($userID);

        $query = "SET INTO markets(marketID, marketName, cityID, latitude, longitude, closedDate)".
                 " VALUES ('".$marketID."', '".$marketName."', '".$cityID."', '".$latitude."', '".$longitude."', 'NULL')";
        $result = $this->connection->query($query);
        if($this->connection->errno)
            return ERRORS::EDIT_MARKET_MYSQL_ERROR;

        $res = $this->checkToken($userID, $token);
        if($res == ERRORS::NO_ERROR)
        {
            $userRights = $this->getUserRights($userID);
            if(Validator::isAdmin($userRights) || Validator::isModerator($userRights))
            {
                $query = "UPDATE markets SET marketName='".$marketName."', cityID='".$cityID."', latitude='".
                         $latitude."', longitude='".$longitude."', closedDate=DATE(FROM_UNIXTIME('".$closedDate."'))".
                         "' WHERE commentID='".$commentID."'";
                $result = $this->connection->query($query);
                if($this->connection->errno)
                    return ERRORS::DELETE_COMMENT_MYSQL_ERROR;
                return ERRORS::NO_ERROR;
            }
            else
            {
                return ERRORS::PERMISSION_DENIED;
            }
        }
        else
        {
            return $res;
        }

   }
   public function deleteMarket($marketID, $token, $userID)
   {
        $marketID = $this->connection->real_escape_string($marketID);
        $res = $this->checkToken($userID, $token);
        if($res == ERRORS::NO_ERROR)
        {
            $userRights = $this->getUserRights($userID);
            if(Validator::isAdmin($userRights) || Validator::isModerator($userRights))
            {
                $query = "DELETE FROM markets WHERE marketID='".$marketID."'";
                $result = $this->connection->query($query);
                if($this->connection->errno)
                    return ERRORS::DELETE_MARKET_MYSQL_ERROR;
                return ERRORS::NO_ERROR;
            }
            else
            {
                return ERRORS::PERMISSION_DENIED;
            }
        }
        else
        {
            return $res;
        }
   }

    public function editMarketDesc($marketID, $marketPhoto, $marketText, $pig,
                                   $chicken, $gloves, $lemonade, $cigarettes, $beer,
                                   $coffee, $token, $userID)
    {

    }
}

class Market
{
    public $id;
    public $name;
    public $cityID;
    public $latitude;
    public $longitude;
    public $addedDate;
    public $closedDate;

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
}

class MarketDesc
{
    public $id;
    public $text;
    public $photo;
    public $addedBy;
    public $addedDate;
    public $pig;
    public $chicken;
    public $gloves;
    public $lemonade;
    public $cigarettes;
    public $beer;
    public $coffee;

    function __construct($_id, $_addedDate, $_text, $_photo, $_addedBy, $_pig,
                         $_chicken, $_gloves, $_lemonade, $_cigarettes, $_beer, $_coffee)
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
        $this->coffee =     $_coffee;
    }
}

?>