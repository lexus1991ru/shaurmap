<?php

class WrapperDB
{
    private $connection = false;

    function Connect()
    {
        $this->connection = new mysqli('localhost', 'root', 'Qq12345', 'shaurmap');
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

    function ()
    {

    }
}

$a = new WrapperDB();
$a->Connect();


$a->Disconnect();

?>