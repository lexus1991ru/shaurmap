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



?>