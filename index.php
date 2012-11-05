<?php

class WrapperDB
{
    private $connection = false;

    function Connect()
    {
        $this->connection = new mysqli('localhost', 'root', '', 'shaurmap');
        if($this->connection->connect_errno)
        {
            echo "Fuck you!";
        }
        else
        {
            echo "Ok!";
        }
    }

    function Disconnect()
    {
        $this->connection->close();
        echo "Connection closed!";
    }

    function getCities()
    {
        $query = "SELECT * FROM cities";
        $result = $this->connection->query($query);
        if($result)
        {
            foreach($row in )
        }

    }
}

$a = new WrapperDB();

$a->Connect();


$a->Disconnect();

?>