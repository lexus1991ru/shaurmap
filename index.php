<?php

class WrapperDB
{
    private $connection = false;

    function Connect()
    {
        $db = mysql_connect('localhost', 'root', 'Qq12345', 'shaurmap');
        if(!$db)
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
      if($connection)
      {
        $connection->close();
        echo "Connection closed!";
      }
      else
      {
        echo "No connection to disconnect";
      }
    }
}

$a = new WrapperDB();
$a->Connect();
$a->Disconnect();

?>