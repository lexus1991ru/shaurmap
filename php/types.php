<?php

class MyError
{
    private $message;

    function __construct($msg)
    {
        $this->message = $msg;
    }

    function getMessage()
    {
        return $this->message;
    }
}

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

class Comment
{
    public $commentID;
    public $marketID;
    public $userID;
    public $commentTime;
    public $text;
    public $mark;
    public $photos;
    public $approved;

    function __construct($_commentID, $_marketID, $_userID, $_commentTime, $_text,
                         $_mark, $_photos, $_approved)
    {
        $this->commentID =   $_commentID;
        $this->marketID =    $_marketID;
        $this->userID =      $_userID;
        $this->commentTime = $_commentTime;
        $this->text =        $_text;
        $this->mark =        $_mark;
        $this->photos =      $_photos;
        $this->approved =    $_approved;
    }

    function getMarketDesc()
    {
        return array("commentID" => $this->commentID, "marketID" => $this->marketID,
                      "userID" => $this->userID, "commentTime" => $this->commentTime,
                      "text" => $this->text, "mark" => $this->mark,
                      "photos" => $this->photos, "approved" => $this->approved);
    }

}
?>