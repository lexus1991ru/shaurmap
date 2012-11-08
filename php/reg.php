<?php
require_once("wrapperdb.php");

if(isset($_POST))
{
    $requestType = $_POST['req'];

    switch($requestType)
    {
        case "checkuser":
        {
            if(isset($_POST['username']))
            {
                $dbConn = new WrapperDB();
                $res = $dbConn->checkUser($_POST['username']);
                $dbConn->Disconnect();
                echo json_encode($res);
            }
            break;
        }
        case "checkmail":
        {
            if(isset($_POST['mail']))
            {
                $dbConn = new WrapperDB();
                $res = $dbConn->checkMail($_POST['mail']);
                $dbConn->Disconnect();
                echo json_encode($res);
            }
            break;
        }
        default:
            echo "Unknown Request!";
    }
}

?>